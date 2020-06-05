$.ajaxSetup ({
    // Disable caching of AJAX responses */
    cache: false
});

function urldecode (str) {
    // Decodes URL-encoded string  
    // 
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/urldecode    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous    // +   improved by: Orlando
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      bugfixed by: Rob
    // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on    // %        note 2: pages served as UTF-8
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'
    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    
    return decodeURIComponent(str.replace(/\+/g, '%20'));
}

var waiting = false;
$(window).load(function(){
	prepAjax();
	
	bindFaceboxEvents();
	
	//update edit link based on checkboxes	
  var item_type;
	$('input.grid_checkbox').live('click', function (e) {
    item_type = $('#table_header_bar').attr('class').substring(7);
		//lets hightlight the row if it is checked
		var row = $(this).parent().parent().parent();
		if( row.hasClass('hightlight_row') )
		{
			row.removeClass('hightlight_row');

}
		else

{
			row.addClass('hightlight_row');
		}
				
		toss_url = site_url+ item_type +'/delete/';
    if($('table').hasClass('items')){
      var pathname = window.location.pathname;
			var split_path = pathname.split('/');
      var order_by = split_path[6];
      var order = split_path[7];
      // TODO: Figure out how to paginate results sorted by non-slot-num
      // if((order !== undefined) && (order_by !== undefined)){
      //   edit_url = site_url+ item_type +'/edit/'+order_by+'/'+order+'/';
      // }
      // else{
        edit_url = site_url+ item_type +'/edit/slot_num/asc/';
      // }
    }
    else{
      edit_url = site_url+ item_type +'/edit/';
    }
		move_url = site_url+ item_type +'/transfer/move/';
		merge_url = site_url+ item_type +'/transfer/merge/';
		dupe_url = site_url+ item_type +'/transfer/dupe/';
		reminder_url = site_url+ item_type +'/update/';
		confirm_url = site_url+'confirmations/accept/';
		cancel_url = site_url+'confirmations/cancel/';
		clear_url = site_url+item_type+'/clear/';
		restore_url =site_url+'trash/restore/';
		delete_trash_url =site_url+'trash/delete/';
		
		$('input.grid_checkbox:checked').each(function(i, e){
			edit_url+= $(e).val()+"/";
			toss_url+= $(e).val()+"/";
			move_url+= $(e).val()+"/";
			merge_url+= $(e).val()+"/";
			dupe_url+= $(e).val()+"/";
			reminder_url+=$(e).val()+"/";
			clear_url+=$(e).val()+"/";
			confirm_url+=$(e).val()+"/";
			cancel_url+=$(e).val()+"/";
			restore_url+=$(e).val()+"/";
			delete_trash_url+=$(e).val()+"/";
		});
		
		if($('.grid_checkbox:checked').size()===0) {
			$('.edit').attr('href',edit_url).removeClass('edit_button').addClass('edit_button_disabled');
			$('.toss').attr('href', toss_url).removeClass('toss_button').addClass('toss_button_disabled');
			$('.delete').attr('href', toss_url).removeClass('delete_button').addClass('delete_button_disabled');
			$('.transfer').removeClass('transfer_button').addClass('transfer_button_disabled');
			$('.reminder').attr('href',reminder_url).removeClass('reminder_button').addClass('reminder_button_disabled');
			$('.clear_date').attr('href',clear_url).removeClass('clear_button').addClass('clear_button_disabled');
			$('.submenu').slideUp();
			$('.confirm').attr('href',confirm_url).removeClass('confirm_button').addClass('confirm_button_disabled');
			$('.cancel').attr('href',cancel_url).removeClass('cancel_button').addClass('cancel_button_disabled');
			
			$('.delete_trash').removeAttr('href').removeClass('delete_trash_button').addClass('delete_trash_button_disabled');
			$('.restore').attr('href', restore_url).removeClass('restore_button').addClass('restore_button_disabled');
			
		} else if($('.grid_checkbox:checked').size()==1) {
			if(item_type!='trash') {$('.edit').attr('href', edit_url).removeClass('edit_button_disabled').addClass('edit_button');}
			$('.toss').attr('href', toss_url).removeClass('toss_button_disabled').addClass('toss_button');
			$('.delete').attr('href', toss_url).removeClass('delete_button_disabled').addClass('delete_button');
			$('#move').attr('href', move_url);
			$('#merge').attr('href', merge_url);
			$('#dupe').attr('href', dupe_url);
			$('.transfer').removeClass('transfer_button_disabled').addClass('transfer_button');
			$('.reminder').attr('href',reminder_url).removeClass('reminder_button_disabled').addClass('reminder_button');
			$('.clear_date').attr('href',clear_url).removeClass('clear_button_disabled').addClass('clear_button');
			$('.confirm').attr('href',confirm_url).removeClass('confirm_button_disabled').addClass('confirm_button');
			$('.cancel').attr('href',cancel_url).removeClass('cancel_button_disabled').addClass('cancel_button');
			
			$('.delete_trash').attr('href',delete_trash_url).removeClass('delete_trash_button_disabled').addClass('delete_trash_button');
			$('.restore').attr('href',restore_url).removeClass('restore_button_disabled').addClass('restore_button');
			
		} else {
			$('.edit').attr('href', edit_url).removeClass('edit_button_disabled').addClass('edit_button');
			$('.toss').attr('href', toss_url).removeClass('toss_button_disabled').addClass('toss_button');
			$('.delete').attr('href', toss_url).removeClass('delete_button_disabled').addClass('delete_button');
			$('.transfer').addClass('transfer_button').removeClass('transfer_button_disabled');
			$('.reminder').attr('href',reminder_url).removeClass('reminder_button_disabled').addClass('reminder_button');
			$('.clear_date').attr('href',clear_url).removeClass('clear_button_disabled').addClass('clear_button');
			$('#move').attr('href', move_url);
			$('#merge').attr('href', merge_url);
			$('#dupe').attr('href', dupe_url);
			$('.submenu').slideUp();
			$('.confirm').attr('href',confirm_url).removeClass('confirm_button_disabled').addClass('confirm_button');
			$('.cancel').attr('href',cancel_url).removeClass('cancel_button_disabled').addClass('cancel_button');
			
			$('.delete_trash').attr('href',delete_trash_url).removeClass('delete_trash_button_disabled').addClass('delete_trash_button');
			$('.restore').attr('href',restore_url).addClass('restore_button_disabled').removeClass('restore_button');
			
			if(item_type!='items'){$('.edit').attr('href', edit_url).removeClass('edit_button').addClass('edit_button_disabled');}
		}
	});
	
	
	
	// form submission handler
	
	$('.form').live('submit', function() {
        $(this).ajaxSubmit({dataType:'json', beforeSubmit: prepForSubmit, success: processResults});
    return false;
    });
    

    //workaround for ie
    $(".form input[type=submit]").live("click", function() {
        $(".form").ajaxSubmit({dataType:'json', beforeSubmit: prepForSubmit, success: processResults});
    return false;
    });

    
	//self explanatory
	$('.hide_bottom_notification').live('click', function(e){e.preventDefault();$('#bottom_notification').slideUp();});
	
	$('.submenu_link').click(function(e){$('.submenu').slideUp();});
	$('.transfer, .transfer li').live('click',function(e){
		if(!$(this).hasClass('transfer_button_disabled')){
			$('.submenu').slideToggle();
		}
	});
	

	
	$('.new_item_name').live('keyup', function(e) {
		var input = $('.new_item_name');
    if (num_items > 2000){
      // console.log('large location, disabling find-first!');
      $('#find_first_text').html('Find-First is temporarily disabled for large locations. ');
      $('#find_first').fadeIn('fast');
    }
		else {
    if (input.val().length > 2){
			//alert(urlencode($('.new_item_name').val()));
			$.getJSON('/items/find_first/'+urlencode($('.new_item_name').val()), {'prevent_caching':new Date().getTime()}, function(data){
				if(data !== null && data.num_results > 0) {
					if(data.num_results > 1) {
						$('#find_first_text').html('There are '+data.num_results+' items with similar names. ');
						$('#ff_merge').html('Choose one to merge with.');
						$('#find_first').fadeIn('fast');
						$('#ff_merge').unbind();
						$('#ff_merge').bind('click',function(e) {
							e.preventDefault();						
							var ids = '';
							//dumpProps(data);
							$.each(data.items, function(i, item){
								ids +=item.id+"/";
							});
							$('form#add_item_form').attr('action','/items/edit/'+ids);
							$('#find_first_flag').attr('value','true');
							$('form#add_item_form').submit();
						});
					} else {
						$('#find_first_text').html('There is 1 item that has a similar name. ');
						$('#ff_merge').html('Merge with this item.');
						$('#find_first').fadeIn('fast');
						$('#ff_merge').unbind();
						$('#ff_merge').bind('click',function(e) {
							e.preventDefault();							
							$('form#add_item_form').attr('action','/items/edit/'+data.items.item.id);
							$('#find_first_flag').attr('value','true');
							$('form#add_item_form').submit();
						});
					}
				} else {
					$('#find_first').fadeOut('fast');
				}
  		});
		} else {
			$('#find_first').fadeOut('fast');
		}
    }
	});
});

