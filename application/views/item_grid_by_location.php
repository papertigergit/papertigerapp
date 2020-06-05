<style>
.load_items_trigger {
  display:block; width:100%; text-align:center; background:#C79200; cursor:pointer; height:40px; font-size:22px; font-weight:bold; letter-spacing: -.06em; padding-top:15px; color:white;
}
.load_items_trigger:hover{
  color:lightgray;
}
</style>
<?php
			$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			$show_notes=$this->Settings_model->get_all();
			$notes_settings=array($show_notes->result_array());
			$loc_permissions=$this->Permissions_model->get_by_location($this_location->id,$this->user_data['id']);
			$can_edit="edit_false";
			if($loc_permissions) {
			if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3 || ($organization_admin == $this->user_data['id'])) {
			    $can_edit="edit_true";
			  }
			}
			if($organization_admin == $this->user_data['id']):
			    $can_edit="edit_true";
			endif;	


		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		if($is_network->num_rows() == 0):
			$database_permissions=3;
		        $can_edit="edit_true";
		endif;
	?>
	    									  
	<div id="table_header_bar" class="header_items">
		<div id="header_bar_left"><?=htmlspecialchars($this_location->name)?> &mdash; 
			<?php if($this_location->empty_slots!=0){
				$slots = $this_location->empty_slots==1?' open item':' open items';
				echo $this_location->empty_slots.$slots.' available ';
				$location_full = false;	
                if ($can_edit == "edit_false"){echo '<span style="color:gray">(read only)</span>';}
			} else {
				$location_full = true;?>
				<span class="orange">No open items available</span>
                <?php if ($can_edit == "edit_true"):?>
				<span class="small" id="header_link"><a alt="Modify Location Capacity" rel="facebox nm" href="<?=site_url()?>locations/modify_capacity/<?=$this_location->id?>">Add more capacity...</a></span>
                <?php endif;?>
			<?php }?>
        <?php if($order_by != 'slot_num'): ?>
            <span class="small" style="color:#C79200;">Empty items only displayed when sorting by Item Number</span>
        <?php endif;?>
			</div>
		<div id="header_bar_right">
			<select id="location_jump">
				<option value="0">Jump to a location...</option>
				<?php foreach($locations->result() as $row){?>
				<option value="<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name), 18, '...')?></option>
				<?php }?>
				<option value="trash">Trash</option>
			</select>
            <?php if ($can_edit == "edit_true"){ ?>
			<a <?php if(!$location_full){?>href="<?=site_url()?>items/add/<?=$this_location->id?>/first/<?=$order_by?>/<?=$order?>"<?php }?> class="sprite <?=$location_full?'new_button_disabled':'new_button'?>" alt="New" <?=$location_full?'':'rel="facebox nm"'?>></a>
			<a href="<?=site_url()?>items/edit/<?=$order_by?>/<?=$order?>/" class="sprite edit edit_button_disabled" value="<?=$this_location->id?>" alt="Edit" rel="facebox nm"></a>
			<span style="position:relative">
				<span class="sprite transfer transfer_button_disabled" value="<?=$this_location->id?>" alt="Transfer"></span>
				<div class="submenu">
					<ul>

					<?php 
					global $user_data;
					$packages = $this->Package_model->get_by_id($package_id=$this->Organization_model->get_package_id($user_data['organization_id']));
					if($packages->num_rows() == 0)
					{
						?>
							<li><a id="move" class="submenu_link" href="items/upgrade" rel="facebox nm">move</a></li>
							<li><a id="merge" class="submenu_link" href="items/upgrade" rel="facebox nm">merge</a></li>
							<li><a id="dupe" class="submenu_link" href="items/upgrade" rel="facebox nm">duplicate</a></li>
						<?php
					}
					?>
					<?php
					$package=(array($packages->result()));
					if($package[0][0]->allow_transfers == 0)
					{
						?>
							<li><a class="submenu_link" href="<?=site_url()?>items/upgrade" rel="facebox nm">move</a></li>
							<li><a  class="submenu_link" href="<?=site_url()?>items/upgrade" rel="facebox nm">merge</a></li>
							<li><a class="submenu_link" href="<?=site_url()?>items/upgrade" rel="facebox nm">duplicate</a></li>
						<?php
					}
					if($package[0][0]->allow_transfers == 1)
					{
						?>
						<li><a id="move" class="submenu_link" href="" rel="facebox nm">move</a></li>
						<li><a id="merge" class="submenu_link" href="" rel="facebox nm">merge</a></li>
						<li><a id="dupe" class="submenu_link" href="" rel="facebox nm">duplicate</a></li>
						<?php
					}
					?>

					</ul>
				</div>
			</span>
			<a href="<?=site_url()?>items/delete/" class="sprite toss toss_button_disabled" alt="Delete" rel="facebox modal"></a>
            <?php } ?>
		</div>
		<div class="clear"></div>
	</div>
    <?php if($page_number > 1):?>
