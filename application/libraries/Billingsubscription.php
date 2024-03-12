<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class BillingSubscription {
	
	function __construct() {
		global $CI;
		$CI =& get_instance();
		
		//let's load up Braintreezle
		require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
		$CI->config->load('braintree');
			
		$this->gateway = new Braintree\Gateway([
                    'environment' => $CI->config->item('environment'),
                    'merchantId' => $CI->config->item('merchantId'),
                    'publicKey' => $CI->config->item('publicKey'),
                    'privateKey' => $CI->config->item('privateKey')
                ]);
	}

	function create_subscription($data) {
		log_message('error', '--- STARTING BRAINTREE PAYMENT ---');
		$result = $this->gateway->customer()->create(array(
			'id'		=> $data['id'],
		    'firstName' => $data['firstName'],
		    'lastName' 	=> $data['lastName'],
		    'company' 	=> $data['companyName'],
		    'email' 	=> $data['email']	
		));

		if( ! $result->success ) {
			log_message('error', 'CUSTOMER CREATION RESULT: ' . print_r($result, TRUE) );
			return array(
				'success' => FALSE,
				'message' => NULL
			);
		}

		$customer = $result->customer;

		$result = $this->gateway->creditCard()->create(array(
			'customerId' => $customer->id,
		    'number' => $data['cardNumber'],
			'cvv' => $data['cardCVV'],
		    'expirationDate' => $data['expirationDate'],
		    'billingAddress' => array(
                'firstName'         => $data['billingFirstName'],
                'lastName'          => $data['billingLastName'],
                'streetAddress'     => $data['billingStreet'],
                'extendedAddress'   => $data['billingStreet2'],
                'locality'          => $data['billingCity'],
                'region'            => $data['billingState'],
                'postalCode'        => $data['billingZip']
		    )
		));
		
		if( ! $result->success ) {
			log_message('error', 'CREDIT CARD CREATION RESULT: ' . print_r($result, TRUE) );
			log_message('error', 'CUSTOMER DATA: ' . print_r($data, TRUE) );
			$this->gateway->customer()->delete($data['id']);
			
			return array(	
				'success' 	=> FALSE,
				'message'	=> NULL //$result->errors->errors->nested['creditCard']->errors[0]->message //NULL
			);
		}
		
		$creditCard = $result->creditCard;
		
	
		$result = $this->gateway->subscription()->create(array(
		    'paymentMethodToken' => $creditCard->token,
		    'planId' => $data['package']
		
		));

		if( ! $result->success ) {
			log_message('error', 'SUBSCRIPTION CREATION RESULT: ' . print_r($result, TRUE) );
			log_message('error', 'CUSTOMER DATA: ' . print_r($data, TRUE) );
			$this->gateway->customer()->delete($data['id']);
			$this->gateway->creditCard()->delete($creditCard->token);
			
			return array(	
				'success' 	=> FALSE,
				'message'	=> NULL
			);
		}

		$subscription = $result->subscription;
		log_message('error', '--- ENDING BRAINTREE PAYMENT ---');

		return array(	'success'			=> TRUE,
						'subscription_id'	=> $subscription->id,
						'customer_id'		=> $customer->id
		);			
			
	}
	
	function change_subscription_plan($data) {
	   
		$result = $this->gateway->subscription()->update($data['subscription_id'], array(
		    'planId'  => $data['package'],
		    'price'   => $data['price']
		
		));
		
		if($result->success) {
            return TRUE;
		} else {
            return FALSE;
        }
	}
	
	function update_subscription($data) {
	global $CI;
	
		$subscriptionId = $data["subscription_id"];
		
		
		if( isset($data["cardNumber"]) ) {
		
			$result = $this->gateway->creditCard()->create(array(
				'customerId' => $CI->organization_data->id,
			    'number' => $data['cardNumber'],
			    'expirationDate' => $data['expirationDate'],
			));
			
			$creditCard = $result->creditCard;
			
			if( !$result->success ) {
				return FALSE;
			}
			
		}
		
		$subscription = $this->gateway->subscription()->find($subscriptionId);
		
		$result = $this->gateway->subscription()->update($subscription->id, array(
		    'paymentMethodToken' => $creditCard->token,
		    'price' => $data["amount"]
		));

		if( !$result->success ) {
			return FALSE;
		}

		return TRUE;
		
	}
	
	function cancel_subscription($data) {
	
		$subscriptionId = $data["subscription_id"];
				
		$result = $this->gateway->subscription()->cancel( $subscriptionId );
		
		if( $result->success ) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	
}