//////   facebox Stuff
function bindFaceboxEvents() {
	//make links close facebox
	$('#facebox .close').live('click',function(e){
		e.preventDefault();	
		$('tr.toDelete').stop(true).css('backgroundColor','white').removeClass('toDelete');
		$(document).trigger('close.facebox');
	});
	$('#find_first_table img').live('click',function(e){
		$(this).closest('tr').next().toggleClass('keys_hidden');
	});
	//do this stuff every time a facebox is shown
	$(document).bind('reveal.facebox', function(){
		//add focus highlighting to labels
		$('.inline_input, .inline_select').bind('focusin focusout', function(){$(this).parent().siblings('label').toggleClass('focus');return false;});
		$('.input, .select').bind('focusin focusout', function(){$(this).siblings('label').toggleClass('focus');});
		$('#submit').bind('focusin focusout', function(){$(this).parent().toggleClass('focus');return false;});
		// set initial focus
		$('#facebox .first_input').focus();
		//init jqueryUI widgets, tooltips
		$('#action_date').datepicker({minDate: new Date(),hideIfNoPrevNext:true});

		$('.tooltip').each(function()
		   {
		
		
		      $(this).qtip({
		         content: $(this).parent().prev('p.tooltip_content').html(), // Use the tooltip attribute of the element for the content
		        
				 hide: { delay: 250,
						 fixed: true
						},
				 show: { solo: true, delay:0}
			  });
		   });

		
		// $('.tooltip').qtip({
		// 		content: {text: $("p").prev(".tooltip_content").html()},
		// 		position:{
		// 			corner:{
		// 				target: 'rightMiddle',
		// 				tooltip: 'leftMiddle'
		// 			},
		// 			adjust:{x:10}
		// 		}
		// 	});
					
		//reset bg color on inputs after focus (if there were prior errors)
		$('.input, .inline_input').focus(function(){$(this).css('background-color', 'white');});
		$('.select').focusin(function(){$(this).css('background-color', 'white');});
		//enable facebox links from inside a facebox
		$('a[rel$=modal_fb]').bind('click',function(){$.facebox.settings.modal=true;});
		$('a[rel$=nm_fb]').bind('click',function(){$.facebox.settings.modal=false;});
		$('a[rel*=facebox_fb]').facebox();
		//enable enter for form submission in textareas
		$('textarea').keydown(function(e){if (e.keyCode == 13) {
    	$(this).parents('form').submit();
      return false;
		}});
	});
}


