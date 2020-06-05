$(document).ready(function() {  

$('div#utilities').hide();
$('.choose_expert_profile').hide(); 
  $('div.read_more_expert').on("click",function () {
	var ind_id = $(this).attr('id');
    $('#choose_expert_profile'+ind_id).toggle('slow');
    $(this).text($(this).text() == 'hide' ? 'read more' : 'hide');
    return false;
  });

  // bind "click" event for links with title="submit"
  var $choose_expert = $("a[title=submit]");
  
  $choose_expert.click( function(){
  var inds_id = $(this).attr('id');
/*   	e.preventDefault();      */
  var $form = $( this ), term = $( 'input' ).val(), url = $form.attr( 'action' );
  var $submit_expert = $(this).parents("form");
  $('this input:radio[name=expert_code]').checked = true; 

     $.ajax({
			type: "POST",
      url: url,
      data: inds_id,
      success: function() {
      alert("Your expert has been notified, and will contact you as soon as possible via email to schedule your initial phone consultation. In the meantime, feel free to contact your chosen expert directly. Please note that the expert may offer an initial complimentary 15-30 minute phone consultation, but you will negotiate with the expert directly for any additional consultation time and services including customizing a personal work-flow solution to help you get your office organized and working more productively, and will be able to help you set up Paper Tiger fast and use Paper Tiger more efficiently. Thank you for using Paper Tiger!");
      $submit_expert.submit();
      }      
    });            
 //   return false;
  });

//how much items per page to show
var show_per_page = 5;
//getting the amount of elements inside content div
var number_of_items = $('.choose_expert').children().size();
//calculate the number of pages we are going to have
var number_of_pages = Math.ceil(number_of_items/show_per_page);

//set the value of our hidden input fields
$('#current_page').val(0);
$('#show_per_page').val(show_per_page);
gotopage = $('#current_page').val(0);

//now when we got all we need for the navigation let's make it '

/*
what are we going to have in the navigation?
	- link to previous page
	- links to specific pages
	- link to next page
*/
var navigation_html = '<a class="previous_link" href="javascript:prev();">Prev</a>';
var current_link = 0;
while(number_of_pages > current_link){
	navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';
	current_link++;
}
navigation_html += '<a class="next_link" href="javascript:next();">Next</a>';

$('#page_navigation').html(navigation_html);

//add active_page class to the first page link
$('#page_navigation .page_link:first').addClass('active_page');

//hide all the elements inside content div
$('.choose_expert').children().css('display', 'none');

//and show the first n (show_per_page) elements
$('.choose_expert').children().slice(0, show_per_page).css('display', 'block');


function prev(){

	new_page = parseInt($('#current_page').val()) - 1;
	//if there is an item before the current active link run the function
	if($('.active_page').prev('.page_link').length==true){
		go_to_page(new_page);
	}

}

function next(){
	new_page = parseInt($('#current_page').val()) + 1;
	//if there is an item after the current active link run the function
	if($('.active_page').next('.page_link').length==true){
		go_to_page(new_page);
	}

}
function go_to_page(page_num){
	//get the number of items shown per page
	var show_per_page = parseInt($('#show_per_page').val());

	//get the element number where to start the slice from
	start_from = page_num * show_per_page;

	//get the element number where to end the slice
	end_on = start_from + show_per_page;

	//hide all children elements of content div, get specific items and show them
	$('.choose_expert').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');

	/*get the page link that has longdesc attribute of the current page and add active_page class to it
	and remove that class from previously active page link*/
	$('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');

	//update the current page input field
	$('#current_page').val(page_num);
}

$("div#page_navigation a.page_link").click(function(){
var gotopage = $(this).text();
var gotopage = gotopage - 1;
go_to_page(gotopage);
});

$("div#page_navigation a.previous_link").click(function(){
prev();
});
  
$("div#page_navigation a.next_link").click(function(){
next();
});



var $affiliate_request = $("input#submit.button.nice.white");

$('input#submit.aff_approve.button.nice.white').on("click", function(e){
  e.preventDefault();
   alert("An email has been sent to Paper Tiger's office team for approval.  We will contact you with further details as soon as possible.  In the meantime, if you have questions, feel free to email leads@thepapertiger.com or call at 404 478-6413, ext 1.");
});

// START Dynamic state/province Dropdown //
//

$("#source_usa").hide();
$("#source_can").hide();

$('#country_list').on("change", function() {
  var self = $(this).val();
  var country = self;
  var newCountry = "." + country;
  var newSelector = "div" + newCountry + ".expert_blurb";

  if (self == "Canada"){
    $('div.expert_blurb').not(newCountry).hide();
    $(newSelector).each(function(){$(this).show();})
    $("#source_can").show();
    $("#source_usa").hide();
  }else if (self == "all"){
    $('div.expert_blurb').show();
    $("#source_can").hide();
    $("#source_usa").hide();
  }else if (self == "International"){
    $('div.expert_blurb').show();
    $('div.expert_blurb.United_States').hide();
    $('div.expert_blurb.Canada').hide();
    $("#source_can").hide();
    $("#source_usa").hide();
  }else if (self == "United_States"){
    $('div.expert_blurb').not(newCountry).hide();
    $(newSelector).each(function(){$(this).show();})
    $("#source_usa").show();
    $("#source_can").hide();
  } 
});

$('#source_can').on("change", function() {
  if ($(this).val() != "all") {
    var state = $(this).val();
    var newState = "#" + state;
    var newSelector = "div" + newState + ".expert_blurb";
    $('div.expert_blurb').not(newState).hide();
    $(newSelector).each(function(){$(this).show();})
  }
  else if($(this).val() == "all"){
    $('div.expert_blurb').each(function(){$(this).show();})
    $('div.expert_blurb').not(".Canada").hide();
  } 
});

$('#source_usa').on("change", function() {
  if ($(this).val() != "all") {
    var state = $(this).val();
    var newState = "#" + state;
    var newSelector = "div" + newState + ".expert_blurb";
    $('div.expert_blurb').not(newState).hide();
    $(newSelector).each(function(){$(this).show();})
  }
  else if($(this).val() == "all"){
    $('div.expert_blurb').each(function(){$(this).show();})
    $('div.expert_blurb').not(".United_States").hide();
  } 
});
// END Dynamic state/province Dropdown //


});// End of JS file
