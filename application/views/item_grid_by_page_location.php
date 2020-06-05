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
	<?php
			$table_id="item_table_without_notes";
	if($show_notes->num_rows() > 0) {
	if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
		{
			$table_id="item_table";
		}
	}
	?>

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

<?php
	if($order_by!="slot_num"): //if items are not ordered by slot num
		$i=1;
		$occupied_slots=array();
      foreach($table_data->result() as $next_row) {
	      	array_push($occupied_slots,$next_row->slot_num);
	      ?>
        	<tr class="full_row<?= (($modified_rows!==FALSE) && in_array($next_row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$next_row->id?>">
		<td class="first_column">
          	<span id="item-<?php echo $next_row->slot_num.'-'.$next_row->id; ?>" rel="used"></span>
            	<div class="first_col_text" style="display: inline;">
                <?php if(empty($next_row->transfer_status_id) || $next_row->destination_location_id!==NULL){?>
		<?php if ($can_edit == "edit_true"):?>
		<input type="checkbox" class="grid_checkbox" name="item_<?=$next_row->id?>" value="<?=$next_row->id?>"><?php  endif;?>
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
				  {?><td> </td><? }}?>
				  <td> </td>
				  <td class="itemCategory"> </td>                                                                                                                                                                                                                                           <td class="itemActionDate"> </td>
				  <td class="open_row"> <span class="open">OPEN</span> </td>
				  </tr>

			      <?php
			      endif;
		      }
	      endif;
	      */
      endif; //end of not order order by

        if($order_by=="slot_num"):  //start - order by slot asc
	      if($order=="asc"):
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
          {?><td> </td><?php }} ?>
          <td> </td>
          <td class="itemCategory"> </td>                                                                                                                                                                                                                                     
          <td class="itemActionDate"> </td>
          <td class="open_row"> <span class="open">OPEN</span> </td>
        </tr>
      <?php }?>
    <?php } 
	      endif;
	endif; //end of order by slot



        if($order_by=="slot_num"):  //start - order by slot descending
	      if($order=="desc"):
	     
	     if($start_slot <= $page_size) {
		     $end_slot_for_desc=1;
	     } else {
		      $end_slot_for_desc=($start_slot-$page_size+1);
	     }

	 for($i = $start_slot; $i>=$end_slot_for_desc;$i--){?>
		 <?php
//		 echo "<pre>";
//		 var_dump($table_data);
//		 echo "</pre>";
		 ?>
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
          {?><td> </td><?php }} ?>
          <td> </td>
          <td class="itemCategory"> </td>                                                                                                                                                                                                                                     
          <td class="itemActionDate"> </td>
          <td class="open_row"> <span class="open">OPEN</span> </td>
        </tr>
      <?php }?>
    <?php } 
	      endif;
	endif; //end of order by desc

?>
