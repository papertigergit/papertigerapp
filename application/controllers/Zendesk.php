<?php

/* 
	http://www.zendesk.com/api/remote-authentication 
*/

class Zendesk extends Controller {

	function index() {
	
		$this->load->library('authentication');
		$this->load->model('organization_model');
		
		$user_data = $this->session->userdata('user_data');
		$organization_data = $this->organization_model->get_organization($user_data['organization_id'])->row();	
		
		
/* 		CodeIgniter is a pain and won't allow us access to $_GET properly, let's do it manually */
		
		$string = substr( $_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?')+1, strlen($_SERVER['REQUEST_URI']));
		
		$parts = explode('&', $string);
		
		foreach($parts as $part) {
			$x = explode('=', $part);
			$name = $x[0];
			$$name = $x[1];
		}
			
		
/* 		We should now have $timestamp and $return_to set 

		Lets handle authentication and then redirection back to ZenDesk
*/

		if( $this->authentication->is_authenticated() ) {

            /* Let's pass along the package type as well */
            switch($organization_data->package_id) {
                case 1: //Free
                    $org = 'pto_free';
                    break;                
                case 2: //Pro
                    $org = 'pto_pro';
                    break;
                case 3: //Proyearly
                    $org = 'pto_pro';
                    break;
                case 13: //Proyearlyspecial
                    $org = 'pto_pro';
                    break;
                case 9: //Basic
                    $org = 'pto_basic';
                    break;
                case 10: //Basicyearly
                    $org = 'pto_basic';
                    break;
                case 5: //Business
                    $org = 'pto_business';
                    break;
                case 6: //Businessyearly
                    $org = 'pto_business';
                    break;
                case 15: //Businessyearlyspecial
                    $org = 'pto_business';
                    break;
                case 11: //Elite
                    $org = 'pto_elite';
                    break;
                case 12: //Eliteyearly
                    $org = 'pto_elite';
                    break;
                case 16: //Eliteyearlyspecial
                    $org = 'pto_elite';
                    break;
                case 7: //Unlimited
                    $org = 'pto_unlimited';
                    break;
                case 8: //Unlimitedyearly
                    $org = 'pto_unlimited';
                    break;
                case 17: //Unlimitedyearlyspecial
                    $org = 'pto_unlimited';
                    break;
                default:
                    $org = 'pto_free';            
            } 
            


			$token = 'ItbHxM3HXg6eVUAfCLcYiChKwpXw7o8a2IgP8CPxhHgajDJk';
		
			//hash =  hexdigest(MD5(name+email+external_id+organization+token+timestamp)) 		
			$hash = md5($user_data['first_name'].' '.$user_data['last_name'].$user_data['email_address'].$user_data['id'].$org.$token.$timestamp);
		
			$url = 'https://thepapertiger.zendesk.com/access/remote/?name='.$user_data['first_name'].'%20'.$user_data['last_name'].'&email='.$user_data['email_address'].'&external_id='.$user_data['id'].'&organization='.$org.'&timestamp='.$timestamp.'&return_to='.$return_to.'&hash='.$hash;
		
	
			header('Location: '.$url);
		
		} else {
			
			$this->authentication->logout();
			
		}


		
	
	}
	
	function logout() {
		
/* 		CodeIgniter is a pain and won't allow us access to $_GET properly, let's do it manually */
		
		$string = substr( $_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?')+1, strlen($_SERVER['REQUEST_URI']));
		
		$parts = explode('&', $string);
		
		foreach($parts as $part) {
			$x = explode('=', $part);
			$name = $x[0];
			$$name = $x[1];
		}
		
/* 		We're there any errors? */

		if( $kind == 'error' ) {
		
/* 			echo '<h2>'.htmlentities($error).'</h2>'; */
			echo '<h2>Error</h2>';
			echo '<p>'.htmlspecialchars($message).'</p>';
			return;			
		}
		
		$this->authentication->logout();
		
	}

}

?>
