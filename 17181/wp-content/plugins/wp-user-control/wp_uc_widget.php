<?php

/*

 	Plugin Name: WP User Control
	Plugin URI: http://palmspark.com/wordpress-user-control/
	Version: 1.5.3
	Author: Bill Edgar
	Author URI: http://palmspark.com
	Text Domain: wp-uc-widget
	Description: WP User Control adds a sidebar widget that allows a user to login, register, retrieve lost passwords, etc. without leaving a page/post within your site. 
	Package: wp-uc-widget
	License: BSD New (3-CLause License)
	
	Copyright (c) 2012-2013, PalmSpark LLC
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
	    
	    * Redistributions of source code must retain the above copyright
	      notice, this list of conditions and the following disclaimer.
	    * Redistributions in binary form must reproduce the above copyright
	      notice, this list of conditions and the following disclaimer in the
	      documentation and/or other materials provided with the distribution.
	    * Neither the name of the PALMSPARK LLC nor the
	      names of its contributors may be used to endorse or promote products
	      derived from this software without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL PALMSPARK LLC BE LIABLE FOR ANY
	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	
*/

// define constants
define( 'WP_USER_CONTROL_WIDGET_VERSION', '1.5.3' );
define( 'WP_USER_CONTROL_WIDGET_BASE_URL', network_site_url() );

// directory locations
define( 'WP_USER_CONTROL_WIDGET_DIR', plugin_dir_url(__FILE__) );
define( 'WP_USER_CONTROL_PLUGIN_DIR', basename( dirname( __FILE__ ) ) );
define( 'WP_USER_CONTROL_WIDGET_CSS', WP_USER_CONTROL_WIDGET_DIR . 'css/' );
define( 'WP_USER_CONTROL_WIDGET_WP_INCLUDES', ABSPATH . WPINC );
define( 'WP_USER_CONTROL_WIDGET_INCLUDES', dirname(__FILE__) . '/' . 'inc/' );
define( 'WP_USER_CONTROL_WIDGET_JS', WP_USER_CONTROL_WIDGET_DIR . 'js/' );
define( 'WP_USER_CONTROL_WIDGET_HTML', WP_USER_CONTROL_WIDGET_DIR . 'html/' );

// php class files
define( 'WP_USER_CONTROL_WIDGET_SIDEBAR_CLASS', 'SidebarWidget' );
define( 'WP_USER_CONTROL_WIDGET_CLASS', 'WPUserControlWidget' );
define( 'WP_USER_CONTROL_WIDGET_EXCEPTION_CLASS', 'Exception' );
define( 'WP_USER_CONTROL_WIDGET_UTILITIES_CLASS', 'Utilities' );

// definition and configuration files
define( 'WP_USER_CONTROL_WIDGET_STYLE', 'style.css' );

// generic include method
function wp_uc__autoinclude( $class_name ) {
	try {
		include_once( WP_USER_CONTROL_WIDGET_INCLUDES . $class_name . '.php' );
	} catch ( Exception $e ) {
		echo "<p>" . $e->getMessage() . "</p>";
		exit();
	}
}

// generic getter method
function wp_uc__get( $property, $obj ) { // generic getter
	if ( array_key_exists( $property, get_class_vars( get_class( $obj ) ) ) ) {
		return $obj->$property;
	} else {
		die("<p>$property does not exist in " . get_class( $obj ) . "</p>");
	}
}

// generic setter method
function wp_uc__set( $property, $value, $obj ) { // generic setter
	if ( array_key_exists( $property, get_class_vars( get_class( $obj ) ) ) ) {
		$obj->$property = $value;
	} else {
		die( "<p>$property does not exist in " . get_class( $obj ) . "</p>" );
	}
}

// generic toString method
function wp_uc__toString( $obj ) { // generic object toString
	$attributes = get_class_vars( get_class( $obj ) );
	$str = 'Class = ' . get_class( $obj ) . '\n';
	foreach ( $attributes as $name => $value ) {
		$str .= $name . ' = ' . $value . '\n';
	}
	return $str;
}

