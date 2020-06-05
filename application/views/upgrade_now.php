<style>

#upgrade_now_popup h2{
	font-size:1.3em;
	margin-bottom: 15px;
}

#upgrade_now_popup p{
	font-family: Arial, "MS Trebuchet", sans-serif;
	font-size:.9em;
}

#upgrade_list{
	list-style:none;
	margin: 20px 0;
}

#upgrade_list li{
	border-top: 1px solid #999;
	padding: 10px 0;
	padding-left: 40px;
	background-image:url(<?=site_url()?>img/clear.png);
	background-position:10px center;
	background-repeat:no-repeat;
	cursor: pointer;
}

#upgrade_list li.last{
	border-bottom: 1px solid #999;
}

.hover {
	background:lightgreen;
}

</style>

<script>

$(document).ready(function() {


// Define corners and opposites arrays
var corners = [
   'bottomLeft', 'bottomRight', 'bottomMiddle',
   'topRight', 'topLeft', 'topMiddle',
   'leftMiddle', 'leftTop', 'leftBottom',
   'rightMiddle', 'rightBottom', 'rightTop'
];
var opposites = [
   'topRight', 'topLeft', 'topMiddle',
   'bottomLeft', 'bottomRight', 'bottomMiddle',
   'rightMiddle', 'rightBottom', 'rightTop',
   'leftMiddle', 'leftTop', 'leftBottom'
];


 
 // Destroy currrent tooltip if present
 if($(this).data("qtip")) $(this).qtip("destroy");
	
	$("#upgrade_list li").hover(
	  function () {
	    $(this).addClass('hover');
	  }, 
	  function () {
	    $(this).removeClass('hover');
	  }
	);

  $("#upgrade_list li").qtip({
       style: {
          border: {
             width: 3,
             radius: 10
          },
          padding: 10, 
          textAlign: 'left',
          tip: true, // Give it a speech bubble tip with automatic corner detection
          name: 'dark' // Style it according to the preset 'cream' style
       }
    });


});						   
</script>

<div id="upgrade_now_popup">
	<div id="facebox_header"><div>This feature is not available on your Paper Tiger plan!</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<div id="facebox_body">
		
			<h2>Upgrade to Paper Tiger Pro!</h2>
			<?php
			global $user_data;
			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			?>
			
			<p>This feature is not available on your current Paper Tiger plan. Upgrade now and get these great features:</p>
			
			<ul id="upgrade_list">
			<?php
			if($package_id == 10 || $package_id == 9) { ?>
			<?php } else { ?>
				<li title="You will be able to print directly on Avery tabs that you can snap off and install. It's a real time saver!">Avery templates for printing tabs/labels</li>
			<?php } ?>	
				<li title="Import your existing Paper Tiger 1.X, 2.X, 3.X, or 4.X database and use your existing data.">Import your existing Paper Tiger Desktop databases</li>
				<li title="Use all the space you need with no limit on databases and locations.">Unlimited databases and locations</li>
				<li title="Use advanced search to find items in specific locations, categories, or by action dates.">Advanced searching capabilities</li>
			<?php
			if($package_id == 10 || $package_id == 9) { ?>
			<?php } else { ?>
				<li title="File an ulimited number of items.">Unlimited items</li>				
				<li title="Transfers allow you to move, duplicate, and merge your items and easily keep track of the process.">Powerful transfers to move, duplicate, and merge items</li>
				<li title="Use reports to manage your transfers, maintain your items, and more!">Full-featured reports</li>
				<li class="last" title="Find First warns you if you are about to file an item that already exists, reducing duplicate information.">Find-first</li>				
			<?php } ?>
			</ul>			
			
			<h2 style="text-align:center; font-size:1.7em;"><a href="<?=site_url();?>account/">Go Pro for $9/month or $90/year!</h2>
			
		
		</div>
		<div id="facebox_footer">
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Go Back</a></div>
				</div>
				<div class="clear"></div>
		</div>
	</div>
</div>
