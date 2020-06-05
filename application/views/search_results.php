<?php

        $search_string=$this->session->userdata('search_string');
        $network_utils = array();
        $network_utils = enable_buttons();
        $buttons_on = $network_utils['buttons_on'];
        $show_notes=$this->Settings_model->get_all();
        $notes_settings=array($show_notes->result_array());
        function highlight($search,$data){
            if($search=="+" || $search=="*") {
                    $replace= '<span class="highlight">'.htmlspecialchars($search).'</span>';
                    echo $highLightedData=str_ireplace($search,$replace,$data);
 
            } else {
                if (preg_match("/$search/i",$data,$matches))
                {
                    $replace= '<span class="highlight">'.htmlspecialchars($matches[0]).'</span>';
                    if($search=="^" || $search=="$") { //if search matches caret or dollar sign (That's start and end of line) 
                        $replace= '<span class="highlight">'.htmlspecialchars($search).'</span>';
                    }
                    echo $highLightedData=str_ireplace($search,$replace,$data);
                } else {
                    $replace= '<span class="highlight">'.htmlspecialchars($search).'</span>';
                    echo $highLightedData=str_ireplace($search,$replace,$data);
                }
            }
        }
?>



     <div id="table_header_bar" class="header_items">
        <div id="header_bar_left">
 
 
        Search Results for <?=$this->session->userdata('unformatted_search_string') == '' ?"All Items":"'".$this->session->userdata('unformatted_search_string')."'"?>

<?php
            $pendingcount=count((array)$unconverted_files);
            $docscount=count((array)$converted_files);
            if($docscount==1){
                $d_File="File";
            } else {
                $d_File="Files";
            }
            if($pendingcount==1){
                $unc_File="File";
            } else {
                $unc_File="Files";
            }
?>
<?php
if($pendingcount >= 1){
?>
<p style="margin-bottom:-6px;">          
        <span id="digital_notice" >
         <?php echo $pendingcount; ?> <?php echo $unc_File; ?> found that are NOT in <a style="color:yellow;" target="_blank" href="https://docs.google.com/#folders">Google Drive</a> Format (non-searchable)
         
        <span class="expand_unconverted">Show [+]</span>
 
        </span>
        
</p>
<?php } ?>



        </div>

        <div id="header_bar_right">
            <select id="location_jump">
                <option value="0">Jump to a location...</option>
                <?php foreach($locations->result() as $row){?>
                <option value="<?=$this->session->userdata['database_id']?>/<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name), 18, '...')?></option>
                <?php }?>
                <option value="trash">Trash</option>
            </select>
            <?php if ($buttons_on == 'edit_true'):?>
            <a class="sprite edit edit_button_disabled" value="search" alt="Edit" rel="facebox nm"></a>
            <span style="position:relative">
                <span class="sprite transfer transfer_button_disabled" alt="Transfer"></span>
                <div class="submenu">
                    <ul>
                        <li><a id="move" class="submenu_link" href="" rel="facebox nm">move</a></li>
                        <li><a id="merge" class="submenu_link" href="" rel="facebox nm">merge</a></li>
                        <li><a id="dupe" class="submenu_link" href="" rel="facebox nm">duplicate</a></li>
                    </ul>
                </div>
            </span>
            <a class="sprite toss toss_button_disabled" alt="Delete" rel="facebox modal"></a>
            <?php endif;?>
        </div>
        <div class="clear"></div>
    </div>



<!-- ajdkfj;dsajfk;jds;kfj  -->

                <div id="digital_results">    
         
    <!--start digital files -->
