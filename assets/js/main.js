$(function(){
	
    var BV = new $.BigVideo();
    BV.init();
    if (Modernizr.touch) {
	    BV.show('https://d2chfjgjz40wdu.cloudfront.net/static/images/videos/main-poster.jpg');
	} else {
		BV.show([
	        { type: "video/mp4",  src: "https://d2chfjgjz40wdu.cloudfront.net/static/images/videos/cover.mp4" },
	        { type: "video/webm", src: "https://d2chfjgjz40wdu.cloudfront.net/static/images/videos/main-poster.webm" }
	    ], {ambient: true});
		
	}
    
    $("#fb_login").click(function() {
		OAuth.popup('facebook', {state: state_token}).done(function(result) {
			console.log(result.code);
			$.post('/auth/oauth.php', {code: result.code}).done(function (data, status) {
				if (data.error)
				{
					shakeModal_FB();
				}
				else
				{
					window.location.replace(data.url);
				}
			});
		}).fail(function(err) {
			shakeModal_FB();
		});
	
	});
});
