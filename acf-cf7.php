<?php

/*
Plugin Name: Advanced Custom Fields: Contact Form 7
Plugin URI: https://github.com/taylormsj/acf-cf7
Description: Add one or more contact forms to an advanced custom field 
Version: 1.0.1
Author: Taylor Mitchell-St.Joseph
Author URI: http://taylormitchellstjoseph.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-cf7', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 




// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_cf7( $version ) {
	
	include_once('acf-cf7-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_cf7');	




// 3. Include field type for ACF4
function register_fields_cf7() {
	
	include_once('acf-cf7-v4.php');
	
}

add_action('acf/register_fields', 'register_fields_cf7');	



	
?>