//do this stuff on pageload and everytime an ajax request replaces .content ------------------------------------------------------------------------------------------------------------------------------------
function prepAjax(){

$('th').disableTextSelect();

//Slide up delay for flash error and success
//$("div#flash").click(function(){
//	$("div#flash").slideUp(400);
//});

//Hide action for message
$('#hide_app_message').click(function(event){
	event.preventDefault();	
	$("#app_message").fadeOut(400);	
		  $.ajax({
		  url: "/dashboard/hide_message",
		  cache: false
		  });
});

//Slide down and stay function for flash warning, info
$("#flash_hide_action_date").click(function(event){
	event.preventDefault();	
	$("div#flash.flash_warning,div#flash.flash_info").slideUp(400);	
		  $.ajax({
		  url: "/databases/remove_reminder",
		  cache: false
		  });
});

//
$("#flash_hide").live('click', function(event){
	event.preventDefault();		
	$("div#flash.flash_success,div#flash.flash_error").fadeOut(400);	
});

//Dropdown Checklist for advanced search
$("#category_dropdown").dropdownchecklist({
	emptyText: "Please select ...",	
	maxDropHeight: 150
});
$("#no_category").dropdownchecklist({
	maxDropHeight: 0
});
$("#location_dropdown").dropdownchecklist({
	maxDropHeight: 150
});
$("#no_location").dropdownchecklist({
	maxDropHeight: 0
});

//Dropdown Checklist for Reports

//Dropdown Checklist for advanced search
$("#category_dropdown_file_list").dropdownchecklist({
	emptyText: "Please select ...",	
	maxDropHeight: 150
});
$("#location_dropdown_file_list").dropdownchecklist({
	maxDropHeight: 150
});  

$("#category_dropdown_action_date").dropdownchecklist({
	emptyText: "Please select ...",	
	maxDropHeight: 150
});
$("#location_dropdown_action_date").dropdownchecklist({
	maxDropHeight: 150
});  

$("#category_dropdown_file_cleanout").dropdownchecklist({
	emptyText: "Please select ...",	
	maxDropHeight: 150
});
$("#location_dropdown_file_cleanout").dropdownchecklist({
	maxDropHeight: 150
});  

//toggle advanced search	
$("#adv_search_toggle").toggle(
      function (event) {
	   	event.preventDefault();      
        $('#adv_search').show();
        $('#utilities').css({marginBottom:"74px"});
        $('#adv_search_toggle').text('Hide advanced search');
        $('#search_button').hide();        
      },
      function (event) {
	   	event.preventDefault();      
      	$('#adv_search').hide();
        $('#utilities').css({marginBottom:"0px"});
        $('#adv_search_toggle').text('Show advanced search');        
        $('#search_button').show();

}
    );
    
//close cancel category div
       	
$("#search_toggle #one").toggle(
      function (event) {
           event.preventDefault();      
        $(this).find(".handle").animate({left: '40px'}, 100);
        $(this).addClass("OFF");
        $('#digital_results').toggle('slow');        
      },
      function (event) {
           event.preventDefault();      
         $(this).find(".handle").animate({left: "0"}, 100);
         $(this).removeClass("OFF");
         $('#digital_results').toggle('slow');
}
    );


$("#search_toggle #two").toggle(
      function (event) {
           event.preventDefault();      
        $(this).find(".handle").animate({left: '40px'}, 100);
        $(this).addClass("OFF");
        $('#paper_results').toggle('slow');
        $('.paper_results').toggle('slow');        
      },
      function (event) {
           event.preventDefault();      
         $(this).find(".handle").animate({left: "0"}, 100);
         $(this).removeClass("OFF");
         $('#paper_results').toggle('slow');
         $('.paper_results').toggle('slow');
}
    );






	
//toggle functions for Reports Advanced Options
   $('#reports_adv_options_toggle_transfer').click(function(event){
   	 event.preventDefault();   
     $('#reports_adv_options_container_transfer').slideToggle();
     $(this).text($(this).text() == 'Hide advanced options' ? 'Show advanced options' : 'Hide advanced options');     

});
   
   $('#reports_adv_options_toggle_fileList').click(function(event){
   	 event.preventDefault();   
     $('#reports_adv_options_container_fileList').slideToggle();
     $(this).text($(this).text() == 'Hide advanced options' ? 'Show advanced options' : 'Hide advanced options');
   });   

   $('#reports_adv_options_toggle_actionDate').click(function(event){
   	 event.preventDefault();   
     $('#reports_adv_options_container_actionDate').slideToggle();
     $(this).text($(this).text() == 'Hide advanced options' ? 'Show advanced options' : 'Hide advanced options');     
   }); 

   $('#reports_adv_options_toggle_fileCleanout').click(function(event){
   	 event.preventDefault();   
     $('#reports_adv_options_container_fileCleanout').slideToggle();
     $(this).text($(this).text() == 'Hide advanced options' ? 'Show advanced options' : 'Hide advanced options');     
   });
   
//toggle for db imports file location instructions
   $('#db_import_tool_toggle').click(function(event)
   {   
   	 event.preventDefault();
     $('#db_import_tool').slideToggle();
 	 $('html, body').animate({
 	 	scrollTop: $("#db_import_tool").offset().top
	 }, 500);
     $(this).text($(this).text() == 'Hide instructions' ? 'Continue with the Automatic Database Import Tool.' : 'Hide instructions');     
   }); 

//scroll to top behavior
/* set variables locally for increased performance */
var scroll_timer;
var displayed = false;
var $message = $('#message');
var $window = $(window);
/* var top = $(document.body).children(0).position().top; */
var top = window.innerHeight ? window.innerHeight : $(window).height();

/* react to scroll event on window */
$window.scroll(function () {
	window.clearTimeout(scroll_timer);
	scroll_timer = window.setTimeout(function () { // use a timer for performance
		if($window.scrollTop() <= top) // hide if at the top of the page
		{
			displayed = false;
			$message.fadeOut(500);
		}
		else if(displayed === false) // show if scrolling down
		{
			displayed = true;
			$message.stop(true, true).fadeIn(500).click(function () { $message.fadeOut(500); });
		}
	}, 100);
});

$('#top-link').click(function(e) {
	e.preventDefault();
/* 	$.scrollTo(0,300); */
	$('html, body').animate({scrollTop:0}, 'fast');
});

/**------END SCROLL TO TOP -------**/
	
	//init facebox links
	$('a[rel$=modal]').bind('click',function(){$.facebox.settings.modal=true;});
	$('a[rel$=nm]').bind('click',function(){$.facebox.settings.modal=false;});
	$('a[rel*=facebox]').facebox();
	//init 'jump to location' dropdown
	$('#location_jump').change(function(){
		if($('#location_jump').val()=='trash'){
			window.location = "/trash";	
		} else {
			window.location = "/locations/view/"+$('#location_jump').val();
		}
	});
	$('#category_jump').change(function(){
		window.location = "/categories/view/"+$('#category_jump').val();
	});
	//init gridsorter

  //ShiftClick Binding	
  $('input.grid_checkbox').unbind();
  $('input.grid_checkbox').shiftClick();



// RECENT TOGGLE

$('.recent_switch').hide();  
$('#recent_toggle').click(function () {
	/* $('.recent_switch').hide(); */
    $('.recent_switch').toggle('slow');
    $(this).text($(this).text() == '[-]' ? '[+]' : '[-]');
    return false;
  });
  
// RECENT TOGGLE

$('.recent_blog_post').show();  
$('#recent_blog_toggle').click(function () {
	/* $('.recent_switch').hide(); */
    $('.recent_blog_post').toggle('slow');
    $(this).text($(this).text() == '[+]' ? '[-]' : '[+]');
    return false;
  });
$('#expert_toggle').click(function () {
	/* $('.recent_switch').hide(); */
    $('.my_expert_choose').toggle('slow');
    $(this).text($(this).text() == '[+]' ? '[-]' : '[+]');
    return false;
  });

/*
  $('.choose_expert_profile').hide(); 
  $('#read_more_expert').click(function () {
    $('.choose_expert_profile').toggle('slow');
    $(this).text($(this).text() == 'read more' ? 'hide' : 'read more');
    return false;
  });
*/

/*
$('.expert_info button').bind(click, function(e){

document.myform.submit();


})
*/

/*
jQuery('.submitCal').click(function () {
  var timeVar = jQuery("<input type='hidden' name='time'/>");
  timeVar.val(jQuery(this).attr("href"));
  jQuery("#calendar").append(timeVar).submit();  
  return false;
});
*/

  
// Pending TOGGLE

$('.table_header_bar_pending').hide();  
$('.expand_unconverted').click(function () {
    /* $('.recent_switch').hide(); */
    $('.pending_conversion_grid').hide('slow');
    $('.table_header_bar_pending').slideToggle('slow');
    $(this).text($(this).text() == 'Hide [-]' ? 'Show [+]' : 'Hide [-]');
    return false;
  });

$('.pending_conversion_grid').hide();  
$('#pending_toggle_button').click(function () {
	/* $('.recent_switch').hide(); */
    $('.pending_conversion_grid').slideToggle('slow');
    $(this).text($(this).text() == 'Hide [-]' ? 'Show [+]' : 'Hide [-]');
    return false;
  });
  
// Pending TOGGLE
$('#paper_toggle_button').click(function () {
	/* $('.recent_switch').hide(); */
    $('.paper_results').slideToggle('slow');
    $(this).text($(this).text() == 'Show [+]' ? 'Hide [-]' : 'Show [+]');
    return false;
  });

$('a').each(function() {                                                         
	var a = new RegExp('/' + window.location.host + '/');                            
	  if(!a.test(this.href)) {                                                       
		      $(this).click(function(event) {                                              
			            event.preventDefault();                                                    
				          event.stopPropagation();                                                   
					        window.open(this.href, '_blank');                                          
						    });                                                                          
		        }                                                                              
});
  
// Pending TOGGLE
$('#digital_toggle_button').click(function () {
	/* $('.recent_switch').hide(); */
    $('.digital_files').slideToggle('slow');
    $(this).text($(this).text() == 'Show [+]' ? 'Hide [-]' : 'Show [+]');
    return false;
  });

$('ul .people li:gt(2)').hide(); 
$('ul .people .more_people').toggle(
      function (event) {
           event.preventDefault();      
        $('ul .people li:gt(2)').toggle('slow');        
				$(this).text($(this).text() == 'See More' ? 'Hide' : 'See More');
				return false;
      },
      function (event) {
           event.preventDefault();      
        $('ul .people li:gt(1)').toggle('slow');        
				$(this).text($(this).text() == 'See More' ? 'Hide' : 'See More');
				return false;
      }
			);

$('ul .tweets li div:gt(1)').hide(); 
$('ul .tweets .more_tweets').toggle(
      function (event) {
           event.preventDefault();      
        $('ul .tweets li div:gt(1)').toggle('slow');        
				$(this).text($(this).text() == 'See More' ? 'Hide' : 'See More');
				return false;
      },
      function (event) {
           event.preventDefault();      
        $('ul .tweets li div:gt(1)').toggle('slow');        
				$(this).text($(this).text() == 'See More' ? 'Hide' : 'See More');
				return false;
      }
			);


	//RECENT ACTIVITY TABLE -----------------------------------------------------------RECENT ACTIVITY TABLE
		dataTableGrid = $('#recent_activity:not(.empty)').dataTable({
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": false,
						"bInfo": false,
						"fnDrawCallback": function(){
							$('input.grid_checkbox').unbind();
							$('input.grid_checkbox').shiftClick();
						},
						"aaSortingFixed": [[3,'desc']],
						"bAutoWidth": false,
						"aoColumns": [
								{ "bSortable": false },
								{ "bSortable": false },
								{ "bSortable": false },								
								{ "sType": "date" }
						]
			});
	
	//CATEGORIES TABLE -----------------------------------------------------------CATEGORIES TABLE
		dataTableGrid = $('#category_table:not(.empty)').dataTable({
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": false,
						"bInfo": false,
						"fnDrawCallback": function(){
							$('input.grid_checkbox').unbind();
							$('input.grid_checkbox').shiftClick();
						},
						"bAutoWidth": false,
						"aoColumns": [
								{ "sType": "html" },
								{ "bSortable": false },
								null
						]
			});
	
	//REMINDERS TABLE -----------------------------------------------------------REMINDERS TABLE
		dataTableGrid = $('#reminder_table:not(.empty)').dataTable({
			"bPaginate": false,
			"bLengthChange": false,
			"bFilter": false,
			"bInfo": false,
			"fnDrawCallback": function(){
				$('input.grid_checkbox').unbind();
				$('input.grid_checkbox').shiftClick();
			},
			"bAutoWidth": false,
			"aoColumns": [
				null,
				null,
				{ "sType": "html" },
				{ "bSortable": false },
				{ "bSortable": false },
				{ "sType": "date" }
				]
		});
		dataTableGrid = $('#reminder_table_without_notes:not(.empty)').dataTable({
			"bPaginate": false,
			"bLengthChange": false,
			"bFilter": false,
			"bInfo": false,
			"fnDrawCallback": function(){
				$('input.grid_checkbox').unbind();
				$('input.grid_checkbox').shiftClick();
			},
			"bAutoWidth": false,
			"aoColumns": [
				null,
				null,
				{ "sType": "html" },
				{ "bSortable": false },
				{ "sType": "date" }
				]
		});
	
	//LOCATIONS TABLE -----------------------------------------------------------LOCATIONS TABLE
		dataTableGrid = $('#location_table:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "html" },
							{ "bSortable": false },
							null,
							null,
							{ "sType": "date" }
					]
    });

	//ITEMS BY CATEGORY TABLE -----------------------------------------------------------TRASH TABLE
      dataTableGrid = $('#item_table_by_category:not(.empty)').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "fnDrawCallback": function(){
          $('input.grid_checkbox').unbind();
          $('input.grid_checkbox').shiftClick();
        },
        //"bStateSave": true,
        "bAutoWidth": false,
        "aoColumns": [
      { "sType": "num-html" },
        { "sType": "string" },
        { "bSortable":false },
        { "bSortable":false },
        { "sType": "string" },
        { "sType": "date" },
        null
        ]
      });
      dataTableGrid = $('#item_table_by_category_without_notes:not(.empty)').dataTable({
        "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bInfo": false,
                    "fnDrawCallback": function(){
                      $('input.grid_checkbox').unbind();
                      $('input.grid_checkbox').shiftClick();
                    },
                    //"bStateSave": true,
                    "bAutoWidth": false,
                    "aoColumns": [
      { "sType": "num-html" },
                    { "sType": "string" },
                    { "bSortable":false },
                    { "sType": "string" },
                    { "sType": "date" },
                    null
        ]
      });

	//TRASH TABLE -----------------------------------------------------------TRASH TABLE
		dataTableGrid = $('#trash_table:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					//"bStateSave": true,
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "num-html" },
							{ "sType": "string" },
							{ "bSortable":false },
							{ "bSortable":false },
							{ "sType": "string" },
							null
					]
		});

		dataTableGrid = $('#trash_table_without_notes:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					//"bStateSave": true,
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "num-html" },
							{ "sType": "string" },
							{ "bSortable":false },
							{ "sType": "string" },
							null
					]
		});

	//	new FixedHeader( dataTableGrid );
		
	//SEARCH RESULTS -----------------------------------------------------------SEARCH RESULTS
		dataTableGrid = $('#search_results_table:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "num-html" },
							{ "sType": "html" },
							{ "bSortable":false},
							{ "bSortable":false},
							null,
							null,
							null
					]
		});
		
		dataTableGrid = $('#search_results_table_google:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": true,
				"aoColumns": [
							{ "asSorting": [ "asc" ] },
							{ "sType": "html" },
							{ "sType": "date"},
							{ "sType": "date"},
							null
					]
		});
		
		dataTableGrid = $('#search_results_table_unconverted:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": false,
					"aoColumns": [
							{ "bSortable":false},
							{ "sType": "html" },
							{ "sType": "date"},
							{ "sType": "date"},
							null
					]
		});
		
		

		dataTableGrid = $('#search_results_table_without_notes:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "num-html" },
							{ "sType": "html" },
							{ "bSortable":false},
							null,
							null,
							null
					]
		});
		
	//CONFIRMATION TABLE -----------------------------------------------------------CONFIRMATION TABLE	
		dataTableGrid = $('table.confirmation_table:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"bAutoWidth": false,
					"aoColumns": [
							//{ "sType": "num-html" },
							null,
							null,
							null,
							{ "bSortable":false }
					]
		});
		
	//STATUS TABLE -----------------------------------------------------------STATUS TABLE
		dataTableGrid = $('table#status_table:not(.empty)').dataTable({
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": false,
					"bInfo": false,
					"fnDrawCallback": function(){
						$('input.grid_checkbox').unbind();
						$('input.grid_checkbox').shiftClick();
					},
					"aaSorting": [[ 1, "desc" ]],
					"bAutoWidth": false,
					"aoColumns": [
							{ "sType": "html" },
							{ "sType": "date" },
							{ "sType": "html" },
							{ "sType": "html" },
							{ "sType": "num-html" }
							
					]
		});		
}

