$(function(){

$('#email').focus();
	
	$('#login-form').submit( function() { 
		$(this).ajaxSubmit({dataType: 'json', success: processResults});
    return false;
	});
});

function processResults(data){
	$('.alert').remove();
	if(data.result == "validation_error") {
		if(data.email_error_text != ''){
			$('#email').css({borderColor: 'red'}).after(data.email_error_text);
		}
		if(data.password_error_text){
			$('#password').css({borderColor: 'red'}).after(data.password_error_text);
		}
		$('#login-form').before(data.login_error_text);
		$('.new_alert').fadeIn().removeClass('new_alert');
	} else if (data.result=="successful_authentication"){
		window.location = "/dashboard"
	}
}

function applySlidingLabels() {
	$('form .wrapper label').each(function(){
		$(this).addClass('sliding_label');
		
		var inputval = $(this).next('input').val();
		
		var labelwidth = $(this).width();
		var labelmove = labelwidth + 5;
		
		//onload, check if a field is filled out, if so, move the label out of the way
		if(inputval !== ''){
			$(this).stop().animate({ 'left':'-'+labelmove }, 1);
		}    	
		
		// if the input is empty on focus move the label to the left
		// if it's empty on blur, move it back
		$('input:not(:submit)').focus(function(){
			var label = $(this).prev('label');
			var width = $(label).width();
			var adjust = width + 5;
			var value = $(this).val();
			
			if(value == ''){
				label.stop().animate({ 'left':'-'+adjust }, 'fast');
			} else {
				label.css({ 'left':'-'+adjust });
			}
			
			$(this).css({backgroundColor: 'white', borderColor: '#999'}).next().remove();
			
		}).blur(function(){
			var label = $(this).prev('label');
			var value = $(this).val();
			if(value == ''){
				label.stop().animate({ 'left':'5px' }, 'fast');
			}
		});
	});
}