if ( !class_exists( 'wp_uc_widget' ) ) {

	// wp_uc_widget class
	class wp_uc_widget {
		
		// public constructor method
		function wp_uc_widget() { 
			$this->__construct();
		}
		
		// hidden constructor method
		function __construct() {
			
			wp_uc__autoinclude( WP_USER_CONTROL_WIDGET_EXCEPTION_CLASS );
			wp_uc__autoinclude( WP_USER_CONTROL_WIDGET_SIDEBAR_CLASS ); // must be loaded first... base class
			wp_uc__autoinclude( WP_USER_CONTROL_WIDGET_UTILITIES_CLASS );
			wp_uc__autoinclude( WP_USER_CONTROL_WIDGET_CLASS );
			wp_uc__autoinclude( WP_USER_CONTROL_WIDGET_CLASS );
			
			try {
				$this->include_wp_functions();
			} catch ( wp_pluginException $e ) {
				echo $e->getError();
				die( '<p>WP User Control ' . __( 'exiting.' ) . '</p>' );
			}
			
			// add action hook for output of jquery.sparkline script
			add_action(
				$tag = 'wp_loaded',
				$callback = array( &$this, 'loadScripts' ),
				$priority = 10
				);
			
			// add action hook for loading of plugin text domain and language files
			add_action(
				$tag = 'plugins_loaded',
				$callback = array( &$this, 'loadText' ),
				$priority = 10
				);
			
			// verify we're on admin pages
			if ( is_admin() ) {

				// runs when plugin is activated
				register_activation_hook( __FILE__, array( &$this, 'activate' ) );
				// runs when plugin is deactivated
				register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
				
			}
		
		}
		
		// activate method
		function activate() {
			// call setup function
			$this->setup();
		}
		
		// deactivate method
		function deactivate() {
			
			// remove action and filter hooks			
			remove_action(
				$tag = 'wp_login_failed',
				$function_to_remove = 'wp_user_control_login_fail_action',
				$priority = 10,
				$accepted_args = 1
				); // remove action hook for failed login
			
			remove_filter(
				$tag = 'registration_errors',
				$function_to_remove = 'wp_user_control_registration_errors_filter',
				$priority = 10,
				$accepted_args = 3
				); // remove filter for registration errors
				
			remove_filter(
				$tag = 'login_url',
				$function_to_remove = 'wp_user_control_login_url_filter',
				$priority = 10,
				$accepted_args = 2
				); // remove filter for custom login url
			
			remove_filter(
				$tag = 'wp_mail_from',
				$function_to_remove = 'wp_user_control_mail_from_filter',
				$priority = 10,
				$accepted_args = 1
				); // remove filter for custom mail from address
			
			remove_filter(
				$tag = 'wp_mail_from_name',
				$function_to_remove = 'wp_user_control_mail_from_name_filter',
				$priority = 10,
				$accepted_args = 1
				); // remove filter for custom mail from name
			
		}
		
		function include_wp_functions() {
			// check for theme.php file existence
			if ( is_file( WP_USER_CONTROL_WIDGET_WP_INCLUDES . '/registration.php' ) ) {
				// check for get_page_templates function existence
				if ( !function_exists( 'username_exists' ) || !function_exists( 'email_exists' ) ) {
					// include get_page_templates function if necessary					
					if ( !include_once( WP_USER_CONTROL_WIDGET_WP_INCLUDES . '/registration.php' ) ) { // for WP get_page_templates function
						throw new wp_PluginException( 'WP username_exists or email_exists function INCLUDE failed.</p>' );
					}
				}
				// otherwise path is incorrect, throw error
			} else {
				throw new wp_PluginException( WP_USER_CONTROL_WIDGET_WP_INCLUDES . '/registration.php file not found.' );
			}
		}
		
		function loadScripts() {
			// register script with wordpress
			wp_register_script(
				$handle = 'wp-user-control-widget',
				$src = WP_USER_CONTROL_WIDGET_JS . 'wp-user-control-widget.js',
				$deps = array( 'jquery' ),
				$ver = WP_USER_CONTROL_WIDGET_VERSION,
				$in_footer = false
				);
			// print script
			wp_enqueue_script( $handle = 'wp-user-control-widget' );
		} // end method loadScripts
		
		function loadText() {
			// load plugin text domain
			load_plugin_textdomain(
				$domain = 'wp-user-control',
				$abs_rel_path = false,
				$plugin_rel_path = WP_USER_CONTROL_PLUGIN_DIR . '/languages/'
				);
		}
		
		// setup method
		function setup() {
					
		}
		
		// uninstall method
		function uninstall() {
			if ( !defined( 'ABSPATH' ) || !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
				exit();
			}
		}
		
		// validate method
		function validate( $rawinput ) {
			return $rawinput;
		}
		
	}
	
} // end class wp_uc_widget

// instantiate new wp_uc_widget object
if ( class_exists( 'wp_uc_widget' ) ) {
	new wp_uc_widget();
}
?>