<div class="load_items_trigger" id="load_previous">Load Previous Items</div>
<?php endif;?>
	<?php
			$table_id="item_table_without_notes";
	if($show_notes->num_rows() > 0) {
	if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
		{
			$table_id="item_table";
		}
	}
	?>
	<table cellspacing="0" id="<?=$table_id?>" class="grid items">
		<thead>
		
<?php

	$item_name_order = 'desc';
	$item_number_order = 'desc';
	$item_category_order = 'desc';
	$item_action_order = 'desc';
	$item_status_order = 'desc';

	$item_name_sort = FALSE;
	$item_number_sort = FALSE;
	$item_category_sort = FALSE;
	$item_action_sort = FALSE;
	$item_status_sort = FALSE;
	if(!isset($order_by)) {
		$order_by="slot_num";
	}
	if(!isset($order)) {
		$order="asc";
	}
	if(!isset($page_number)) {
		$page_number=1;
	}

switch ($order_by) {
	case 'slot_num':
		$item_number_sort = TRUE;
		if ($order == 'asc'){
			$item_number_order = 'asc';	
	}
		if ($order == 'desc'){
			$item_number_order = 'desc';	
	}
	break;
	case 'name':
		$item_name_sort = TRUE;
		if ($order == 'asc'){
			$item_name_order = 'asc';	
	}
		if ($order == 'desc'){
			$item_name_order = 'desc';	
	}
	break;
	case 'category':
		$item_category_sort = TRUE;
		if ($order == 'asc'){
			$item_category_order = 'asc';	
	}
		if ($order == 'desc'){
			$item_category_order = 'desc';	
	}
	break;
	case 'action_date':
		$item_action_sort = TRUE;
		if ($order == 'asc'){
			$item_action_order = 'asc';	
	}
		if ($order == 'desc'){
			$item_action_order = 'desc';	
	}
	break;
	case 'status':
		$item_status_sort = TRUE;
		if ($order == 'asc'){
			$item_status_order = 'asc';	
	}
		if ($order == 'desc'){
			$item_status_order = 'desc';	
	}
	break;
}	