function prepForSubmit(formData, jqForm, options){
	//if we are waiting on an ajax call, do not submit another request, return
	if (waiting) {return false;}
	//disable the submit visually
	$('#submit').attr('disabled', 'disabled');
	//do not accept more submissions
	waiting = true;
}

//do this when you get a json response
function processResults(data) {
	if(data.result == "validation_error") {
		error_string = "";
		for(err in data.errors) {
			//highlight each input with an error independently
			$('#'+err).animate({backgroundColor: 'pink'});
			$('#result').addClass('error');
			error_string += (data.errors[err]);
		}
		//show error box with all error strings
		$('#result').html(error_string).slideDown();
		//re-enable form submission
		$('#submit').attr('disabled', '');
		waiting = false;
    } 
    else if (data.result == "slot_collision_error"){
        // $('#facebox_intro').hide();
		error_string = "";
        //Show error message at the bottom
		for(err in data.errors) {
			//highlight each input with an error independently
			$('#result').addClass('error');
			error_string += (data.errors[err]);
        }
        //Update the edit timestamp with current time
        var new_time = new Date().getTime();
        $('#edit_time').val(new_time);
		//show error box with all error strings
		$('#result').html(error_string).slideDown();
        //re-enable form submission
		$('#submit').attr('disabled', '');
        $('#submit').val('Add to ' + data.location_name + ' ' + data.new_slot_number);
		$('#position').val(data.new_slot_number);
		$('#next_avail_slot').html(data.new_slot_number);
		waiting = false;
    }
    else if (data.result == "collision_error"){
        
        //fill divs with the person's name that beat you to editing this item
        $('.collision_edited_by').html(data.edited_by);

        //Change active link to "your changes"
        $('#collision_yours').addClass('active'); 

        //Change submit text to appropriate initial message
        $('#submit').val('Overwrite Previous Changes with My Changes');
        //Update inputs with original information
        $('#collision_original').click(function(event) {
                event.preventDefault();						
                //fill divs with old data
                $('#name').val(urldecode($('#original_item_name').html()));
                $('#keywords').val(urldecode($('#original_keywords').html()));
                //only fill in notes if the div exists!
                if ( $('#original_item_notes').length ) { $('#notes').val(urldecode($('#original_notes').html())); }
                $('#category').val(urldecode($('#original_category').html()));
                $('#action_date').val(urldecode($('#original_action_date').html()));
                if ( !($(this).hasClass("active") )) {
                    $(".collision_link").removeClass("active");							  
                    $(this).addClass('active'); 
                }
                $('#name, #keywords, #notes, #category, #action_date').css('backgroundColor', 'lightblue').delay(300).animate({"backgroundColor": "white"}, 1000);
            });

        //Update inputs with your information
        $('#collision_yours').click(function(event) {
                event.preventDefault();						
                //fill divs with old data
                $('#name').val(urldecode(data.your_item_name));
                $('#keywords').val(urldecode(data.your_item_keywords));
                //only fill in notes if the div exists!
                if ( $('#your_item_notes').length ) { $('#notes').val(urldecode(data.your_item_notes)); }
                $('#category').val(urldecode(data.your_item_category));
                $('#action_date').val(urldecode(data.your_item_action_date));
                if ( !($(this).hasClass("active") )) {
                    $(".collision_link").removeClass("active");							  
                    $(this).addClass('active'); 
                }
                $('#name, #keywords, #notes, #category, #action_date').css('backgroundColor', 'lightblue').delay(300).animate({"backgroundColor": "white"}, 1000);
            });

        //Update inputs with new information
        $('#collision_new').click(function(event) {
                event.preventDefault();						
                //fill divs with old data
                $('#name').val(urldecode(data.new_item_name));
                $('#keywords').val(urldecode(data.new_item_keywords));
                //only fill in notes if the div exists!
                if ( $('#new_item_notes').length ) { $('#notes').val(urldecode(data.new_item_notes)); }
                $('#category').val(urldecode(data.new_item_category));
                $('#action_date').val(urldecode(data.new_item_action_date));
                if ( !($(this).hasClass("active") )) {
                    $(".collision_link").removeClass("active");							  
                    $(this).addClass('active'); 
                }
                $('#name, #keywords, #notes, #category, #action_date').css('backgroundColor', 'lightblue').delay(300).animate({"backgroundColor": "white"}, 1000);
            });

        //Update inputs with merged information
        $('#collision_merged').click(function(event) {
                event.preventDefault();						
                //fill divs with old data
                $('#name').val(urldecode(data.merged_item_name));
                $('#keywords').val(urldecode(data.merged_item_keywords));
                //only fill in notes if the div exists!
                if ( $('#merged_item_notes').length ) { $('#notes').val(urldecode(data.merged_item_notes)); }
                $('#category').val(urldecode(data.your_item_category));
                $('#action_date').val(urldecode(data.your_item_action_date));
                if ( !($(this).hasClass("active") )) {
                    $(".collision_link").removeClass("active");							  
                    $(this).addClass('active'); 
                }
                $('#name, #keywords, #notes, #category, #action_date').css('backgroundColor', 'lightblue').delay(300).animate({"backgroundColor": "white"}, 1000);
            });

       //enable tooltips for each option 
		$('.collision_link').each(function()
		   {
		      $(this).qtip({
		         content: $(this).next('p.collision_tooltip').html(),
		         style: {
						name: 'cream',
						tip: true
						},
				 hide: { delay: 250,
						 fixed: true
						}
		      });
		   });

        $('#collision_errors').css('backgroundColor', 'pink').delay(600).animate({"backgroundColor": "white"}, 2000);
        // $('#facebox_intro').hide();
		error_string = "";
        //Show error message at the bottom
		for(err in data.errors) {
			//highlight each input with an error independently
			$('#result').addClass('error');
			error_string += (data.errors[err]);
        }
        //Update the edit timestamp with current time
        new_time = new Date().getTime();
        $('#edit_time').val(new_time);
		//show error box with all error strings
		$('#result').html(error_string).slideDown();
		$('#collision_selector').show();
        //re-enable form submission
		$('#submit').attr('disabled', '');
        $('#submit').val('Overwrite Previous Changes');
		waiting = false;
    }
    
    else if (data.result=="successful_input"){
		//this is to prevent response caching in IE
		time = new Date().getTime();
		//make the call to get new grid data
		$("#content").load(data.grid_callback, {'prevent_caching':time}, function(){
			//after successful loading:
			//close facebox
			$(document).trigger('close.facebox');
			//prep the grid elements
			prepAjax();
			//restore the form to original status and accept new submissions
			$('.form').resetForm();
			$('#result').removeClass('error').empty().hide();
			$('#submit').attr('disabled', '');
			waiting = false;
			highlightRow();
			
			//if we have a message, lets show it
			
			if( data.message != undefined && data.message_type != undefined) {
				$('#flash').remove();
				$('body').prepend('<div id="flash" class="flash_' + data.message_type + '"><p>' + data.message + '</p><a href="#" id="flash_hide" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>');	
			} else {
				$('#flash').remove();	
			}
			
		});
	} else if (data.result=="ff_forward"){
		if(data.multiple_items == "true"){
			//dumpProps(data);
			var ids = '';
			$.each(data.ids, function(i, id){
				ids += id+'/';
			});
			$('.fb_content').load('/items/find_first_view/'+ids);
			$('#submit').attr('disabled', '');
			waiting = false;
		} else {
			$('.fb_content').load('/items/find_first_view/'+data.item_id);
			$('#submit').attr('disabled', '');
			waiting = false;
		}
	} else if (data.result=="merge_forward"){
		$('.fb_content').load('/items/merge_view/'+data.item_id);
		$('#submit').attr('disabled', '');
		waiting = false;
	} else if ( data.result=="successful_redirect") {
		window.location = data.url;
	}
}

