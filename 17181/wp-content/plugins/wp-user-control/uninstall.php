<?php

if ( !class_exists( 'wp_user_control_uninstall' ) ) {

	// wp_user_control class
	class wp_user_control_uninstall {
		
		// public constructor method
		function wp_user_control_uninstall() { 
			$this->__construct();
		}
		
		// hidden constructor method
		function __construct() {
			// verify administrative rights
			if ( is_admin() ) {
				
				if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
					exit();
				}
				// remove wp_user_control settings from wordpress database
				$settings = array( 'widget_wp-user-control-widget' );
				foreach ( $settings as $key => $value ) {
					// remove options from WordPress database
					delete_option( $value );
				}
			}
		} // end method __construct()
		
	} // end class wp_user_control_uninstall
	
} // end if class exists

// instantiate new wp_user_control_uninstall object
if ( class_exists( 'wp_user_control_uninstall' ) ) {
	new wp_user_control_uninstall();
}

?>