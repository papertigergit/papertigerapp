<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('expire_form_inputs'))
{
	function expire_form_inputs()
	{
	
		$output = "<select class=\"transaction_field\" id=\"card_expires_on_month\" name=\"card_expires_on_month\">";
		
		
		for($i=1; $i <= 12; $i++) {
			
			$j = $i;
			
			$output .= "<option value=\"".$j."\">";
			$output .= $j." - ".date("F", mktime(0, 0, 0, $i, 1, 2005));
			$output .= "</option>";								
			
		}
		
		$output .= "</select>";
		
		$current_year = (int) date('Y');
		$go_to_year = $current_year + 15;
		
		$output .= "<select class=\"transaction_field\" id=\"card_expires_on_year\" name=\"card_expires_on_year\">";
		
		for($i=$current_year; $i<=$go_to_year; $i++) {
		
			if($i==$current_year) {
				$output .= "<option selected=\"selected\" value=\"".$i."\">".$i."</option>";
			} else {
				$output .= "<option value=\"".$i."\">".$i."</option>";
			}
			
		}
		
		$output .= "</select>";
		
		return $output;	
	}
	
}