function highlightRow(){
  $('tr.modified_row').css('backgroundColor', '#F4D99D').delay(600).animate({"backgroundColor": "white"}, 2000);
	if($('tr.modified_row').length){
		$('html, body').animate({
			scrollTop: $('tr.modified_row').offset().top - 100
		}, 500);
	}
  
}

$.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

$.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

$.fn.dataTableExt.aTypes.push( function ( sData ){
	sData = typeof sData.replace == 'function' ? sData.replace( /<.*?>/g, "" ) : sData;
	
	var sValidFirstChars = "0123456789-";
	var sValidChars = "0123456789.";
	var Char;
	var bDecimal = false;
	
	/* Check for a valid first char (no period and allow negatives) */
	Char = sData.charAt(0); 
	if (sValidFirstChars.indexOf(Char) == -1) {
		return null;
	}
	
	/* Check all the other characters are valid */
	for ( var i=1 ; i<sData.length ; i++ ) {
		Char = sData.charAt(i); 
		if (sValidChars.indexOf(Char) == -1) {
			return null;
		}
		
		/* Only allowed one decimal place... */
		if (Char == "."){
			if (bDecimal){return null;}
			bDecimal = true;
		}
	}
	return 'num-html';
});

function dumpProps(obj, parent) {
   // Go through all the properties of the passed-in object 
   for (var i in obj) {
      // if a parent (2nd parameter) was passed in, then use that to 
      // build the message. Message includes i (the object's property name) 
      // then the object's property value on a new line 
      if (parent) { msg = parent + "." + i + "\n" + obj[i]; } else { var msg = i + "\n" + obj[i]; }
      // Display the message. If the user clicks "OK", then continue. If they 
      // click "CANCEL" then quit this level of recursion 
      if (!confirm(msg)) { return; }
      // If this property (i) is an object, then recursively process the object 
      if (typeof obj[i] == "object") { 
         if (parent) { dumpProps(obj[i], parent + "." + i); } else { dumpProps(obj[i], i); }
      }
   }
}							


