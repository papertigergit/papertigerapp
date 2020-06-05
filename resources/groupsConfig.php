<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

return array(

		'css' => array(
						'//css/reset.css',
						'//facebox/facebox.css',
						'//css/smoothness/jquery-ui-1.8.6.custom.css',
						'//css/ui.dropdownchecklist.css',
						'//css/ui.daterangepicker.css',
						'//css/style.css'
						),
	            	
    	 'js' => array(
    	 				// '//js/jquery-1.4.2.js',
    	 			    '//js/jquery-1.4.4.min.js',
    	 				//'//js/jquery-1.7.1.min.js',
    	 				'//facebox/facebox.js',
    	 				'//js/jquery-ui-1.8.6.custom.min.js',
    	 				'//js/jquery.qtip.js',
    	 				'//js/jquery.form.js',
    	 				'//js/jquery.blockUI.js',
    	 				'//js/jquery.dataTables.1.6.2.js',
    	 				'//js/jquery.disable.text.select.pack.js',
    	 				'//js/jquery.shiftclick.js',
    	 				'//js/jquery.metadata.js',
    	 				'//js/ui.core-min.js',
    	 				'//js/ui.dropdownchecklist-min.js',
    	 				'//js/daterangepicker.jQuery.js',
    	 				'//js/jquery.timers-1.2.0.js',
    	 				'//js/jquery.numeric.js',
    	 				'//js/main.js'
    	 				)

// 'js' => array('//js/file1.js', '//js/file2.js'),
    // 'css' => array('//css/file1.css', '//css/file2.css'),

    // custom source example
    /*'js2' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => create_function('$a', 'return $a;')
        ))
    ),//*/

    /*'js3' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => array('Minify_Packer', 'minify')
        ))
    ),//*/
);
