<div id="dashboard">

    <h1>Affiliates Administration</h1>
    <br/><br/>
    <h2>Pending Payouts <?=$affiliate->first_name?> <?=$affiliate->last_name?> <?=anchor('/admin/affiliates/edit/'.$affiliate_code,'Edit Info')?></h2>
    <br/><br/>
    
    <?php if($pending_payouts->num_rows() == 0): ?>
        
        There are no pending payouts to display.
    
    <?php else : ?>
   
    <ol>
        <?php for($i=0; $i < $pending_payouts->num_rows(); $i++): ?>
                
            <li>
                <?=date('M d Y', $new[$i]['original_date'])?> - Referred <?=$new[$i]['organization_name']?>; Annual Net: $<?=$new[$i]['annual']?> [<?php echo ($new[$i]['tier'] > 0) ? 'indirect' : 'direct'; ?>]
            </li>        
        
        <?php endfor; ?>
    

    
    </ol>
    
    <br/><br/>
    Total net referrals: $<?=number_format($total_annual, 2)?> <br/>
    Payout amount: $<?=number_format($total_paid, 2)?> (Paid out at <?=$payout_percent*100;?>%)
    
    
    <br/><br/>
    <div>
        <?=form_open('admin/affiliates/markaspaid')?>
            <input type="hidden" name="affiliate_code" value="<?=$affiliate_code?>"/>
            <input type="submit" value="Mark as paid"/>
        </form>
        
        Payout to: <br/>
        
        <?=$affiliate->affiliate_first_name?> <?=$affiliate->affiliate_last_name?> <br/>
        
        <?=$affiliate->affiliate_street?> <br/>
        
        <?php if($affiliate->affiliate_street_2): ?>
        <?=$affiliate->affiliate_street_2?> <br/>
        <?php endif; ?>
        
        <?=$affiliate->affiliate_city?>, <?=$affiliate->affiliate_state?> <?=$affiliate->affiliate_zip?> <br/>
        
        TAX ID: <?=$affiliate->affiliate_tax?> <br/>
        
        <?php if($affiliate->affiliate_paypal): ?>
        Paypal Address: <?=$affiliate->affiliate_paypal?> <br/>
        <?php endif; ?>
        
    </div>
    
    <?php endif; ?>
    
    
    <br/><br/>
    
    <h2>Manual Referral Entry</h2>
    
        <?=form_open('admin/affiliates/manualentry')?>
            <label for="referral_organization_id">Organization ID: </label><input type="text" name="referral_organization_id" id="referral_organization_id"/> <br/>
            
            <label for="referral_date">Referral Date: </label><input type="text" name="referral_date" value="" id="referral_date" class="input"/> <span class="gray">(MM/DD/YY)</span><br/>  
            
            <label for="referral_price">Price: </label>$<input type="text" name="referral_price" id="referral_price"/> <span class="gray">(Annual Equivalent)</span><br/>            
                
            <input type="hidden" name="affiliate_code" value="<?=$affiliate_code?>"/>
            <input type="submit" value="Add Referral"/>
        </form>
    
    <br/><br/>
    
    <h2>All Referrals</h2>
    
    <?php if($referrals->num_rows() == 0): ?>
        
        There are no referrals to display.
    
    <?php else : ?>
    
    <ol>
        <?php foreach($referrals->result() as $referral): ?>
        
            <li>
            	<?php if($referral->organization_id == NULL): ?>
                	<?=date('M d Y', $referral->original_date)?> - Referred <?=$referral->org_name?> (Manual Entry).	
            	<?php else: ?>
	                <?=date('M d Y', $referral->original_date)?> - Referred <?=$referral->organization_name?> (<?=$referral->organization_email?>).
            	<?php endif; ?>
			</li>        
        
        <?php endforeach; ?>
    

    
    </ol>
    
    <?php endif; ?>
    
    
    
    
</div>