<?php
if($pendingcount >= 1){
?>
  <div class="table_header_bar_pending">
                    <samp id="pending_conversion_total">DIGITAL - <?php echo $pendingcount; ?> <?php echo $unc_File; ?> found that are NOT in <a style="color:yellow;" target="_blank" href="https://docs.google.com/#folders">Google Drive</a> Format (non-searchable) <span id="pending_toggle_button"> Show <span class="expand_shrink">[+]</span></span></samp>
                    
                </div> 
 
<div class="pending_conversion_grid">
<table cellspacing="0" id="search_results_table_unconverted" class="grid">
            <thead>
                <tr class="th_row">
                    <th class="header_itemType">File Type</th>
                    <th class="header_itemDoc">File Name</th>
                    <th class="header_itemPublished">Date Published</th>
                                        <th class="header_itemModified">Date Modified</th>
                    <th class="header_itemActionDate">Author</th>
                </tr>
            </thead>
       
       <tbody>
            <!--pending_digital data-->  
<?php foreach ($unconverted_files as $item){ ?>
                       <tr>
                <td>
                                    
                    <div id="google_action">
                    <p id="google_download">Download<br />
 <a href="<?php echo $item['link']; ?>"><img class="google_icons" src="<?php echo $item['iconLink']; ?>"></a>                   <br/>
                              </p>
                     
                    </div><!-- google_action -->
                                        
                   
                        
 
                     
                </td>
                <td>
                                    
<center><a target="_blank" href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></center>

                     
                </td>
                <td>
                <center><?php echo date("F d, Y", strtotime($item['createdDate'])); ?></center>         </td>
                <td>
                <center><?php echo date("F d, Y", strtotime($item['modifiedDate'])); ?></center>
                <td>
<center><?php echo $item['owner']; ?>  </center>            </td>
            </tr>
<?php } ?>
                </tbody>
            
       </table>
</div>
<?php } ?>
<?php
if($docscount >= 1){
?>
<div class="table_header_bar_google">
<samp id="pending_conversion_total">
DIGITAL - <b id='change_digital_count'><?php echo $docscount; ?></b>  <?php echo $d_File; ?> found in <a style="color:yellow;" target="_blank" href="https://drive.google.com/drive/my-drive">Google Drive</a> <span id="digital_toggle_button"> Hide <span class="expand_shrink">[-]</span></span>
</samp>
<a class="digital_help" href="https://app.thepapertiger.com/locations/help_green/" rel="facebox nm"><img width="16" height="16" class="tooltip digital_help_red" src="https://app.thepapertiger.com/img/tooltip.png"></a>
</div>
<div class="digital_files">
        <table cellspacing="0" id="search_results_table_google" class="grid">
            <thead>
                <tr class="th_row">
                    <th class="header_itemType">File Type</th>
                    <th class="header_itemName">File Name</th>
                    <th class="header_itemPublished">Date Published</th>
                                        <th class="header_itemModified">Date Modified</th>
                    <th class="header_itemActionDate">Author</th>
                </tr>
            </thead>
       
       <tbody>
            <!--digital data-->  
<?php foreach ($converted_files as $item){ ?>
                                <tr>
<td style="width:auto;">
                        <div class="download_tab">
                                               <div id="google_action">
                    <p id="google_download">Open

                    <div>

    <a href="<?php echo $item['link']; ?>"><img class="google_icons" src="<?php echo $item['iconLink']; ?>"></a>         <div/>
          <div style="font-size:10px;">
                    <?php if($item['mimeType']=="application/vnd.google-apps.spreadsheet"){?>Spreadsheet<?php } ?>
                    <?php if($item['mimeType']=="application/pdf"){?>PDF<?php } ?>
                    
                    <?php if($item['mimeType']=="application/postscript"){?>AI<?php } ?>
                    <?php if($item['mimeType']=="image/bmp"){?>BMP<?php } ?>
                    <?php if($item['mimeType']=="image/png"){?>PNG<?php } ?>
                    <?php if($item['mimeType']=="application/msword"){?>Document<?php } ?>
                    <?php if($item['mimeType']=="image/gif"){?>GIF<?php } ?>
                    <?php if($item['mimeType']=="image/jp2"){?>JPEG<?php } ?>
                    <?php if($item['mimeType']=="image/jpeg"){?>JPEG<?php } ?>
                    <?php if($item['mimeType']=="image/pict"){?>PIC<?php } ?>
                    <?php if($item['mimeType']=="image/x-macpaint"){?>PNT<?php } ?>
                    <?php if($item['mimeType']=="application/vnd.ms-powerpoint"){?>Power Point<?php } ?>
                    <?php if($item['mimeType']=="image/x-quicktime"){?>QTI<?php } ?>
                    <?php if($item['mimeType']=="text/richtext"){?>RTX<?php } ?>
                    <?php if($item['mimeType']=="text/rtf"){?>RTF<?php } ?>
                    <?php if($item['mimeType']=="image/tiff"){?>TIFF<?php } ?>
                    <?php if($item['mimeType']=="text/plain"){?>TXT<?php } ?>
                    <?php if($item['mimeType']=="application/vnd.ms-excel"){?>Spreadsheet<?php } ?>
                
                       <?php if($item['mimeType']=="application/vnd.google-apps.presentation"){?>Power Point<?php } ?>
                       <?php if($item['mimeType']=="application/vnd.google-apps.document"){?>Document<?php } ?><div/>   
                    </p>
                     
                    </div><!-- google_action -->
                          
                                           
                          
                        </div>
                    </td>
                    <td>
                                                                     <center> <a target="_blank" href="https://docs.google.com/spreadsheets/d/1YLf6wPQIYcvh1EBjuxDnPre-J_4hu6UeWKGT7RjbhcU/edit?usp=docslist_api">
                                                                          <a target="_blank" href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></center>                
                                            </td>
                    <td>
                    
                  <center><?php echo date("F d, Y", strtotime($item['createdDate'])); ?></center>                         </td>
                      
      
                    <!-- <td>Private</td> -->
                    <td>
                        <center><?php echo date("F d, Y", strtotime($item['modifiedDate'])); ?></center>                      </td>
                    <td>
                        <center><?php echo $item['owner']; ?></center>                    </td>
           </tr>
     <?php } ?>

            </tbody>
            
       </table>

</div>
 
 
 </div> 
 <?php } ?>
   

