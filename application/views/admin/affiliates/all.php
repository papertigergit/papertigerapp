<div id="dashboard">

    <h1>All Affiliates</h1>
    
    <?php if($affiliates->num_rows() == 0): ?>
        
		There are no registered affiliates.
    
    <?php else : ?>
    
    <ol>
        <?php foreach($affiliates->result() as $a): ?>
        
            <li>
				<?=anchor('/admin/affiliates/view/'.$a->affiliate_code, $a->first_name." ".$a->last_name);?>
            </li>        
        
        <?php endforeach; ?>
    
    <?php endif; ?>
    
</div>