<?php
if(($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 105) || ($user_data['id'] == 204) || ($user_data['id'] == 6)) 
{
?>
<form id="date_search" method="POST" action="<?=site_url()?>payments/search">
<script>

$(document).ready(function() {
   //Date Range Picker for advanced search
	$('#daterangepicker').daterangepicker(); 
});

</script>

<div class="field_container">
<label for="daterangepicker">Dates:</label>
<?php
$date_range_value='';
if($this->input->post('date_payments')){
	$date_range_value=$this->input->post('date_payments');
}
?>
<input id="daterangepicker" type="text" value="<?=$date_range_value?>"  name="date_payments" />			
<input type="submit" name="submit" value="get data">
</div>						
</form>
<?php
$this->load->helper('date');
echo $payment_logs->num_rows()." Results</br>";
echo "<table>";
echo "<tr><td>date</td><td>org_id</td><td>user_id</td><td>type</td><td>action</td><tr>";
foreach($payment_logs->result() as $payment_data)
{
	$org_name=null;
	if($payment_data->organization_id) {
		$org_name=$this->Organization_model->get_org_by_id($payment_data->organization_id)->row()->name;
	}
	$user_name=null;
	if($payment_data->user_id) {
		$user_name=$this->User_model->get_user($payment_data->user_id)->row()->email_address;
	}
echo "<tr><td>".unix_to_human($payment_data->date)."</td><td>".$org_name."</td><td>".$user_name."</td><td>".$payment_data->type."</td><td>".$payment_data->action."</td><tr>";
}
echo "</table>";
}
else
{
	redirect('/');
}
?>
