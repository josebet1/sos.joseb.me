
var currentCard = 0;
  
$(document).ready(function(){

  reloadQuizlet();  
  
  $("#quizlet_back").click(function() {
    currentCard--;
    reloadQuizlet();
  });
  
  $("#quizlet_fwd").click(function() {
    currentCard++;
    reloadQuizlet();
  });

  $("#quizlet_reveal").click(function() {
    if ($("#quizlet_answer").is(":visible")) {
      $("#quizlet_reveal").text("Show Answer")
      $("#quizlet_answer").hide(400);
      $("#quizlet_reveal").removeClass().addClass("btn btn-success")
    } else {
      $("#quizlet_reveal").text("Hide Answer")
      $("#quizlet_answer").show(400);
      $("#quizlet_reveal").removeClass().addClass("btn btn-danger")
    }
  });  
});

function reloadQuizlet() {
  $("#quizlet_answer").hide();
  $("#quizlet_reveal").text("Show Answer")
  $("#quizlet_reveal").removeClass().addClass("btn btn-success")
  
  $("#quizlet_term").text(quizletData[currentCard]["term"]); 
  $("#quizlet_answer").text(quizletData[currentCard]["definition"]);
  console.log(currentCard);
  console.log(currentCard - 1);
  if ((currentCard - 1) >= 0 && (currentCard - 1) < quizletData.length) {
    $('#quizlet_back').prop('disabled', false);
  } else {
    $('#quizlet_back').prop('disabled', true);
  }
  
  if ((currentCard + 1) > quizletData.length) {
    $('#quizlet_fwd').prop('disabled', true);
  } else {
    $('#quizlet_fwd').prop('disabled', false);
  }
}