function urlencode (str) {
    str = (str+'').toString();    
    // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
    // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+').replace(/~/g, '%7E');
}

function checkrange(id,capacity)
{
	var from=document.getElementById(id+'_from').value;
		var to=document.getElementById(id+'_to').value;
		if(from-to>500)
		{
			alert("Cannot print more than 500 tabs at once. Please select a value less than 500.");
			$('#'+id+'_from').attr("value",capacity);
			$('#'+id+'_to').attr("value","1");
			return false;
		}
		
	else
		return true;
}

var checkbox_counter=0;
function enableLabel(id, capacity, name){
	
	if($('#location_'+id).is(':checked')){
		$('#'+id+'_from').attr("disabled","");
		$('#'+id+'_from').attr("value",capacity);
		$('#'+id+'_to').attr("disabled", "");
		$('#'+id+'_to').attr("value","1");
		$('#'+'through_'+id).attr('style','color:black');
		$('#dd_location_'+id).toggle();
		checkbox_counter++;
		if(name>14)
		{
			$('#hidden_location_'+id).toggle();

}
		var from=document.getElementById(id+'_from').value;
		var to=document.getElementById(id+'_to').value;
		if(from-to>500)

{
			alert("Cannot print more tha 500 tabs at once. Please select a value less than 500.");
			$('#'+id+'_from').attr("value",capacity);
			$('#'+id+'_to').attr("value","1");
			
		}
	}

else{
		$('#'+id+'_from').attr("disabled","disabled");
		$('#'+id+'_from').attr("value","");
		$('#'+id+'_to').attr("disabled", "disabled");
		$('#'+id+'_to').attr("value","");
		$('#'+'through_'+id).attr('style','color:lightgray');
		$('#dd_location_'+id).toggle();
		if(name>14)
		{
			$('#hidden_location_'+id).toggle();
		}	
		checkbox_counter--;	
	}
}

function css_browser_selector(u){var ua = u.toLowerCase(),is=function(t){return ua.indexOf(t)>-1;},g='gecko',w='webkit',s='safari',o='opera',h=document.documentElement,b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?'mobile':is('iphone')?'iphone':is('ipod')?'ipod':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win':is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js']; c = b.join(' '); h.className += ' '+c; return c;}; css_browser_selector(navigator.userAgent);
