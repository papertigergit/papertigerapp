// Adjust the height of the viewport according to window height
var adjusted_window_hegiht;
var top_offset = 190;

$(window).resize(function () {
    adjusted_window_height = $(window).height() - top_offset;
    var px_value = adjusted_window_height + 'px';
    $('div.col2').css('height', px_value);
});

// Create the tooltips only on document load
$(document).ready(function () {
    //VARIABLES
    var action_name;
    // var parent_container;
    // Adjust window size on load
    adjusted_window_height = $(window).height() - top_offset;
    var px_value = adjusted_window_height + 'px';
    $('div.col2').css('height', px_value);

    var file_quantity = $('.file').length;
    var file_width = 413;
    var horizontal_container_width = (file_quantity * file_width) + 'px';
    $('#files_horizontal_container').css('width', horizontal_container_width);

    $(".file").hover(

    function () { //hover in
        $(".single_file_action_tools", this).show();
    }, function () { //hover out
        $(".single_file_action_tools", this).hide();
    });

    $(".single_file_action_tools .tool").hover(

    function () { //hover in
        action_name = $("img", this).attr("alt");
        $(this).css("background", "white");
        $(".tool_label", this).text(action_name);
    }, function () { //hover out
        $(this).css("background", "inherit");
        $(".tool_label", this).text("");
        //TODO:hover timeout!
    });

    //Hide popups when clicked outside of them
    $('body').click(function () {
        $('.dropdown').hide();
        $(':button').removeClass("pressed");
    });

    //If we click the button or inside the popup, don't hide it
    $('#label_button, #checkbox_button').click(function (event) {
        event.stopPropagation();
    });

    $("#label_button").bind("click", function () {
        // $(this).toogleClass("active");
        $(':button').removeClass("pressed");
        $('.dropdown').hide();
        $('#labels_dropdown').toggle();
        $(this).toggleClass("pressed");
    });

    $("#checkbox_button").bind("click", function () {
        // $(this).toogleClass("active");
        $(':button').removeClass("pressed");
        $('.dropdown').hide();
        $('#checkbox_dropdown').toggle();
        $(this).toggleClass("pressed");
    });

    //Turn vertical scroll into horizontal scroll!
    $('#files').mousewheel(function (event, delta) {
        this.scrollLeft -= (delta * 30);
        event.preventDefault();
    });

  
  //TODO: FOR THE LOVE OF GOD PLEASE CLEAN THIS UP!

   $('#select_all').click(function(){
     selectAllCheckboxes();
     //TODO: Add functionality to select all items that match the current view
     });

   $('#select_none').click(function(){
     selectNoneCheckboxes();
     });

   $('#select_inverse').click(function(){
     selectInverseCheckboxes();
     });

    function selectAllCheckboxes(){
      var checkBoxes = $("#files :checkbox");
      checkBoxes.attr("checked", "checked");    
    }

    function selectNoneCheckboxes(){
      var checkBoxes = $("#files :checkbox");
      checkBoxes.removeAttr("checked");    
    }

    function selectInverseCheckboxes(){
      $('#files :checkbox').each(function(){
        if ($(this).attr("checked") === true){
          $(this).removeAttr("checked");
          console.log("This checkbox is checked, we should uncheck it!");
          console.log($(this).attr("checked"));
        }
        else if ($(this).attr("checked") === false){
          $(this).attr("checked", "checked");
          console.log("This checkbox is not checked, we should check it!");
          console.log($(this).attr("checked"));
        }
      });
    }
});