?> 

		<tr class="th_row">
		<th class="header_itemNumber sorting<?php if ( $item_number_sort === TRUE ) { echo '_'.$item_number_order;}?>">Item</th>
			<th class="header_itemName  sorting<?php if ( $item_name_sort === TRUE ) { echo '_'.$item_name_order;}?>">Item Name</th>
			<th class="header_itemKeywords">Keywords</th>
			<?php
			if($show_notes->num_rows() > 0) {
			if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
			{
				?>
				<th class="header_itemDescription">Notes</th>
				<?php
			}}
			?>
			<th class="header_itemCategory sorting<?php if ( $item_category_sort === TRUE ) { echo '_'.$item_category_order;}?>">Category</th>
			<th class="header_itemActionDate  sorting<?php if ( $item_action_sort === TRUE ) { echo '_'.$item_action_order;}?>">Action Date</th>
			<th class="header_itemStatus">Status</th>
		</tr>
		
	</thead>
		<?php $next_row = $table_data->row();?>	
		<?php $page_size=$this->config->item('page_size'); ?>
		<?php 
			$total_pages=ceil($this_location->capacity/$page_size);
			if($page_number == null || $page_number == 1) {
				$start_slot=1;
				if($order_by=="slot_num" && $order=="desc") {
					$start_slot=$this_location->capacity;
				}
			} else {
				$start_slot = ((($page_number-1)*$page_size)+1); 
				if($order_by=="slot_num" && $order=="desc") {
					$start_slot=($this_location->capacity) - ($page_size * ($page_number-1));
				}
			}
			if($page_number == $total_pages) {
				$total_slots = $page_size  - (($total_pages*$page_size) - $this_location->capacity);
			} else {
				$total_slots=$page_size;
			}
			if($this_location->capacity <= $page_size) {
				$total_slots = $this_location->capacity;
			}
		?>
	<tbody id="item_table_body">
