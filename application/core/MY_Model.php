<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PaperTiger base model.
 *
 * CodeIgniter 2 behaviour that used to be an edit inside system/core/Model.php: every model gets a copy of
 * the controller's loaded objects (libraries, models, ...) as properties. Moved here unchanged so that
 * system/ can be replaced by a clean framework (Phase 4); all models extend MY_Model.
 */
class MY_Model extends CI_Model {

	var $_parent_name = '';

	public function __construct() {
		parent::__construct();   // empty in CodeIgniter 3
		// If the magic __get() or __set() methods are used in a Model references can't be used.
		$this->_assign_libraries( (method_exists($this, '__get') OR method_exists($this, '__set')) ? FALSE : TRUE );
		
		// We don't want to assign the model object to itself when using the
		// assign_libraries function below so we'll grab the name of the model parent
		$this->_parent_name = ucfirst(get_class($this));
		
		log_message('debug', "Model Class Initialized");
	}

	function _assign_libraries($use_reference = TRUE)
	{
		$CI =& get_instance();				
		foreach (array_keys(get_object_vars($CI)) as $key)
		{
			if ( ! isset($this->$key) AND $key != $this->_parent_name)
			{			
				// In some cases using references can cause
				// problems so we'll conditionally use them
				if ($use_reference == TRUE)
				{
					$this->$key = NULL; // Needed to prevent reference errors with some configurations
					$this->$key =& $CI->$key;
				}
				else
				{
					$this->$key = $CI->$key;
				}
			}
		}		
	}

}
