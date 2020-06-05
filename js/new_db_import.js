$(document).ready(function(){
  $("#import_db_form").validate({    
  
	  rules: {
	    file: {
	      required: true,
	      accept: "ptd|bak|mdf"
	    },
	    name: {
	      required: true
	    }
	  },
	  
	  messages: {
	  
	  	file: {
	  		required: "Please choose a database to import!",
	  		accept: "Make sure your database has a .BAK, .PTD, or .MDF extension!"
	  	},
	  	
	  	name: {
	  		required: "Please enter a name for your database!",
	  	},
	  	
	  },
	  
	  submitHandler: function(form) {
	  		jQuery("#submit_container").html('<img class="spinner" src="/img/spinner.gif"><span class="please_wait">Please wait, uploading...</span>');
			form.submit();
	     }
	  
	});
});