<?php
	if($order_by!="slot_num"){ //if items are not ordered by slot num
		$i=1;
		$occupied_slots=array();
      foreach($table_data->result() as $next_row) {
	      	array_push($occupied_slots,$next_row->slot_num);
	      ?>
        	<tr class="full_row<?= (($modified_rows!==FALSE) && in_array($next_row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$next_row->id?>">
		<td class="first_column">
          	<span id="item-<?php echo $next_row->slot_num.'-'.$next_row->id; ?>" rel="used"></span>
            	<div class="first_col_text" style="display: inline;">
                <?php $has_checkbox = FALSE; if(empty($next_row->transfer_status_id) || $next_row->destination_location_id!==NULL){?>
		<?php if ($can_edit == "edit_true"):?>
    <?php $has_checkbox = TRUE;?>
		<input type="checkbox" class="grid_checkbox" style="margin-right: -27px;" name="item_<?=$next_row->id?>" value="<?=$next_row->id?>"><?php  endif;?>
		<?php }?>
                <span style="margin-left:27px;"><?=$next_row->slot_num?></span>
            	</div>
          	</td>
		<td class="itemName"><?=htmlspecialchars($next_row->name) ?></td>
		<td class="itemKeywords"><span class="keywords"><?=htmlspecialchars($next_row->keywords) ?></span></td>
		<?php
		  if($show_notes->num_rows() > 0)
		  {
		  if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
		    {
		      ?>
		  <td class="itemDescription"><span class="notes"><?=htmlspecialchars($next_row->description) ?></span></td>
		    <?php
		    }
		  }
		?>
		<td class="itemCategory"><?=$next_row->category_id==0?'':htmlspecialchars($this->Category_model->get_by_id($next_row->category_id)->name)?></td>
		<td class="itemActionDate"><?=$next_row->action_date==0?'':date("m/d/Y",$next_row->action_date)?></td>
		<?php 
		  $transfer = '';
		  if(!empty($next_row->transfer_status_id) && $next_row->type =='delete' && $next_row->destination_location_id==="0"){
		    $transfer = "Pending Toss";
		  } else if(!empty($next_row->transfer_status_id) && $next_row->type =='merge'){
		    if($next_row->merge_into == 0){
		      $transfer = 'Merge Destination';
		    } else {
		      $transfer = 'Merge Source';
		    }   
		  } else if(!empty($next_row->transfer_status_id) && $next_row->destination_location_id===NULL) {
		    $transfer = "Transfer IN Pending";
		  } else if(!empty($next_row->transfer_status_id)) {
		    if($next_row->type == 'move') {
		      $transfer = "Transfer OUT Pending";
		    } else {
		      $transfer = "Pending Duplication";
		    }
		  }
		?>
	        <td class="item_status"><?=$transfer?></td>
	        </tr>
	      <?php
	      $i++;
      }
      /*
	if($table_data->num_rows < $total_slots): //Display empty slots if table data < total slots
	      for($i=1;$i<=$total_slots;$i++) {
		      if(!in_array($i,$occupied_slots)):
		      ?>

			  <tr class="open_row">
			  <td><span id="item-<?=$i?>" rel="open"></span><span class="open_row_item_number"><?=$i?></span></td>
			  <td> </td>
			  <?php 
			  if($show_notes->num_rows() > 0) {
			  if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
			  {?><td> </td><?php }}?>
			  <td> </td>
			  <td class="itemCategory"> </td>                                                                                                                                                                                                                                           <td class="itemActionDate"> </td>
			  <td class="open_row"> <span class="open">OPEN</span> </td>
			  </tr>

		      <?php
		      endif;
	      }
	endif; // end of if($table_data->num_rows == $total_slots):  
	*/
      } //end of not order order by slot num

        if($order_by=="slot_num"){  //start - order by slot asc
	      if($order=="asc"){
	 $stopping_slot=min(($page_size * $page_number),$this_location->capacity);	      
	 for($i = $start_slot; $i<=$stopping_slot;$i++){?>
      <?php if(isset($next_row) && $next_row->slot_num == $i){?>
        <tr class="full_row<?= (($modified_rows!==FALSE) && in_array($next_row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$next_row->id?>">
          <td class="first_column">
          <span id="item-<?php echo $i.'-'.$next_row->id; ?>" rel="used"></span>
            <div class="first_col_text" style="display: inline;">
                            <?php if(empty($next_row->transfer_status_id) || $next_row->destination_location_id!==NULL){?><?php if ($can_edit == "edit_true"):?><input type="checkbox" class="grid_checkbox" name="item_<?=$next_row->id?>" value="<?=$next_row->id?>"><?php  endif;?><?php }?>
                            <span style="margin-left:27px;"><?=$next_row->slot_num?></span>
            </div>
          </td>
          <td class="itemName"><?=htmlspecialchars($next_row->name) ?></td>
          <td class="itemKeywords"><span class="keywords"><?=htmlspecialchars($next_row->keywords) ?></span></td>
          <?php
          if($show_notes->num_rows() > 0)
          {
          if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
            {
              ?>
          <td class="itemDescription"><span class="notes"><?=htmlspecialchars($next_row->description) ?></span></td>
            <?php
            }}
          ?>
          <td class="itemCategory"><?=$next_row->category_id==0?'':htmlspecialchars($this->Category_model->get_by_id($next_row->category_id)->name ?? NULL)?></td>
          <td class="itemActionDate"><?=$next_row->action_date==0?'':date("m/d/Y",$next_row->action_date)?></td>
          <?php 
          $transfer = '';
          if(!empty($next_row->transfer_status_id) && $next_row->type =='delete' && $next_row->destination_location_id==="0"){
            $transfer = "Pending Toss";
          } else if(!empty($next_row->transfer_status_id) && $next_row->type =='merge'){
            if($next_row->merge_into == 0){
              $transfer = 'Merge Destination';
            } else {
              $transfer = 'Merge Source';
            }   
          } else if(!empty($next_row->transfer_status_id) && $next_row->destination_location_id===NULL) {
            $transfer = "Transfer IN Pending";
          } else if(!empty($next_row->transfer_status_id)) {
            if($next_row->type == 'move') {
              $transfer = "Transfer OUT Pending";
            } else {
              $transfer = "Pending Duplication";
            }
          }
          ?>
          <td class="item_status"><?=$transfer?></td>
	        <?php $next_row = $table_data->next_row()?>
      <?php } else { ?>
        <tr class="open_row">
          <td><span id="item-<?=$i?>" rel="open"></span><span class="open_row_item_number"><?=$i?></span></td>
          <td> </td>
          <?php 
          if($show_notes->num_rows() > 0) {
          if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
          {?><td> </td><?php }}?>
          <td> </td>
          <td class="itemCategory"> </td>                                                                                                                                                                                                                                     
          <td class="itemActionDate"> </td>
          <td class="open_row"> <span class="open">OPEN</span> </td>
        </tr>
      <?php }?>
    <?php } 
	      }
	} //end of order by slot



        if($order_by=="slot_num"){  //start - order by slot descending
	      if($order=="desc"){
	     
	     if($start_slot <= $page_size) {
		     $end_slot_for_desc=1;
	     } else {
		      $end_slot_for_desc=($start_slot-$page_size+1);
	     }

	 for($i = $start_slot; $i>=$end_slot_for_desc;$i--){ ?>
      <?php if(sizeof($next_row)>0 && $next_row->slot_num == $i){?>
        <tr class="full_row<?= (($modified_rows!==FALSE) && in_array($next_row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$next_row->id?>">
          <td class="first_column">
          <span id="item-<?php echo $i.'-'.$next_row->id; ?>" rel="used"></span>
            <div class="first_col_text" style="display: inline;">
                            <?php if(empty($next_row->transfer_status_id) || $next_row->destination_location_id!==NULL){?><?php if ($can_edit == "edit_true"):?><input type="checkbox" class="grid_checkbox" name="item_<?=$next_row->id?>" value="<?=$next_row->id?>"><?php  endif;?><?php }?>
                            <span style="margin-left:27px;"><?=$next_row->slot_num?></span>
            </div>
          </td>
          <td class="itemName"><?=htmlspecialchars($next_row->name) ?></td>
          <td class="itemKeywords"><span class="keywords"><?=htmlspecialchars($next_row->keywords) ?></span></td>
          <?php
          if($show_notes->num_rows() > 0)
          {
          if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
            {
              ?>
          <td class="itemDescription"><span class="notes"><?=htmlspecialchars($next_row->description) ?></span></td>
            <?php
            }}
          ?>
          <td class="itemCategory"><?=$next_row->category_id==0?'':htmlspecialchars($this->Category_model->get_by_id($next_row->category_id)->name)?></td>
          <td class="itemActionDate"><?=$next_row->action_date==0?'':date("m/d/Y",$next_row->action_date)?></td>
          <?php 
          $transfer = '';
          if(!empty($next_row->transfer_status_id) && $next_row->type =='delete' && $next_row->destination_location_id==="0"){
            $transfer = "Pending Toss";
          } else if(!empty($next_row->transfer_status_id) && $next_row->type =='merge'){
            if($next_row->merge_into == 0){
              $transfer = 'Merge Destination';
            } else {
              $transfer = 'Merge Source';
            }   
          } else if(!empty($next_row->transfer_status_id) && $next_row->destination_location_id===NULL) {
            $transfer = "Transfer IN Pending";
          } else if(!empty($next_row->transfer_status_id)) {
            if($next_row->type == 'move') {
              $transfer = "Transfer OUT Pending";
            } else {
              $transfer = "Pending Duplication";
            }
          }
          ?>
          <td class="item_status"><?=$transfer?></td>
	        <?php $next_row = $table_data->next_row()?>
      <?php } else {?>
        <tr class="open_row">
          <td><span id="item-<?=$i?>" rel="open"></span><span class="open_row_item_number"><?=$i?></span></td>
          <td> </td>
          <?php 
          if($show_notes->num_rows() > 0) {
          if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
          {?><td> </td><?php }}?>
          <td> </td>
          <td class="itemCategory"> </td>                                                                                                                                                                                                                                     
          <td class="itemActionDate"> </td>
          <td class="open_row"> <span class="open">OPEN</span> </td>
        </tr>
      <?php }?>
    <?php } 
	      }
	} //end of order by desc

