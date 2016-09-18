<?php
  
  function contextualTime($small_ts, $large_ts=false) {
    if(!$large_ts) $large_ts = time();
    $n = $large_ts - $small_ts;
    if($n <= 1) return 'less than 1 second ago';
    if($n < (60)) return $n . ' seconds ago';
    if($n < (60*60)) { $minutes = round($n/60); return 'about ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago'; }
    if($n < (60*60*16)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
    if($n < (time() - strtotime('yesterday'))) return 'yesterday';
    if($n < (60*60*24)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
    if($n < (60*60*24*6.5)) return 'about ' . round($n/(60*60*24)) . ' days ago';
    if($n < (time() - strtotime('last week'))) return 'last week';
    if(round($n/(60*60*24*7))  == 1) return 'about a week ago';
    if($n < (60*60*24*7*3.5)) return 'about ' . round($n/(60*60*24*7)) . ' weeks ago';
    if($n < (time() - strtotime('last month'))) return 'last month';
    if(round($n/(60*60*24*7*4))  == 1) return 'about a month ago';
    if($n < (60*60*24*7*4*11.5)) return 'about ' . round($n/(60*60*24*7*4)) . ' months ago';
    if($n < (time() - strtotime('last year'))) return 'last year';
    if(round($n/(60*60*24*7*52)) == 1) return 'about a year ago';
    if($n >= (60*60*24*7*4*12)) return 'about ' . round($n/(60*60*24*7*52)) . ' years ago'; 
    return false;
  }
  
  session_start();
  
  $user_token = $_GET['token'];
  if ($_GET['token'] == '') {
    if ($_SESSION['token'] == '') {
      //WTF
      session_destroy();
      header('Location: http://sos.joseb.me');
      exit();
    }
    $user_token = $_SESSION['token'];
  } else {
    $_SESSION['token'] = $user_token;
  }
  $selected_id = $_GET['id'];
  
  $ownCall = "http://sos.joseb.me/api/notes";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $ownCall);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "X-SOS-Authentication: $user_token"
    ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec ($ch);
  curl_close($ch);
  
  $apiResponse = json_decode($response);
  
  if ($apiResponse->message == 'Invalid auth token') {
    session_destroy();
    header('Location: http://sos.joseb.me');
    exit();
  }
  
  $createdTime = contextualTime(strtotime($apiResponse[$selected_id]->date));

  $quizlet_id = $apiResponse[$selected_id]->categories[0]->quizlet_id;
  
  $quizlet_base = "https://api.quizlet.com/2.0/sets/$quizlet_id?client_id=y82YhQBend&whitespace=1";
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $quizlet_base);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec ($ch);
  curl_close($ch);

  $quizlet_data = json_decode($response, true);

  $tempArray = [];
  foreach ($quizlet_data['terms'] as $term) {
    $innerArray = ['term' => $term['term'], 'definition' => $term['definition']];
    array_push($tempArray, $innerArray);
  }
  
  $quiz_data = json_encode($tempArray);


  //now we just need the data from our DBs
  
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>Dashboard | Speed of Sound</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css" rel="stylesheet"/>


    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="assets/css/demo.css" rel="stylesheet" />


    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/4.0.1/ekko-lightbox.min.css" rel="stylesheet" type="text/css" />

</head>
<body>

<div class="wrapper">
    <div class="sidebar" data-color="dark" data-image="assets/img/sidebar-5.jpg">
    	<div class="sidebar-wrapper">
            <div class="logo">
                <a href="" class="simple-text">
                    <i class="pe-7s-note2"></i> Latest notes
                </a>
            </div>
            <ul class="nav">
            <?php foreach ($apiResponse as $id => $obj): ?>
            
            <?php if ($selected_id == $id): ?>
                <li class="active">
            <?php else: ?>
                <li>
            <?php endif; ?>
                    <a href="index.php?id=<?=$id;?>">
                        <i class="pe-7s-piggy"></i>
                        <p><?=$obj->title;?> </p>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
    	</div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php echo $apiResponse[$selected_id]->title; ?></a>
                </div>
                <div class="collapse navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#">
                                Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Text Summary</h4>
                                <p class="category">Category: <?=$apiResponse[$selected_id]->categories[0]->name;?></p>
                            </div>
                            <div class="content">
                              <p><?php echo $apiResponse[$selected_id]->summary; ?></p>

                                <div class="footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-clock-o"></i> Scanned <?=$createdTime;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Related Quizlet</h4>
                                <p class="category">Test yourself & ace that exam</p>
                            </div>
                            <div class="content">
                              <div class="row">

                                  <div class="col-md-12">
                                      <div class="card">
                                          <div class="card-image">
                                              <img class="img-responsive" src="http://material-design.storage.googleapis.com/publish/v_2/material_ext_publish/0Bx4BSt6jniD7TDlCYzRROE84YWM/materialdesign_introduction.png">
                                              <span class="card-title" id="quizlet_term">Loading...</span>
                                          </div>
                                          
                                          <div class="card-content">
                                              <p id="quizlet_answer" style="display: none;">A proportionate saving in costs gained by an increased level of production.</p>
                                          </div>
                                          
                                          <div class="card-action">
                                            <center>
                                              <button type="button" class="btn btn-default" id="quizlet_back" disabled="trues"><i class="pe-7s-left-arrow"></i></button>
                                              <button type="button" class="btn btn-success" id="quizlet_reveal">Show Answer</button>
                                              <button type="button" class="btn btn-default" id="quizlet_fwd"><i class="pe-7s-right-arrow"></i></button>
                                            </center>
                                            </div>
                                          </div>
                                      </div>
                                  </div>
                            
                              <div class="footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-history"></i> Powered by Quizlet
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Image uploaded</h4>
                                <p class="category">In case the summary above is not enough</p>
                            </div>
                            <div class="content">
                              <div class="row">

                                  <div class="col-md-12">
                                      <a href="<?php echo $apiResponse[$selected_id]->image_url; ?>" data-toggle="lightbox">
                                        <img src="<?php echo $apiResponse[$selected_id]->image_url; ?>" class="img-responsive">
                                      </a>
                                      </div>
                                  </div>
                            
                              <div class="footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-cloud-upload"></i> Uploaded via iPhone
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
</div>


</body>

    <!--   Core JS Files   -->
  <script src="assets/js/jquery-1.10.2.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/4.0.1/ekko-lightbox.min.js"></script>

	<!--  Checkbox, Radio & Switch Plugins -->
	<script src="assets/js/bootstrap-checkbox-radio-switch.js"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

  <!--  Notifications Plugin    -->
  <script src="assets/js/bootstrap-notify.js"></script>

  <script src="//js.pusher.com/3.1/pusher.min.js"></script>


    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>
  <script type="text/javascript">
  var pusher = new Pusher('278a1e04ebd47969dbd8');
  var channel = pusher.subscribe('SOS-NEW-DATA');
  channel.bind('NEW-FILE', function(data) {
    window.location = "http://sos.joseb.me/dashboard/?id=0";
  });
  var currentQuizletID = <?php echo $quizlet_id; ?>;
  var quizletData = <?php echo $quiz_data; ?>;
  </script>
  <script src="assets/js/quizlet.js"></script>
</html>
