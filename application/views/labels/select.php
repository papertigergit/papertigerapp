<script type="text/javascript" src="<?=site_url()?>js/jquery.NobleCount.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>


<?php if($locations->num_rows() > 0): ?>

<script type="text/javascript">

function print_label(id)
{
$(document).ready(function()
{
$('#p_name_'+id).NobleCount('#count_'+id,{max_chars: 14});
});

}
var maxl=13;
function limit_char(taObj) {
	if (taObj.value.length>maxl) 
	{
		//alert('Limit Reached');
		maxl++;
	}
	else
	 {
		maxl=13;
		return true;
		}
}
function chk_template()
{
	
	if(checkbox_counter<=0)
	{
		alert("Please Check atleast one tab");
		return false;
	}
	if (document.getElementById('label_type').value=='null')
	{
		alert("Please Select a Template. " );
		return false;
	}	
	
	
}


</script>
<div id="db_tools_page_wrapper">
	<div id="db_tools_page">
		<h2>Print tabs and labels for your file folders</h2>		
		<?php if($this->session->flashdata('labels_error')): ?>
		<div id="flash_error"><?php echo $this->session->flashdata('labels_error') ?></div>
		<?php endif; ?>
	<form action="<?=site_url()?>labels/generate" method="POST" id="import_db_form" name="label_form" id="label_form" target="_blank">
			<h3>First, select the template you would like to use:</h3><br />
			<div class ="section">
			<div id="dropdown" style="text-align:left;float:left;">
			<?php
			$enabled='';
			$package_text='';
			foreach($packages->result() as $package) {
				if($package->labels == 0) {
					$enabled="disabled";
					$package_text="Disabled, upgrade to Pro!";
				}
			}
			?>
				<select id="label_type" name="label_type">
				<!--	<option class="preview" value="null"> Select </option>
					<option class="preview" value="5567">Avery File Folder Labels 5567 (Template 5567) </option>-->
					<option class="preview" value="1000" > Self Cut Tabs </option>
					<option class="preview" value="5366" <?=$enabled?>>Avery File Folder Labels 5366 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="5167" <?=$enabled?>>Avery File Folder Labels 5167 (Template 8167) <?=$package_text?></option>
					<option class="preview" value="5267" <?=$enabled?>>Avery File Folder Labels 5267 (Template 8167) <?=$package_text?></option>
					<option class="preview" value="8167" <?=$enabled?>>Avery File Folder Labels 8167 (Template 8167) <?=$package_text?></option>
					<option class="preview" value="5160" <?=$enabled?>>Avery File Folder Labels 5160 (Template 8160) <?=$package_text?></option>
			<!--//		<option class="preview" value="11136" <?php //=$enabled?>>Avery File Folder Labels 11136 (Template 11136) <?php //=$package_text?></option>  
					<option class="preview" value="11137" <?php //=$enabled?>>Avery File Folder Labels 11137 (Template 11137) <?php //=$package_text?></option> -->
					<option class="preview" value="5266"  <?=$enabled?>>Avery File Folder Labels 5266 (Template 5066) <?=$package_text?></option>
					<option class="preview" value="8066"  <?=$enabled?>>Avery File Folder Labels 8066 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="5066" <?=$enabled?>>Avery File Folder Labels 5066 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="5766"  <?=$enabled?>>Avery File Folder Labels 5766 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="5866"  <?=$enabled?>>Avery File Folder Labels 5866 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="5966" <?=$enabled?>>Avery File Folder Labels 5966 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="6466" <?=$enabled?>>Avery File Folder Labels 6466 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="8366" <?=$enabled?>>Avery File Folder Labels 8366 (Template 5366) <?=$package_text?></option>
					<option class="preview" value="45366" <?=$enabled?>>Avery File Folder Labels 45366 (Template 5366) <?=$package_text?></option>
				</select>
			</div>
			<br><br>
		</div>
		<br>
		
			<h3>Second, enter the range of items to print:</h3>
			<div class="section">
				
					<div id="form_padding">
						<div class="form_divider">
							<p>The file folder tabs will print in numeric order based on your entries below.  You can print enough tabs for the entire location now (i.e. the entire file cabinet) or print just want you need.  You can always print more tabs at a later date.  Your tabs will be generated in .pdf format and be ready for you to download, print, cut, and install!</p><br />
						</div>
						<?php foreach($locations->result() as $location): ?>
						<div class="form_divider" >
							<input type="checkbox" name="location_<?php echo $location->id; ?>" id="location_<?php echo $location->id; ?>" class="labels_checkbox" onclick="enableLabel('<?php echo $location->id; ?>', '<?php echo $location->capacity; ?>', '<?php echo strlen($location->name); ?>')"/><label for="location_<?php echo $location->id; ?>"><?php echo htmlspecialchars($location->name); ?> (Max <?php echo $location->capacity; ?>)</label>
							<div style="float:right;">
								<input type="text" name="<?php echo $location->id; ?>_from" id="<?php echo $location->id; ?>_to" style="width:100px;" value="" disabled="disabled" onBlur="return checkrange('<?php echo $location->id?>','<?php echo $location->capacity ?>')"/>
								&nbsp;<span style="color:lightgray" id="through_<?php echo $location->id; ?>">through</span>&nbsp;
								<input type="text" name="<?php echo $location->id; ?>_to" id="<?php echo $location->id; ?>_from" style="width:100px;" value="" disabled="disabled" onBlur="return checkrange('<?php echo $location->id?>','<?php echo $location->capacity ?>')"/>  
							</div>
							<div name="hidden_location_<?php echo $location->id;?>" id="dd_location_<?php echo $location->id;?>" style="display:none">
								Print: <select name="print_label_<?php echo $location->id;?>">
									<option value='title'>Item title</option>
									<option value='location'>Location</option>			
									<option value='all'>Item title & Location</option>			
								<select>
							</div>
							<div name="hidden_location_<?php echo $location->id;?>" id="hidden_location_<?php echo $location->id;?>" class="labels_checkbox_long_name" style="display:none">
								<h4 style="color:red;"> Name Too Long </h4>
								<p> Please Enter name (not more then 14 characters) <input type="text" name="p_name_<?php echo $location->id;?>" id="p_name_<?php echo $location->id;?>" width="25"  onKeyPress="return limit_char(this)" onKeyUp="print_label('<?php echo $location->id; ?>')"> 
								
								<b><span id="count_<?php echo $location->id;?>" > 14</span></b>
								</p>
							</div>
							<div class="clear"></div>
						</div>
						<?php endforeach; ?>
					</div>
					<div id="result"></div>
			</div>	
					
					<div id="submit_container" class="db_import_submit">
					
						<span><input type="submit" name="submit" id="submit" value="Generate Tabs PDF" onClick="return chk_template()"/></span>
						<span class="small gray"><span>or</span><a href="/locations/" class="close red">Cancel</a></span>
					</div>
					<!--<input type="hidden" name="edit_location" value="true"/>-->
				</form>
				
	</div>
</div>					
				
				<?php endif; ?>