?>
	</tbody>
	</table>
    <?php
	$last_page_by_items=($order_by!="slot_num" && ($page_number == (ceil($this_location->num_items/$page_size))));		
	//exit(var_dump($last_page_by_items));
    ?>
    <?php if($page_number < ceil($this_location->capacity / $page_size) && !($last_page_by_items)):?>
  <div class="load_items_trigger" id="load_next">Load More Items</div>
    <?php endif; ?>
   <div id="table_toolbar">

    <?php if ($can_edit != "edit_false"):?>
				<a <?php if(!$location_full){?>href="<?=site_url()?>items/add/<?=$this_location->id?>/first/<?=$order_by?>/<?=$order?>"<?php }?> alt="Add New File" class="sprite <?=$location_full?'new_item_button_disabled':'new_item_button'?>" <?=$location_full?'':'rel="facebox nm"'?>></a>
        
    <?php if($location_full){ ?>
        <div id="bottom_notification" class="orange">No Slots Available

        <?php if ($can_edit =="edit_true"): ?><a href="<?=site_url()?>locations/modify_capacity/<?=$this_location->id?>" rel="facebox nm" class="blue">Add more capacity...</a><?php endif;?><span class="right"><a class="orange bottom_notification_right_link hide_bottom_notification" href="#">Hide this message</a></span></div>
		<div class="clear"></div>
		<?php } ?>
