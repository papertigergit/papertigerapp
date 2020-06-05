$(document).ready(function() {

	$("#signup").validate({
		
		rules: {
			signup_password: {
				required: true,
				minlength: 5
			},
			signup_password_confirmation: {
				required: true,
				minlength: 5,
				equalTo: "#signup_password"
			},
		},
		
		messages: {
		
			signup_password: {
				required: "Please enter a password!",
				rangelength: jQuery.format("Enter at least {0} characters")
			},
			signup_password_confirmation: {
				required: "The passwords did not match!",
				minlength: jQuery.format("Enter at least {0} characters"),
				equalTo: "Make sure you enter the same password!"
			},
			card_zip_code:{
				required: "Billing Zip Code is required!",
				},
			ccnumber: {
				required: "Your Credit Card number is required!"
				},
		},
		
		


		errorPlacement: function(error, element) {
		     if (element.attr("name") == "ccnumber"
		                 || element.attr("name") == "card_expires_on_month"
		                 || element.attr("name") == "card_expires_on_year")
		       error.insertAfter("#secure");
		       
		       error.$("#secure").html("");
		       
        },

		success: function(label) {
		

		if (element.attr("id") == "card_number" 
		                 || element.attr("id") == "card_zip_code"
		                 || element.attr("id") == "card_expires_on_month"
		                 || element.attr("id") == "card_expires_on_year")
		       label.html("");
		     else

			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		       
        },


       
		
		
errorPlacement: function(error, element) {
		     error.appendTo( element.parent("td").next("td") );
		},	

errorPlacement: {
		
		     if (element.attribute("name") == "ccnumber");
		       error.insertAfter("#cc");
		     else
		       error.insertAfter(element);
		}
		
		messages: {
			firstname: "Enter your firstname",
			lastname: "Enter your lastname",
			username: {
				required: "Enter a username",
				minlength: jQuery.format("Enter at least {0} characters"),
			},
			password: {
				required: "Provide a password",
				rangelength: jQuery.format("Enter at least {0} characters")
			},
			password_confirm: {
				required: "Repeat your password",
				minlength: jQuery.format("Enter at least {0} characters"),
				equalTo: "Enter the same password as above"
			},
			email: {
				required: "Please enter a valid email address",
				minlength: "Please enter a valid email address",
				remote: jQuery.format("{0} is already in use")
			},
			dateformat: "Choose your preferred dateformat",
			terms: " "
		},
		// the errorPlacement has to take the table layout into account
		errorPlacement: function(error, element) {
			if ( element.is(":radio") )
				error.appendTo( element.parent().next().next() );
			else if ( element.is(":checkbox") )
				error.appendTo ( element.next() );
			else
				error.appendTo( element.parent().next() );
		},
		// specifying a submitHandler prevents the default submit, good for the demo
		submitHandler: function() {
			alert("submitted!");
		},
		// set this class to error-labels to indicate valid fields
		success: function(label) {
			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		}

	
	});
	
});		
