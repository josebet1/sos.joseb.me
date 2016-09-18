/*
 *
 * login-register modal
 * Autor: Creative Tim
 * Web-autor: creative.tim
 * Web script: http://creative-tim.com
 * 
 */
function showLoginForm(){
    $('#loginModal .registerBox').fadeOut('fast',function(){
        $('.loginBox').fadeIn('fast');
        $('.register-footer').fadeOut('fast',function(){
            $('.login-footer').fadeIn('fast');    
        });
        
        $('.modal-title').html('Login with Email');
    });       
     $('.error').removeClass('alert alert-danger').html(''); 
}

function openLoginModal(){
    showLoginForm();
    setTimeout(function(){
        $('#loginModal').modal('show');    
    }, 230);
    
}
function openRegisterModal(){
    showRegisterForm();
    setTimeout(function(){
        $('#loginModal').modal('show');    
    }, 230);
    
}

function loginAjax(){
  $.ajax('/api/user/login', {
    'data': JSON.stringify({ username: $('#email').val(), password: $('#password').val() }),
    'type': 'POST',
    'contentType': 'application/json' })
  .done(function(data) {
    console.log(data);
      window.location.replace("/dashboard?token=" + data['token'] + "&id=0");
  })
  .fail(function() {
    shakeModal()
  });

}

function shakeModal(){
    $('#loginModal .modal-dialog').addClass('shake');
             $('.error').addClass('alert alert-danger').html("Invalid email/password combination");
             $('input[type="password"]').val('');
             setTimeout( function(){ 
                $('#loginModal .modal-dialog').removeClass('shake'); 
    }, 1000 ); 
}

function shakeModal_Reg(){
    $('#loginModal .modal-dialog').addClass('shake');
             $('.error').addClass('alert alert-danger').html("Oops, seems like you didn't fill out something.");
             $('input[type="password"]').val('');
             setTimeout( function(){ 
                $('#loginModal .modal-dialog').removeClass('shake'); 
    }, 1000 ); 
}

function shakeModal_FB(){
    $('#loginModal .modal-dialog').addClass('shake');
             $('.error').addClass('alert alert-danger').html("Oops, something went wrong. Try that again!");
             setTimeout( function(){ 
                $('#loginModal .modal-dialog').removeClass('shake'); 
    }, 1000 ); 
}

function validateForm()
{
	if( !$('#reg_password_confirmation').val() ) {
		shakeModal_Reg();
		event.preventDefault();
	}
	if( !$('#reg_password').val() ) {
		shakeModal_Reg();
		event.preventDefault();
	}
	if( !$('#reg_email').val() ) {
		shakeModal_Reg();
		event.preventDefault();
	}
	
}