<!-- end of paper_results -->
<!-- adfakds;jfkd;osjf;adsj -->

<?php
$user_setting=$this->Settings_model->get_all();
?>

<div <?php if($digital_results_found){ ?> id="paper_results" <?php } else{ echo " ";} ?> >

<?php  if($digital_results_found){ ?>
<div class="table_header_bar_paper">
<samp id="pending_conversion_total">PHYSICAL - <?=$table_data->num_rows();?> <?=$unc_File?> found in Paper Tiger <span id="paper_toggle_button"> Hide <span class="expand_shrink">[-]</span></span></samp>
<a class="digital_help" href="<?=site_url()?>locations/help_blue/" rel="facebox nm"><img width="16" height="16" class="tooltip digital_help_red" src="<?=site_url()?>img/tooltip.png"></a>
</div>
<?php } else{ echo " ";} ?>


<div <?php if($digital_results_found){ ?> class="paper_results" <?php } else{ echo " ";} ?>>
 
<?php $results_found = false;?>
<?php $row = $table_data->first_row()?>
<?php foreach ($location_result_data->result() as $result_table) { ?>
 
 
<?php $count=1?>
        <div class="table_header_bar_2">
            <?=$result_table->count?> Items Found in <a href="<?=site_url()?>locations/view/index/<?=$result_table->location_id?>"><?=htmlspecialchars($this->Location_model->get_by_id($result_table->location_id)->name)?></a>
            <?php
                $maximum_search_terms = $this->config->item('maximum_search_terms');
                if($result_table->count > $maximum_search_terms)  {
                    echo '<span style="">(Only the first '.$maximum_search_terms.' search terms have been returned.)</span>';
                }
            ?>
        </div>
            <?php
                    $table_id="search_results_table_without_notes";
            if($show_notes->num_rows() > 0) {
            if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
                {
                    $table_id="search_results_table";
                }
            }
            ?>
        <table cellspacing="0" id="<?=$table_id?>" class="grid">
            <thead>
                <tr class="th_row">
                    <th class="header_itemNumber">Item</th>
                    <th class="header_itemName">Item Name</th>
                    <th class="header_itemKeywords">Keywords</th>
                    <?php
                    if($show_notes->num_rows() > 0) {
                        if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
                        {
                            ?>
                            <th class="header_itemDescription">Notes</th>
                            <?php
                        }
                    }
                    ?>
                    <th class="header_itemCategory">Category</th>
                    <th class="header_itemActionDate">Action Date</th>
                    <th class="header_itemStatus">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $max_results=min($maximum_search_terms,$result_table->count);
            ?>
    <?php while($count<=$max_results) { ?>
        <?php $results_found = true;?>
                <tr class="full_row<?= (($modified_rows!==FALSE) && in_array($row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$row->id?>">
        <?php
                $can_edit="edit_false";
                $loc_permissions=$this->Permissions_model->get_by_location($row->location_id,$this->user_data['id']);
                if($loc_permissions) {
                if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3 || ($organization_admin == $this->user_data['id'])) {
                    $can_edit="edit_true";
                  }
                }
                if($organization_admin == $this->user_data['id']):
                    $can_edit="edit_true";
                endif;  
        ?>
                    <td class="first_column">
                    <span id="item-<?=$row->id?>" class="used-<?=$can_edit?>"></span>
                        <div class="first_col_text">
                            <input type="checkbox" <?php if ($can_edit == 'edit_false'):?>disabled="disabled"<?php endif;?> class="grid_checkbox" name="item_<?=$row->id?>" value="<?=$row->id?>">
                            <span style="margin-left:27px;"><?=$row->slot_num?></span>
                        </div>
                    </td>
    <td class="itemName"><a href="<?=site_url()?>locations/view/index/<?=$row->location_id?>/<?= ceil($row->slot_num / $this->config->item('page_size') )?>#item_<?=$row->id?>"><?=highlight($search_string,$row->name)?></a></td>
                    <td><span class="keywords"><?=highlight($search_string,$row->keywords) ?></span></td>
                    <?php
                    if($show_notes->num_rows() > 0) {
                        if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
                        {
                            ?>
                            <td><span class="notes"><?=highlight($search_string,$row->description) ?></span></td>
                            <?php
                        }
                    }
                    ?>
                    <td class="itemCategory"><?=$row->category_id==0?'&nbsp;':htmlspecialchars($this->Category_model->get_by_id($row->category_id)->name??"")?></td>
                    <td class="itemActionDate"><?=$row->action_date==0?'&nbsp;':date("m/d/Y",$row->action_date)?></td>
                    <?php 
                    $transfer = '';
                    if(!empty($row->transfer_status_id) && $row->type =='delete' && $row->destination_location_id==="0"){
                        $transfer = "Pending Deletion";
                    } else if(!empty($row->transfer_status_id) && $row->type =='merge'){
                        if($row->merge_into == 0){
                            $transfer = 'Merge Destination';
                        } else {
                            $transfer = 'Merge Source';
                        }
                    } else if(!empty($row->transfer_status_id) && $row->destination_location_id===NULL) {
                        $transfer = "Transfer IN Pending";
                    } else if(!empty($row->transfer_status_id)) {
                        if($row->type == 'move') {
                            $transfer = "Transfer OUT Pending";
                        } else {
                            $transfer = "Pending Duplication";
                        }
                    }
                    ?>
                    <td class="itemStatus"><?=$transfer?></td>
                </tr>
                <?php $count++?>
                <?php $row = $table_data->next_row()?>
    <?php }?>
            </tbody>
    </table>
