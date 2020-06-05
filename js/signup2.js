$(document).ready(function() {
		$("#signup").validate({

			 highlight: function(element, errorClass) {
			        $(element).css({'background-color': 'pink'});
			 },

			 unhighlight: function(element, errorClass) {
			 		       $(element).css({'background-color': 'white'});
			 },


		});
        
        $("#signup_first_name").rules("add", { 
            required:true,  
            messages: {  
                required: "Enter your first name!",  
            }

        });
        $("#signup_last_name").rules("add", { 
            required:true,  
            messages: {  
                required: "Enter your last name!",  
            }

        });
        $("#signup_company_name").rules("add", { 
            required:true,  
            messages: {  
                required: "Enter a company\/organization!",  
            }

        });
        $("#signup_email").rules("add", { 
            required:true,
            email:true,
            messages: {  
                required: "Please enter your email!",
                email: "Enter a valid email address!"
            }

        });
        $("#signup_password").rules("add", { 
            required:true,
            minlength: 6,
            messages: {  
                required: "Enter a password!",
                minlength: "Enter something longer than 6 characters!"
            }

        });
        $("#signup_password_confirmation").rules("add", { 
            required:true,
            equalTo: "#signup_password",
            messages: {  
                required: "Please verify the password!",
                equalTo: "Passwords do not match!"
            }

        });

    jQuery.fn.exists = function(){return jQuery(this).length>0;}

    if ($("#cc_wrapper").exists()) {

        $("#card_number").rules("add", { 
            required:true,
            number:true,
            messages: {  
                required: "Please enter a credit card number!",
            }

        });

        $("#billingFirstName").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });
        
        $("#billingLastName").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });

        $("#billingCity").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });
        
        $("#billingStreet").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });

        $("#billingState").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });
        
        $("#billingZip").rules("add", { 
            required:true,
            messages: {  
                required: "Required"
            }

        });

}

});