<?php endif;?>
	</div>

	
    <div id="load_more_items" style="display:none; position:fixed; right:50px; bottom: 50px; padding:20px; background:white; color:green; margin:0; border: 1px solid green;"><img style="margin-right:5px;" src="<?=site_url()?>img/spinner.gif">Loading more items</div>
	
<div id="nub">

</div>

<script>

          $(document).ready(function(){

            var onSwitch = false;
            var order_by = '<?=$order_by?>';

						//Click handlers for sorting
            $('.header_itemNumber').click(function(event){
              event.preventDefault();
							window.location="<?=site_url()?>locations/view/<?=$this->session->userdata('database_id')?>/<?=$this_location->id?>/1/slot_num/<?=($item_number_order == 'desc' ? 'asc' : 'desc')?>";
            });
            
            $('.header_itemName').click(function(event){
              event.preventDefault();
              window.location="<?=site_url()?>locations/view/<?=$this->session->userdata('database_id')?>/<?=$this_location->id?>/1/name/<?=($item_name_order == 'desc' ? 'asc' : 'desc')?>";
            });
            
            $('.header_itemCategory').click(function(event){
              event.preventDefault();
              window.location="<?=site_url()?>locations/view/<?=$this->session->userdata('database_id')?>/<?=$this_location->id?>/1/category/<?= ($item_category_order == 'desc' ? 'asc' : 'desc') ?>";
            });
            
            $('.header_itemActionDate').click(function(event){
              event.preventDefault();
              window.location="<?= site_url() ?>locations/view/<?= $this->session->userdata('database_id') ?>/<?= $this_location->id ?>/1/action_date/<?= ($item_action_order == 'desc' ? 'asc' : 'desc') ?>";
            });
            
            $('.header_itemStatus').click(function(event){
              event.preventDefault();
              window.location="<?= site_url() ?>locations/view/<?= $this->session->userdata('database_id') ?>/<?= $this_location->id ?>/1/status/<?= ($item_status_order == 'desc' ? 'asc' : 'desc') ?>";
            });
            
            var last_time = new Date().getTime();
            var new_time;

            <?php if ($page_number == null || $page_number == 1){
              echo "var curr_page_loaded = 1;";
            }
            if ($page_number > 1) {
              echo "var curr_page_loaded = $page_number;";
            }
            ?>
            
            if (!min_page_loaded || !max_page_loaded){
              var min_page_loaded = <?= $page_number ?>;
              var max_page_loaded = <?= $page_number ?>;
            }

            function get_next_page()
            {
                // Display alert or whatever you want to do when you're 
                //   at the bottom of the page. 
                  var page = max_page_loaded + 1;
                  if (( page > max_page_loaded ) && ( onSwitch === false )){
                    max_page_loaded++;
                    $('#load_more_items').fadeIn();
										fetch_url = "<?php echo site_url(); ?>locations/view/<?= $this->session->userdata('database_id') ?>/<?= $this_location->id ?>/" + page + '/<?= $order_by ?>/<?= $order ?>';
                    //Let's get the contents of the next page and append them to the table
                    $.ajax({
                      url: fetch_url,
                        cache: false,
                        success: function(html){
                          //If the session expired, redirect user to login screen
                          if (html == 'session_expired'){
                            alert('You have been logged out of Paper Tiger. Redirecting you to the login page!');
                            window.location="<?= site_url() ?>login/logout";
                          }
                          //Make sure we're putting it in the right table
                          else {
                            $('#load_more_items').fadeOut();
                            $("#item_table_body").append(html);
                            //ShiftClick Binding	
                            $('input.grid_checkbox').unbind();
                            $('input.grid_checkbox').shiftClick();
                            onSwitch = false;
                            if (order_by != 'slot_num'){
                              if (Math.ceil((<?= $this_location->num_items ?> / <?= $page_size ?>)) == page ){
                                $('#load_next').hide();
                              }
                            }
                            else{
                              if (Math.ceil((<?= $this_location->capacity ?> / <?= $page_size ?>)) == page ){
                                $('#load_next').hide();
                              }
                            }
                          }
                        }
                    });
                  }
            }

            function get_prev_page(old_document_height, scroll_top)
            {
                // Display alert or whatever you want to do when you're 
                //   at the bottom of the page. 
                  var page = min_page_loaded - 1;
                  if (page < min_page_loaded){
                    min_page_loaded--;
                    $('#load_more_items').fadeIn();
										fetch_url = "<?php echo site_url(); ?>locations/view/<?= $this->session->userdata('database_id') ?>/<?= $this_location->id ?>/" + page + '/<?= $order_by ?>/<?= $order ?>';
                    //Let's get the contents of the next page and append them to the table
                    $.ajax({
                      url: fetch_url,
                        cache: false,
                        success: function(html){
                          //If the session expired, redirect user to login screen
                          if (html == 'session_expired'){
                            alert('You have been logged out of Paper Tiger. Redirecting you to the login page!');
                            window.location="<?= site_url() ?>login/logout";
                          }
                          //Make sure we're putting it in the right table
                          else {
                            $("#item_table_body").prepend(html);
                            //ShiftClick Binding	
                            $('input.grid_checkbox').unbind();
                            $('input.grid_checkbox').shiftClick();
                            var new_document_height = document.body.offsetHeight;
                            $('#load_more_items').fadeOut();
                            $(window).scrollTop(new_document_height - old_document_height + scroll_top);
                            if (page == 1){
                              $('#load_previous').hide();
                            }
                          }
                        }
                    });
                  }
            }

            //Load next page handler function
            $('#load_next').click(function(event){
              event.preventDefault();
              if (('#item_table_without_notes').length){
                if ( Math.floor( (<?= $this_location->capacity ?> - $('#item_table_without_notes >tbody >tr').length) / <?= $page_size ?> ) > 0 ){
                  new_time = new Date().getTime();
                  if (new_time - last_time >= 500){
                    get_next_page();
                    onSwitch = true;
                    last_time = new_time;
                  }
                }
              }
              if (('#item_table').length){
                if ( Math.floor( (<?= $this_location->capacity ?> - $('#item_table >tbody >tr').length) / <?= $page_size ?> ) > 0 ){
                  new_time = new Date().getTime();
                  if (new_time - last_time >= 500){
                    get_next_page();
                    last_time = new_time;
                  }
                }
              }
            });

            //Load previous page handler function
            $('#load_previous').click(function(event){
              event.preventDefault();
              if (('#item_table_without_notes').length){
                if ( Math.floor( (<?= $this_location->capacity ?> - $('#item_table_without_notes >tbody >tr').length) / <?= $page_size ?> ) > 0 ){
                  if (min_page_loaded > 1){
                    new_time = new Date().getTime();
                    if (new_time - last_time >= 500){
                      get_prev_page(document.body.offsetHeight, $(document).scrollTop());
                      last_time = new_time;
                    }
                  }
                }
              }
              if (('#item_table').length){
                if ( Math.floor( (<?= $this_location->capacity ?> - $('#item_table >tbody >tr').length) / <?= $page_size ?> ) > 0 ){
                  if (min_page_loaded > 1){
                    new_time = new Date().getTime();
                    if (new_time - last_time >= 1000){
                      get_prev_page(document.body.offsetHeight, $(document).scrollTop());
                      last_time = new_time;
                    }
                  }
                }
              }
            });

          });
