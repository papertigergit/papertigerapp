<style>
p, ol, #db_import_tool li, #db_import_tool label {font-family:arial; font-size:.9em; margin-bottom:1em;}
#db_import_tool img{border:solid 1px gray;}

h4.name, h5.database, h6.location{
	float:left;
	margin-right: 20px;
	width:150px;
}

h5.database {
	margin-left:20px;
}

h6.location {
	float:left;
	margin-left:40px;
	color:gray;
}

ul h3 {
	font-weight:normal;
}

p.description {
	display:inline;
}

h3.user_name{
	display:block;
	width:220px;
	float:left;
	font-weight:normal;
}


li.database_container {
	border-bottom:solid 1px gray;
}

li.location:nth-child(even) {
	background:white;
}

li.location{
	border-top:solid 1px gray;
}

input[type=checkbox]{
	margin-left:20px;
}

ul.triState {
	width:100px;
	float:left;
	display:block;
}

ul.triState2 {
	display:block;
	width:470px;
}

div.user_permissions_container {
	padding-top:10px;
	padding-left:10px;
	font-family: arial;
	background:#ececec;
}

ul.databases_list_read, ul.databases_list_write, ul.locations_container{
	display:none;
}

ul.triState li {
	padding-bottom:8px;
}

ul.triState li label {
	float:left;
}

span.all_dbs{
	margin-left:25px;
	font-weight:bold;
}

span.db_name{
	margin-left:25px;
	font-weight:bold;
}

span.loc_name{
	margin-left:25px;
	font-weight:normal;
	color:gray;
}

a.expand_link{
	display:inline;
	font-size:.8em;
	margin-left:20px;
}

ul.triState ul{
	border-top:solid 1px gray;
	margin-top:5px;
	padding-top:10px;
}

ul.triState ul ul{
	border-top:none;
	margin-top:0;
	padding-top:8px;
}

li.locations_container{
	margin-bottom:5px;
}

ul.locations_container li {
	display:block;
	margin-bottom:8px;
}

#footnote {
	padding-top:10px;
	font-size:.9em;
}

.inputs_container {
	font-family:arial;
	display:block;
	padding: 10px 5px;
	background:#ececec;
	margin-bottom:20px;
}

.inputs_container input[type=text], .inputs_container input[type=password] {
	height:20px;
	width:400px;
	font-size:1em;
	font-family:arial;
}

.inputs_container label {
	display:inline-block;
	width:100px;
	margin-bottom:20px;
	font-weight:bold;
	font-size:.9em;
}

.inputs_container label.last {
	margin-bottom:0;
}

h3.db_tools_subheading {
	margin-bottom:10px;
	font-size:1.3em;
}


#db_tools_page #submit_container {
	padding-top:10px;
	border-top: solid 2px black;
	font-family:arial;
}

</style>

	<div id="db_tools_page_wrapper">
	
		<div id="db_tools_page">			
				
				<h2>Edit Affiliate - <em><?=$affiliate->first_name;?> <?=$affiliate->last_name;?></em></h2>
				
				<p>Here you can affiliate information for this user.</p>

<?php echo validation_errors(); ?>

<form action="<?=site_url()?>admin/affiliates/edit/<?=$affiliate_code?>" method="POST" name="edit_affiliate">
	
				<!-- <p>Change this user's name</p> -->
				<h3 class="db_tools_subheading">Affiliate Information:</h3>
				<div class="inputs_container">
				
		          <label for="affiliate_first_name">First Name</label>
		          <input type="text" class="inline_input input" name="affiliate_first_name" id="affiliate_first_name" value="<?php echo set_value('affiliate[first_name]', $affiliate->affiliate_first_name); ?>"/><br>
		          
		          <label for="affiliate_last_name">Last Name</label>
		          <input type="text" class="inline_input input" name="affiliate_last_name" id="affiliate_last_name" value="<?php echo set_value('affiliate[last_name]', $affiliate->affiliate_last_name); ?>" /><br>
		          
		          <label for="affiliate_street">Street</label>
		          <input type="text" class="inline_input input" name="affiliate_street" id="affiliate_street" value="<?php echo set_value('affiliate[street]', $affiliate->affiliate_street); ?>" /><br>
		          
		          <label for="affiliate_street2">Street 2</label>
		          <input type="text" class="inline_input input" name="affiliate_street2" id="affiliate_street2" value="<?php echo set_value('affiliate[street2]', $affiliate->affiliate_street_2); ?>" /><br>
		          
		          <label for="affiliate_city">City</label>
		          <input type="text" class="inline_input input" name="affiliate_city" id="affiliate_city" value="<?php echo set_value('affiliate[city]', $affiliate->affiliate_city); ?>" /><br>
		          
		          <label for="affiliate_state">State</label>
		          <input type="text" class="inline_input input" name="affiliate_state" id="affiliate_state" value="<?php echo set_value('affiliate[state]', $affiliate->affiliate_state); ?>" /><br>
		          
		          <label for="affiliate_zip">Zip</label>
		          <input type="text" class="inline_input input" name="affiliate_zip" id="affiliate_zip" value="<?php echo set_value('affiliate[zip]', $affiliate->affiliate_zip); ?>" /><br>
		          
		          <label for="affiliate_tax">Tax ID</label>
		          <input type="text" class="inline_input input" name="affiliate_tax" id="affiliate_tax" value="<?php echo set_value('affiliate[tax]', $affiliate->affiliate_tax); ?>" /><br>
		          
		          <label for="affiliate_level">Affiliate Level</label>
		          <input type="text" class="inline_input input" name="affiliate_level" id="affiliate_level" value="<?php echo set_value('affiliate[level]', $affiliate->affiliate_level); ?>" /><br>

              <label for="affiliate_level">Affiliate Code</label>
		          <input type="text" class="inline_input input" name="affiliate_code" id="affiliate_code" value="<?php echo set_value('affiliate[code]', $affiliate->affiliate_code); ?>" /><br>

		          
				  <label class="last" for="affiliate_paypal">PayPal E-Mail</label>
		          <input type="text" class="inline_input input" name="affiliate_paypal" id="affiliate_paypal" value="<?php echo set_value('affiliate[paypal]', $affiliate->affiliate_paypal); ?>" /><br>
                </div> <!-- .inputs_container -->


      
				
				<div id="result"></div>
			<div id="submit_container">	<span><input type="submit" name="submit" id="submit" value="Save affiliate information"/></span><span class="small gray"><span>or</span><a href="<?='/admin/affiliates/view/'.$affiliate_code?>" class="close red">Cancel</a></span></div>
			
			<div class="clear"></div>
			
						</form>
						
		</div> <!-- #db_tools -->
	
	</div> <!-- #db_tools_wrapper -->	
