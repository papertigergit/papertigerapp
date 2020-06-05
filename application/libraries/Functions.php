<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Functions {
		
	function stringBeginsWith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
	}
	
}

?>