<?php }?>
    <?php if(!$results_found){?>
        <table cellspacing="0" class="search_results_table empty" class="grid"><tr class="open_row"><td colspan="7">No Results</td></tr></table>
    <?php }?>
</div> <!-- end of paper_results -->
</div>
<div id="nub" class="search_nub">
 
       
    
<script type="text/javascript">
    var nub = $('#nub');
    nub.hide();
    var last_move = 0;
    var allow_hide = 0;
    var open_time;
     
    $('#search_results_table tbody tr, #search_results_table_without_notes tbody tr').hover(
        function () { 
            //mouse in
            nub.stop(true);
            nub.hide();
             
             
            //Morris, the function that gets the item ID is broken and results in undefined, needs to be fixed for this to work!
            var info = $('td:first-child span', this);
            var pos = $('td', this);
            var row_offset = pos.offset();
            var bgcolor = $(this).css('background-color');
            nub.css('background-color', bgcolor);
            nub.css('border-right', bgcolor);
            var data = info.attr("id");
            var arr = data.split('-');
            var id = arr[1];
            var data2 = info.attr("class");
            var data2_content = data2.split('-');
            var edit = data2_content[1];
 
                var link2 = document.createElement('a');
                link2.setAttribute('href', '/items/delete/'+id+'/');
                link2.setAttribute('rel', 'facebox nm');
/*                      link2.appendChild( document.createTextNode('Delete') ); */
 
                var trash = new Image();
                trash.src = '<?=site_url()?>img/trashcan.png';
                 
                link2.appendChild( trash );
 
                var link = document.createElement('a');
                link.setAttribute('href', '/items/edit/'+id+'/');
                link.setAttribute('href', '/items/edit/slot_num/asc/'+id+'/');
                link.setAttribute('rel', 'facebox nm');
                link.setAttribute('class', 'edit');
                link.appendChild( document.createTextNode('Edit') );
 
                //document.getElementById('nub').appendChild(link);
                nub.html(link2);
                nub.append(' ');
                nub.append(link);
                 
            if (edit != "edit_false"){
                nub.css( {"top": row_offset.top} );
                nub.show();     
                $('a[rel*=facebox]').unbind('click');   
                $('a[rel*=facebox]').facebox();
                open_time = 0;
            }
             
        },
        function () {
            //mouse out
            open_time = new Date().getTime();
            setTimeout( hideNub, 500 );
        }
    );
     
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
         
        if(  open_time != 0 && time_now - open_time > 500 )
        {
            nub.hide();
        }
        else
        {
            setTimeout( hideNub, 500 );
        }
    }
</script>