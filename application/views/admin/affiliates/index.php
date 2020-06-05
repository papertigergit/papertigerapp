<div id="dashboard">

    <h1>Affiliates Administration</h1>
    <br/><br/>
    
    
    <?=anchor('/admin/affiliates/all', 'View All Affiliates')?>
    
    <br/><br/>
    <h2>Pending payouts:</h2>
        
    <?php if($pending_payouts->num_rows() == 0): ?>
        
        All payouts are up to date.
    
    <?php else : ?>
    
    <ol>
        <?php foreach($pending_payouts->result() as $referral): ?>
        
            <li>
				<?=anchor('/admin/affiliates/view/'.$referral->affiliate_code, $referral->first_name." ".$referral->last_name);?>
            </li>        
        
        <?php endforeach; ?>
    
    <?php endif; ?>
    
    </ol>
    
    <br/><br/>
    <h2>Latest Payments (informational only; no action necessary):</h2>
    
    <?php if($latest_payments->num_rows() == 0): ?>
        
        There are no payments to display.
    
    <?php else : ?>
    
    <ol>
        <?php foreach($latest_payments->result() as $payment): ?>
        
            <li>
                $<?=number_format($payment->amount, 2)?> to <?=$payment->affiliate_code?> on <?=date('M d Y', $payment->date)?>
            </li>        
        
        <?php endforeach; ?>
    
    <?php endif; ?>
    
    </ol>
    
    <br/><br/>
    <h2>Latest Referrals (informational only; no action necessary):</h2>
    
    <?php if($latest_referrals->num_rows() == 0): ?>
        
        There are no referrals to display.
    
    <?php else : ?>
    
    <ol>
        <?php foreach($latest_referrals->result() as $referral): ?>
        
            <li>
            	<?php if($referral->organization_id == NULL): ?>
                	<?=date('M d Y', $referral->original_date)?> - <?=anchor('admin/affiliates/view/'.$referral->affiliate_code, $referral->first_name." ".$referral->last_name)?> referred <?=$referral->org_name?> (Manual Entry).	
            	<?php else: ?>
	                <?=date('M d Y', $referral->original_date)?> - <?=anchor('admin/affiliates/view/'.$referral->affiliate_code, $referral->first_name." ".$referral->last_name)?> referred <?=$referral->organization_name?> (<?=$referral->organization_email?>).
            	<?php endif; ?>
            </li>        
        
        <?php endforeach; ?>
    
    <?php endif; ?>
    
    </ol>
    
</div>
