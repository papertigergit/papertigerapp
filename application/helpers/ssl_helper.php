<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SSL Helpers
 *
 * @author		Peng Kong
 * @link		http://it.euphoriatwentythree.com/web-development/codeigniter/codeigniter-ssl-https-helper/
 * @credit		Inspired by nevercraft - http://codeigniter.com/forums/viewthread/83154/#454992@package
 */

// ------------------------------------------------------------------------

/**
 * Maintain SSL
 *
 * @access	public
 * @param	bool
 * @param	int
 * @return	void
 */
if ( ! function_exists('maintain_ssl'))
{
	function maintain_ssl($maintain = FALSE, $port = 443)
	{
		$CI =& get_instance();

		if (!$maintain && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'])) 
		{
			// remove protocol
			$segments = explode('://', $CI->config->config['base_url']);
			// explode url into segements
			$segments = explode('/', $segments[1]);
			// remove port number
			$domain = explode(':', $segments[0]);
			// form temp base url
			$temp_base_url = 'http://'.$domain[0];
			// replace segments
			for ($i=1; $i<sizeof($segments); $i++)
				if ($segments[$i])
					$temp_base_url .= $segments[$i].'/';
			// Temporarily overwrite base url
			$CI->config->config['base_url'] = $temp_base_url;
			
			// Keep flashdata - Requires modification to MY_Session keep_flashdata()
			// See http://it.euphoriatwentythree.com/web-development/codeigniter/codeigniter-session-keep-all-flashdata/
			$CI->session->keep_flashdata();

			// Correct by redirect
			header('Location: '.current_url() );
		}

		if ($maintain)
		{
			// remove protocol
			$segments = explode('://', $CI->config->config['base_url']);
			// explode url into segements
			$segments = explode('/', $segments[1]);
			// remove port number
			$domain = explode(':', $segments[0]);
			// form temp base url
			$temp_base_url = 'https://'.$domain[0];
			//using a non-standard SSL port?
			if($port !== 443) {
				$temp_base_url .= ':'.$port.'/';
			}
			// replace segments
			for ($i=1; $i<sizeof($segments); $i++)
				if ($segments[$i])
					$temp_base_url .= $segments[$i].'/';
			// Temporarily overwrite base url
			$CI->config->config['base_url'] = $temp_base_url;
		}

		// if don't maintain but SSL is on -OR- maintain but SSL isn't on, correct by redirect
		if ((!$maintain && !empty($_SERVER['HTTPS'])) || ($maintain && empty($_SERVER['HTTPS'])))
		{
			// Keep flashdata - Requires modification to MY_Session keep_flashdata()
			// See http://it.euphoriatwentythree.com/web-development/codeigniter/codeigniter-session-keep-all-flashdata/
			$CI->session->keep_flashdata();

			// Correct by redirect
			header('Location: '.current_url() );
		}
	}
}

/* End of file ssl_helper.php */
/* Location: ./system/application/helpers/ssl_helper.php */