</script>

<?php if ($can_edit == "edit_true"){ ?>    
	
<script type="text/javascript">
	var nub = $('#nub');
	var last_move = 0;
	var allow_hide = 0;
	
  var open_time;
//Nub Related Functions ###########################################
	
	nub.hover (
		function() {
			nub.show();
			open_time = 0;
		},
		function () {
			open_time = new Date().getTime();
			setTimeout( hideNub, 500 );
		}
	);
	
	//delay before closing nub
	function hideNub() 
	{
		var time_now = new Date().getTime();
		
		if(  open_time !== 0 && time_now - open_time > 500 )
		{
			nub.hide();
		}
		else
		{
			setTimeout( hideNub, 500 );
		}
	}


	nub.hide();
  
$('#item_table tbody tr,#item_table_without_notes tbody tr').live({
  mouseenter: function() {       
			//mouse in
			nub.stop(true, true);
			nub.hide();
			
			var info = $('td:first-child span', this);
			var pos = $('td', this);
			var row_offset = pos.position();
			var bgcolor = $(this).css('background-color');
			nub.css('background-color', bgcolor);
			nub.css('border-right', bgcolor);
			var data = info.attr("id");
			var arr = data.split('-');
			pos = arr[1];
			var id = arr[2];
      var link;
						
			if(info.attr("rel") !== "open")
			{

				var link2 = document.createElement('a');
				link2.setAttribute('href', '/items/delete/'+id+'/');
				link2.setAttribute('rel', 'facebox nm');

				var trash = new Image();
				trash.src = '<?= site_url() ?>img/trashcan.png';
				
				link2.appendChild( trash );

				link = document.createElement('a');
				link.setAttribute('href', '/items/edit/<?= $order_by ?>/<?= $order ?>/'+id+'/');
				link.setAttribute('rel', 'facebox nm');
				link.setAttribute('class', 'edit');
				link.appendChild(document.createTextNode('Edit'));

				//document.getElementById('nub').appendChild(link);
				nub.html(link2);
				nub.append(' ');
				nub.append(link);
				
			}
			else
			{
				link = document.createElement('a');
				link.setAttribute('href', '/items/add/<?= $this_location->id ?>/' + pos + '/<?= $order_by ?>/<?= $order ?>');
				link.setAttribute('rel', 'facebox nm');
				link.setAttribute('class', 'new');
				link.appendChild( document.createTextNode('New') );

				nub.html( link );
			}		
				
			nub.css( {"top": row_offset.top} );
			nub.show();		
			$('a[rel*=facebox]').unbind('click');	
			$('a[rel*=facebox]').facebox();
			open_time = 0;
  },
  mouseleave: function() {
			//mouse out
			open_time = new Date().getTime();
			setTimeout( hideNub, 500 );
  }
});
</script>
<?php } ?>
