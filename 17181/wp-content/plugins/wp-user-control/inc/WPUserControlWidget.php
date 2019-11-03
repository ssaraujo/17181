<?php
 
/*
	WP User Control
	Contact: Bill Edgar (bill@palmspark.com)
	http://palmspark.com/wordpress-user-control
	
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

/* ------------- BEGIN ACTION & FILTER FUNCTIONS -------------- */

/**
 * Method to remove GET variables from URL.
 *
 * @param string $url
 * @return string
 */
function wp_user_control_cleanURI( $url ) {
	if ( $pos = strpos( $url, '?' ) ) {
		$url = substr( $url, 0, $pos );
	}
	return $url;
}

/**
 * 
 * Method to output logged in user control box
 * 
 * @param string $customLink
 * @param string $customLinkTitle
 * @param string $displayAvatar
 */
function wp_user_control_logged_in_user( $customLink, $customLinkTitle, $displayAvatar ) {
	get_currentuserinfo(); global $current_user; ?>
	<div id="wp-user-control-sidebox">
		<h3><?php echo __( 'Welcome,', 'wp-user-control' ) . ' ' . $current_user->display_name; ?></h3>
    <?php if ( $displayAvatar === 'enabled' ) { ?>
    	<div id="wp-user-control-usericon">
    	<?php echo get_avatar( $current_user->ID, 60 ); ?>
    	</div>
    <?php } ?>
    	<div id="wp-user-control-userinfo">
    		<p><?php _e( "You're logged in as", 'wp-user-control' ); ?> 
    			<strong><?php echo $current_user->display_name; ?></strong>
    		</p>
    		<p>
    			<a href="<?php echo wp_logout_url( wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ) ) ; ?>"><?php _e( 'Log out', 'wp-user-control' ); ?></a> &nbsp;|&nbsp; 
    			<?php 
    			/*
    			 * Use admin_url() instead of network_admin_url() for WPMS because network_admin_url()
    			 * will always point to the primary site admin dashboard rather than the current network site
    			 */
    			if ( force_ssl_admin() ) {
    				if ( strpos( admin_url(), 'http://' ) == 0 ) {
    					$admin_url = str_replace( 'http://', 'https://', admin_url() );
    				} else {
						$admin_url = admin_url();
					}
    			} else {
					$admin_url = admin_url();
				}
    			// check user capability
    			if ( current_user_can( 'manage_options' ) ) { 
	    			// output admin link if appropriate
    				echo '<a href="' . $admin_url . '">' . __( 'Admin', 'wp-user-control' ) . '</a>'; 
    			} else { 
    				// output profile link
    				echo '<a href="' . $admin_url . 'profile.php">' . __( 'Profile', 'wp-user-control' ) . '</a>'; 
    			}
    			// output custom link, if desired
    			if ( !empty( $customLink ) && !empty( $customLinkTitle ) ) {
    				echo ' &nbsp;|&nbsp; <a href="' . $customLink . '">' . $customLinkTitle . '</a>';
    			}
    			?>
    		</p>
    	</div>
    </div> <?php 
}

/**
 * Method to process custom login for WP User Control widget.
 */
function wp_user_control_login_request() {
	// check POST for wp_uc_login_request
	$action = ( array_key_exists( 'wp_uc_login_request', $_REQUEST ) ) ? trim( $_REQUEST['wp_uc_login_request'] ) : false;
	// if login action is requested
	if ( $action ) {
		$login = 'failed';
		// grab desired user name and email
		$user_login = ( array_key_exists( 'user_login', $_REQUEST ) ) ? trim( $_REQUEST['user_login'] ) : false;
		$user_pass = ( array_key_exists( 'user_pass', $_REQUEST ) ) ? trim( $_REQUEST['user_pass'] ) : false;
		$remember = ( array_key_exists( 'remember', $_REQUEST ) ) ? trim( $_REQUEST['remember'] ) : false;
		// check for empty fields
		if ( !empty( $user_login ) && !empty( $user_pass ) ) {
			// set up array for user credentials
			$credentials = array();
			$credentials['user_login'] = $user_login;
			$credentials['user_password'] = $user_pass;
			$credentials['remember'] = ( !empty( $remember ) ) ? true : false;
			// attempt to signon user
			// first perform SSL checks to determine protocol
			if ( is_ssl() || force_ssl_login() ) {
				$ssl = true;
			} else {
				$ssl = false;
			}
			$user = wp_signon( $credentials, $ssl );
			// check for error
			if ( is_wp_error( $user ) ) {
				$login = 'failed';
			// otherwise process logged in user output
			} else {
				// update current user
				// wp_set_current_user( $user->ID, $user->user_login );
				$login = 'success';
			}
		}
		// construct server redirect after processing login
		$referrer = wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ); // get submission source
		// SSL checks
		$url = ( is_ssl() ) ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST']; // get host url
		$url .= $referrer; // add rest of address
		$result = ( $login == 'failed' ) ? "?login=failed&user_login=$user_login" : ''; // append failed result if applicable
		$url .= $result;
		wp_redirect( $url ); // redirect
		exit;
	}
}

/**
 *
 * Filter method to set custom login url.
 *
 * @param string $login_url
 * @param string $redirect
 * @return string
 */
function wp_user_control_login_url_filter( $login_url, $redirect ) {
	if ( !empty( $redirect ) ) {
		// first get options array from wordpress database
		$arr = get_option(
			$show = 'widget_wp-user-control-widget',
			$default = false
			);
		// check to see if option array was successfully retrieved
		if ( $arr !== false ) {
			// loop through options array from database
			foreach ( $arr as $instance_id => $properties ) {
				// make sure we're dealing with a valid instance id
				if ( is_numeric( $instance_id ) && count( $properties ) > 1 ) {
					// if custom login url is stored and not empty, return it
					if ( !empty( $properties['customLoginURL'] ) ) {
						return $properties['customLoginURL'];
						// otherwise return default login url
					} else {
						return $login_url;
					}
					// instance id is not valid, or no options are saved, return default login url
				} else {
					return $login_url;
				}
			}
			// options array was not successfully retrieved, return default login url
		} else {
			return $login_url;
		}
		// custom login redirect has already been set for site, return that
	} else {
		return $redirect;
	}
}

/**
 * 
 * Method for sending user notification email via wp_mail
 * 
 * @param string $message
 * @param string $subject
 * @param string $user_mail
 */
function wp_user_control_mail( $message, $subject, $user_email ) {
	if ( false == wp_mail( $user_email, sprintf( __( '%s ' . $subject, 'wp-user-control' ), htmlspecialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ), $message ) ) {
		$error[] = '<p><span class="registerfail">' . __( 'E-mail could not be sent.', 'wp-user-control' ) . "<br />\n"
				. __( 'Possible reason: your host may have disabled the mail() function...', 'wp-user-control' ) . '</span></p>';
	}
}

/**
 * 
 * Filter method to set custom email address for sending all WP administrative email.
 * 
 * @param string $from_email
 * @return string $from_email
 */
function wp_user_control_mail_from_filter( $from_email ) {
	// first get options array from wordpress database
	$arr = get_option(
		$show = 'widget_wp-user-control-widget',
		$default = false
		);
	// check to see if option array was successfully retrieved
	if ( $arr !== false ) {
		// loop through options array from database
		foreach ( $arr as $instance_id => $properties ) {
			// make sure we're dealing with a valid instance id
			if ( is_numeric( $instance_id ) && count( $properties ) > 1 ) {
				// if mail from address is stored and not empty, return it
				if ( !empty( $properties['mailFromAddress'] ) ) {
					return $properties['mailFromAddress'];
					// otherwise return default mail from
				} else {
					return $from_email;
				}
				// instance id is not valid, or no options are saved, return default login url
			} else {
				return $from_email;
			}
		}
		// options array was not successfully retrieved, return default login url
	} else {
		return $from_email;
	}
}

/**
 * 
 * Filter method to set custom name in from field for all WP administrative email.
 * 
 * @param string $from_name
 * @return string $from_name
 */
function wp_user_control_mail_from_name_filter( $from_name ) {
	// first get options array from wordpress database
	$arr = get_option(
		$show = 'widget_wp-user-control-widget',
		$default = false
		);
	// check to see if option array was successfully retrieved
	if ( $arr !== false ) {
		// loop through options array from database
		foreach ( $arr as $instance_id => $properties ) {
			// make sure we're dealing with a valid instance id
			if ( is_numeric( $instance_id ) && count( $properties ) > 1 ) {
				// if mail from address is stored and not empty, return it
				if ( !empty( $properties['mailFromName'] ) ) {
					return $properties['mailFromName'];
					// otherwise return default mail from
				} else {
					return $from_name;
				}
				// instance id is not valid, or no options are saved, return default login url
			} else {
				return $from_name;
			}
		}
		// options array was not successfully retrieved, return default login url
	} else {
		return $from_name;
	}
}

function wp_user_control_output_registration_error( $error ) {
	?>
	<p><span class="registerfail">
	<?php
	switch ( $error ) {
		case 'username_exists':
			_e( 'Username exists. Please choose another.', 'wp-user-control' );
			break;
		case 'email_exists':
			_e( 'Email already registered. Did you forget your password?', 'wp-user-control' );
			break;
		case 'registration_disabled':
			_e( 'Sorry, new registrations are not allowed at this time.', 'wp-user-control' );
			break;
		case 'logged_in':
			_e( 'You are logged in already. No need to register again!', 'wp-user-control' );
			break;
		case 'empty_fields':
			_e( 'Please enter a valid user name and email address.', 'wp-user-control' );
			break;
		case 'uppercase':
			_e( 'User name cannot contain uppercase letters.', 'wp-user-control' );
			break;
		case 'spaces':
			_e( 'User name cannot contain spaces.', 'wp-user-control' );
			break;
		case 'honeypot':
			// do not output any error, spam registration
			break;
		default:
			_e( 'Registration failed. Unknown error.', 'wp-user-control' );
			break;
	}
	?>
	</span></p><?php
}

/**
 *
 * Method to create user message for email notification of password reset or new user creation.
 *
 * @param string $action ('reset' or 'new')
 * @param string $temp_password
 * @param string $url
 * @param string $user_login
 */
function wp_user_control_user_email_msg( $action, $temp_password, $url, $user_login ) {
	// create new user email message
	if ( $action == 'reset' ) {
		$message = __('Someone has asked to reset the password for the following site and username.', 'wp-user-control' ) . "\r\n\r\n";
	} else {
		$message = __('A new user has been created for the following site.', 'wp-user-control' ) . "\r\n\r\n";
	}
	$message .= $url . "\r\n\r\n";	
	$message .= sprintf( __( 'Username: %s', 'wp-user-control' ), $user_login ) . "\r\n";
	$message .= sprintf( __( 'Temporary Password: %s', 'wp-user-control' ), $temp_password ) . "\r\n\r\n";
	$message .= __( 'Please change your password when you login.', 'wp-user-control' ) . "\r\n\r\n";
	return $message;
} // end function user_email_msg

/* ------------- END ACTION & FILTER FUNCTIONS -------------- */

if ( !class_exists( 'wp_user_control_Widget' ) ) {
	
	// wp_user_control_widget class
	class wp_user_control_Widget extends wp_SidebarWidget {
		
		private $tabSelections = array();
			
		/* -------------------------------------------------------------------------*/
		/* Constructor
		/* -------------------------------------------------------------------------*/
		
		/**
		 * Widget constructor method. Must be overridden in child class.
		 */
		function wp_user_control_Widget() {
			// initialize widget
			$my_locale = ( get_locale() ) ? get_locale() : 'en_US';
			$this->init( array(
				'widgetDescription' =>	__( 'Customizable user control widget for login, registration, lost password, etc. within sidebar.', 'wp-user-control' ),
				'widgetDomain' =>		'wp-user-control',
				'widgetLocale' => 		$my_locale,
				'widgetName' =>			'WP User Control Widget',
				'widgetSlug' =>			'wp-user-control-widget',
				'widgetStyle' =>		WP_USER_CONTROL_WIDGET_STYLE,
				'widgetStyleHandle' => 	'wp-user-control-widget-style'
				) );
			
			// add action hooks
			add_action(
				$tag = 'init',
				$function_to_add = 'wp_user_control_login_request',
				$priority = 10,
				$accepted_args = 1
				); // hook custom login function to init action
			
			// add filter hooks				
			add_filter(
				$tag = 'login_url',
				$function_to_add = 'wp_user_control_login_url_filter',
				$priority = 10,
				$accepted_args = 2
				); // hook login url

			 add_filter(
			 	$tag = 'wp_mail_from',
			 	$function_to_add = 'wp_user_control_mail_from_filter',
			 	$priority = 10,
			 	$accepted_args = 1
			 	); // hook mail from address
			 
			 add_filter(
		 		$tag = 'wp_mail_from_name',
		 		$function_to_add = 'wp_user_control_mail_from_name_filter',
		 		$priority = 10,
		 		$accepted_args = 1
			 	); // hook mail from name
			
		} // end constructor method
		
		/* -------------------------------------------------------------------------*/
		/* Private Functions
		/* -------------------------------------------------------------------------*/
		
	    /**
	    *
	    * method to set up default options for widget. Must be overidden by child class.
	    */
	    protected function setUpDefaults() {
	    	$this->defaults = array(
	    		'customLink' =>				'',
	    		'customLinkTitle' =>		'',
	    		'customLoginURL' =>			'',
	    		'defaultTab' =>				'login',
	    		'displayAvatar' =>			'enabled',
	    		'loginButtonLabel' =>		__( 'Login', 'wp-user-control' ),
	    		'loginTabLabel' =>			__( 'Login', 'wp-user-control' ),
	    		'mailFromAddress' =>		get_option( 'admin_email' ),
	    		'mailFromName' =>			get_option( 'blogname' ),
	    		'resetTabLabel' =>			__( 'Reset', 'wp-user-control' ),
	    		'resetButtonLabel' =>		__( 'Reset Password', 'wp-user-control' ),
	    		'registerButtonLabel' =>	__( 'Register', 'wp-user-control' ),
	    		'registerTabLabel' =>		__( 'Register', 'wp-user-control' ),
	    		'title' =>					__( 'WP User Control Widget', 'wp-user-control' )
	    		);
	    	$this->tabSelections = array(
    			'login' =>		__( 'Login', 'wp-user-control' ),
    			'register' => 	__( 'Register', 'wp-user-control' ),
    			'reset' => 		__( 'Reset', 'wp-user-control' )
	    		);
	    }
		
		/* -------------------------------------------------------------------------*/
		/* WP_Widget API Functions
		/* -------------------------------------------------------------------------*/
	    
		/** 
	     * Outputs widget settings form. Overrides WP_Widget::form().  Must be overidden by child class.
	     * 
	     * @param object $instance	array of widget options. 
	     */
		public function form( $instance ) {
			
			// merge instance options with defaults
			$instance = wp_parse_args( ( array ) $instance, $this->defaults );
			
			// output title text field
			$this->outputFormField( array( 
				'class' => 			'widefat',
				'label' =>			__( 'Title ', 'wp-user-control' ),
				'field' => 			'title',
				'type' => 			'text',
				'value' => 			$instance['title']
				) );
			// output fill background checkbox
			$this->outputFormField( array(
				'label' =>			__( 'Display Avatar ', 'wp-user-control' ), 
				'field' =>			'displayAvatar',
				'type' => 			'checkbox',
				'value' => 			$instance['displayAvatar']
				) );
			// output login tab label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Login Tab Label ', 'wp-user-control' ),
				'field' => 			'loginTabLabel',
				'type' => 			'text',
				'value' => 			$instance['loginTabLabel']
				) );
			// output login button label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Login Button Label ', 'wp-user-control' ),
				'field' => 			'loginButtonLabel',
				'type' => 			'text',
				'value' => 			$instance['loginButtonLabel']
				) );
			// output reset tab label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Reset Tab Label ', 'wp-user-control' ),
				'field' => 			'resetTabLabel',
				'type' => 			'text',
				'value' => 			$instance['resetTabLabel']
				) );
			// output reset button label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Reset Button Label ', 'wp-user-control' ),
				'field' => 			'resetButtonLabel',
				'type' => 			'text',
				'value' => 			$instance['resetButtonLabel']
				) );
			// output register tab label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Register Tab Label ', 'wp-user-control' ),
				'field' => 			'registerTabLabel',
				'type' => 			'text',
				'value' => 			$instance['registerTabLabel']
				) );
			// output register button label field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Register Button Label ', 'wp-user-control' ),
				'field' => 			'registerButtonLabel',
				'type' => 			'text',
				'value' => 			$instance['registerButtonLabel']
				) );
			// output default tab dropdown
			$this->outputFormField( array(
				'label' =>			__( 'Default Tab ', 'wp-user-control' ),
				'field' =>			'defaultTab',
				'my_selection' => 	$instance['defaultTab'],
				'selections' =>		$this->tabSelections,
				'style' =>			'width:80px',
				'type' => 			'dropdown'
				) );
			// output custom login url field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Custom Login URL ', 'wp-user-control' ),
				'field' => 			'customLoginURL',
				'type' => 			'text',
				'value' => 			$instance['customLoginURL']
				) );
			// output custom login url field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Custom Link Title', 'wp-user-control' ),
				'field' => 			'customLinkTitle',
				'type' => 			'text',
				'value' => 			$instance['customLinkTitle']
				) );
			// output custom login url field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'Custom Link URL', 'wp-user-control' ),
				'field' => 			'customLink',
				'type' => 			'text',
				'value' => 			$instance['customLink']
				) );
			// output custom email address field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'From Email Address ', 'wp-user-control' ),
				'field' => 			'mailFromAddress',
				'type' => 			'text',
				'value' => 			$instance['mailFromAddress']
				) );
			// output custom email address field
			$this->outputFormField( array(
				'class' => 			'widefat',
				'label' =>			__( 'From Email Name ', 'wp-user-control' ),
				'field' => 			'mailFromName',
				'type' => 			'text',
				'value' => 			$instance['mailFromName']
				) );
		}
		
		/** 
	     * Processes widget options to be saved. Overrides WP_Widget::update().  Must be overidden by child class.
	     * 
	     * @param object $new_instance	previous instance values before update 
	     * @param object $old_instance  new instance values to be saved via update 
	     * @return object $instance
	     */ 
		public function update( $new_instance, $old_instance ) {
			// set instance to point to old options array
			$instance = $old_instance;
			
			// record new instance values for option keys
			$instance['customLink'] = trim( strip_tags( $new_instance['customLink'] ) );
			$this->OPTIONS[$this->number]['customLink'] = $instance['customLink'];
			$instance['customLinkTitle'] = trim( strip_tags( $new_instance['customLinkTitle'] ) );
			$this->OPTIONS[$this->number]['customLinkTitle'] = $instance['customLinkTitle'];
			$instance['customLoginURL'] = trim( strip_tags( $new_instance['customLoginURL'] ) );
			$this->OPTIONS[$this->number]['customLoginURL'] = $instance['customLoginURL'];
			$instance['defaultTab'] = trim( strip_tags( $new_instance['defaultTab'] ) );
			$this->OPTIONS[$this->number]['defaultTab'] = $instance['defaultTab'];
			$instance['displayAvatar'] = ( $new_instance['displayAvatar'] == 'enabled' ) ? trim( strip_tags( $new_instance['displayAvatar'] ) ) : 'disabled';
			$this->OPTIONS[$this->number]['displayAvatar'] = $instance['displayAvatar'];
			$instance['loginButtonLabel'] = trim( strip_tags( $new_instance['loginButtonLabel'] ) );
			$this->OPTIONS[$this->number]['loginButtonLabel'] = $instance['loginButtonLabel'];
			$instance['loginTabLabel'] = trim( strip_tags( $new_instance['loginTabLabel'] ) );
			$this->OPTIONS[$this->number]['loginTabLabel'] = $instance['loginTabLabel'];
			$instance['mailFromAddress'] = trim( strip_tags( $new_instance['mailFromAddress'] ) );
			$this->OPTIONS[$this->number]['mailFromAddress'] = $instance['mailFromAddress'];
			$instance['mailFromName'] = trim( strip_tags( $new_instance['mailFromName'] ) );
			$this->OPTIONS[$this->number]['mailFromName'] = $instance['mailFromName'];			
			$instance['registerButtonLabel'] = trim( strip_tags( $new_instance['registerButtonLabel'] ) );
			$this->OPTIONS[$this->number]['registerButtonLabel'] = $instance['registerButtonLabel'];
			$instance['registerTabLabel'] = trim( strip_tags( $new_instance['registerTabLabel'] ) );
			$this->OPTIONS[$this->number]['registerTabLabel'] = $instance['registerTabLabel'];
			$instance['resetButtonLabel'] = trim( strip_tags( $new_instance['resetButtonLabel'] ) );
			$this->OPTIONS[$this->number]['resetButtonLabel'] = $instance['resetButtonLabel'];
			$instance['resetTabLabel'] = trim( strip_tags( $new_instance['resetTabLabel'] ) );
			$this->OPTIONS[$this->number]['resetTabLabel'] = $instance['resetTabLabel'];
			$instance['title'] = trim( strip_tags( $new_instance['title'] ) );
			$this->OPTIONS[$this->number]['title'] = $instance['title'];
			
			// return updated options array
			return $instance;
		}
		
		/** 
	     * Outputs widget content. Overrides WP_Widget::widget(). Must be overidden by child class.
	     * 
	     * @param array $args		array of form elements 
	     * @param object $instance	widget instance
	     */
		public function widget( $args, $instance ) {
			$username = null;
			extract( $args );
			// output custom WP widget wrapper
			echo $before_widget;
			// output title based on option input
			$title = apply_filters( 'widget_title', $instance['title'] );
			$customLink = empty( $instance['customLink'] ) ? $this->defaults['customLink'] : $instance['customLink'];
			$customLinkTitle = empty( $instance['customLinkTitle'] ) ? $this->defaults['customLinkTitle'] : $instance['customLinkTitle'];
			$default_tab = empty( $instance['defaultTab'] ) ? $this->defaults['defaultTab'] : $instance['defaultTab'];
			$displayAvatar = empty( $instance['displayAvatar'] ) ? $this->defaults['displayAvatar'] : $instance['displayAvatar'];
			$loginButtonLabel = empty( $instance['loginButtonLabel'] ) ? $this->defaults['loginButtonLabel'] : $instance['loginButtonLabel'];
			$loginTabLabel = empty( $instance['loginTabLabel'] ) ? $this->defaults['loginTabLabel'] : $instance['loginTabLabel'];
			$registerButtonLabel = empty( $instance['registerButtonLabel'] ) ? $this->defaults['registerButtonLabel'] : $instance['registerButtonLabel'];
			$registerTabLabel = empty( $instance['registerTabLabel'] ) ? $this->defaults['registerTabLabel'] : $instance['registerTabLabel'];
			$resetButtonLabel = empty( $instance['resetButtonLabel'] ) ? $this->defaults['resetButtonLabel'] : $instance['resetButtonLabel'];
			$resetTabLabel = empty( $instance['resetTabLabel'] ) ? $this->defaults['resetTabLabel'] : $instance['resetTabLabel'];
			// set default active tab
			$active_tab = $default_tab;
		    // output widget title with WP wrapper
		    if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		    // output html
		    try {
		    	?>
    					<div id="wp-user-control-login-register-password">
    					
    					<?php 
    						global $user_ID, $blog_id; 
    						get_currentuserinfo();
    						global $user_login, $user_email;
    						// if user is not already logged in...
    						if ( !$user_ID ) { 
    							// grab POST variables
    							$login = ( array_key_exists( 'login', $_GET ) ) ? trim( $_GET['login'] ) : false;
    							$register = ( array_key_exists( 'register', $_GET ) ) ? trim( $_GET['register'] ) : false;
    							$reset = ( array_key_exists( 'reset', $_GET ) ) ? trim( $_GET['reset'] ) : false;
    							?>
	    						<?php // Output tabs ?>
	    						<ul class="tabs_login">
	    							<li id="login_tab"><a href="#login_div"><?php echo $loginTabLabel; ?></a></li>
	    							<li id="register_tab"><a href="#register_div"><?php echo $registerTabLabel; ?></a></li>
	    							<li id="reset_tab"><a href="#reset_div"><?php echo $resetTabLabel; ?></a></li>
	    						</ul>
	    						<div class="tab_container_login">
	    							<?php // LOGIN FORM BEGIN ?>
	    							<div id="login_div" class="tab_content_login" style="display:none;">
	    								<?php // handle user signon failure
										if ( $login == 'failed' ) {
											$user_login = ( array_key_exists( 'user_login', $_REQUEST ) ) ? trim( $_REQUEST['user_login'] ) : false;
											$active_tab = 'login';
											?>
	    									<p><span class="loginfail">
	    									<?php _e( 'Please check your username and password.', 'wp-user-control' ); ?>
	    									</span></p>
	    									<?php
										} else { ?>
	    									<p>
	    									<?php _e( 'Enter your username and password below to login.', 'wp-user-control' ); ?>
	    									</p><?php 
	    								} ?>
	    								<form method="post" action="<?php echo wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ) . '?wp_uc_login_request=true'; ?>" class="wp-user-form">
	    									<div class="username">
	    										<label for="user_login"><?php _e( 'Username', 'wp-user-control' ); ?>: </label>
	    										<input type="text" name="user_login" value="<?php 
			    									if ( !isset( $username ) ) {
			    										echo trim( stripslashes( $user_login ) );
			    									} else {
			    										echo trim( stripslashes( $username ) );
			    									}  								
	    										?>" id="user_login" tabindex="11" />
	    									</div>
	    									<div class="password">
	    										<label for="user_pass"><?php _e( 'Password', 'wp-user-control' ); ?>: </label>
	    										<input type="password" name="user_pass" value="" id="user_pass" tabindex="12" />
	    									</div>
	    									<div class="login_fields">
	    										<div class="remember">
	    											<label for="remember">
	    												<input type="checkbox" name="remember" value="forever" checked="checked" id="remember" tabindex="13" />&nbsp;<?php _e( 'Remember me', 'wp-user-control' ); ?>
	    											</label>
	    										</div>
	    										<?php do_action( 'login_form' ); ?>
	    										<input type="submit" name="user-submit" value="<?php echo $loginButtonLabel; ?>" tabindex="14" class="user-submit" />
	    										<input type="hidden" name="redirect_to" value="<?php echo wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ); ?>" />
	    										<input type="hidden" name="user-cookie" value="1" />
	    									</div>
	    								</form>
	    							</div>
	    							<?php // LOGIN FORM END ?>
									<?php // REGISTRATION FORM BEGIN ?>
	    							<div id="register_div" class="tab_content_login" style="display:none;">
	    								<?php 
	    								// if register == true then set register as the active tab
	    								if ( $register == 'true' ) {
	    									$active_tab = 'register';
	    								}
	    								// set default for register error to none
	    								$register_error = 'none';
	    								// first, determine user registration setting for site
	    								if ( is_multisite() ) {
											// make sure user registration is enabled
											$active_signup = get_site_option( 'registration' );
											// if signup option doesn't exist assume everything is enabled (blog and user signup)
											if ( !$active_signup ) {
												$active_signup = 'all';
											}
											// determine specifics of what is enabled
											$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"
											
											// if registration is enabled, proceed --- "all" or "user"
											if ( $active_signup == 'all' || $active_signup == 'user' ) {
												$registrations_disabled = false;
											} else {
												$registrations_disabled = true;
											}
										// if not multisite, check user registration option for standard install
										} else {
	    									$registrations_disabled = ( get_option( 'users_can_register' ) ) ? false : true;
	    								}
	    								
	    								// check registration honey pot
	    								$reg_pot = ( array_key_exists( 'reg_pot', $_REQUEST ) ) ? trim( $_REQUEST['reg_pot'] ) : false;
										// grab desired user name and email
										$user_login = ( array_key_exists( 'user_login', $_REQUEST ) ) ? trim( $_REQUEST['user_login'] ) : false;
										$user_email = ( array_key_exists( 'user_email', $_REQUEST ) ) ? trim( $_REQUEST['user_email'] ) : false;
										
										/**
										 * TODO: implement email validation function to check for valid email address format
										 */
										
										
										if ( !empty( $reg_pot ) ) {
											$register_error = 'honeypot';
											// reset register flag
											$register = 'false';
										} elseif ( empty( $user_login ) && !empty( $register ) || empty( $user_email ) && !empty( $register ) ) {
											$register_error = 'empty_fields';
											// reset register flag
											$register = 'false';
										// make sure user is not already signed in
										} elseif ( is_user_logged_in() && !empty( $register ) ) {
											// if they are then return an error message and we're done
											$register_error = 'logged_in';
											// reset register flag
											$register = 'false';
										// if registration has actually been submitted, proceed
										} elseif ( $register ) {
											if ( username_exists( $user_login ) ) {
												$register_error = 'username_exists';
												// reset register flag
												$register = 'false';
											// make sure user email is not already registered
											} elseif ( email_exists( $user_email ) ) {
												$register_error = 'email_exists';
												// reset register flag
												$register = 'false';
											// check for uppercase
											} elseif ( preg_match( "/[A-Z]/", $user_login ) ) {
												$register_error = 'uppercase';
												// reset register flag
												$register = 'false';
											// check for spaces
											} elseif ( strpos( $user_login, " " ) !== false ) {
												$register_error = 'spaces';
												// reset register flag
												$register = 'false';
											// otherwise proceed with registration checks
											} else {
												// make sure user registration is enabled
												if ( !$registrations_disabled ) {
													// set flag for successful registration
													$register = 'true';
													// generate temp password
													$temp_password = wp_PluginUtilities::generatePassword();
													// check for WPMS
													if ( is_multisite() ) {
														// register user for WPMS
														wpmu_create_user( $user_login, $temp_password, $user_email );
														// get user info after it has been created
														if ( $user = get_user_by( 'login', $user_login ) ) {
															// add user to current blog as subscriber
															add_user_to_blog( $blog_id, $user->id, 'subscriber' );
														}
													// otherwise this is a standard WP install
													} else {
														// register user for WP standard
														wp_create_user( $user_login, $temp_password, $user_email );
													}
													
													// send user notification email
													$message = wp_user_control_user_email_msg( 'new', $temp_password, home_url(), $user_login );
													// send new user registration email meassage
													wp_user_control_mail( $message, 'New User Registration', $user_email );
														
												// otherwise, we're done - return message to WP User Control widget
												} else {
													$register_error = 'registration_disabled';
													// reset register flag
													$register = 'false';
												}
											}
										}
	
	    								// if registration attempt returned success
	    								if ( $register == 'true' ) { 
	    									?>
	    									<p><?php _e( 'Check your email for the password and then return to log in.', 'wp-user-control' ); ?></p> <?php 
	    								// if registration request has not been sent, output initial message
										} elseif ( $register_error == 'none' ) {
											?><p><?php _e( 'Complete the form below to register.', 'wp-user-control' ); ?></p><?php
										// if registration request failed, process error
										} elseif ( $register == 'false' ) {
											$registerError = $register_error;
											// output friendly registration error
											wp_user_control_output_registration_error( $registerError );			
	    								// other possibility is that user registrations are currently disabled
	    								} elseif ( $registrations_disabled ) {
	    									?><p><?php _e( 'New registrations currently disabled.', 'wp-user-control' ); ?></p><?php
	    								}
	    								
	    								?>
	    								<form method="post" action="<?php echo wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ) . '?register=true';
										?>" class="wp-user-form">
	    									<div class="username">
	    										<label for="user_login"><?php _e( 'Username', 'wp-user-control' ); ?>: </label>
	    										<input type="text" <?php if ( $registrations_disabled ) { ?> disabled="disabled" <?php } ?> name="user_login" value="<?php 
	    											echo stripslashes( $user_login ); ?>" id="user_login" tabindex="101" />
	    									</div>
	    									<div class="password">
	    										<label for="user_email"><?php _e( 'Email', 'wp-user-control' ); ?>: </label>
	    										<input type="text" <?php if ( $registrations_disabled ) { ?> disabled="disabled" <?php } ?> name="user_email" value="<?php 
	    											echo stripslashes( $user_email ); ?>" id="user_email" tabindex="102" />
	    									</div>
	    									<div class="reg_pot">
	    										<input type="text" name="reg_pot" value="" alt="if this field is not empty your registration will not be processed" />
	    									</div>
	    									<div class="login_fields">
	    										<?php do_action( 'register_form' ); ?>
	    										<input type="submit" name="user-submit" value="<?php echo $registerButtonLabel; ?>" <?php if ( $registrations_disabled ) { ?> disabled="disabled" <?php } ?> class="user-submit" tabindex="103" />
	    										<input type="hidden" name="redirect_to" value="<?php echo wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ); ?>?register=true" />
	    										<input type="hidden" name="user-cookie" value="1" />
	    									</div>
	    								</form>
	    							</div>
	    							<?php // REGISTRATION FORM END ?>
	    							<?php // RESET FORM BEGIN ?>
	    							<div id="reset_div" class="tab_content_login" style="display:none;"><?php
	    								
	    								if ( $reset == 'true' ) {
	    									$active_tab = 'reset';
	    									global $wpdb;
	    									$user_email = ( array_key_exists( 'user_email', $_POST ) ) ? trim( $_POST['user_email'] ) : null;
	    									$user_exists = false;
	    									if ( !empty( $user_email ) ) {
		    									// check for email
		    									if ( email_exists( $user_email ) ) {
		    										$user_exists = true;
		    										$reset_user = get_user_by( 'email', $user_email );
		    									// otherwise, user does not exist
		    									} else {
		    										$error[] = '<p><span class="registerfail">' . __( 'Email does not exist.', 'wp-user-control' ) . '</span></p>';
		    										$reset = false;
		    									}
	    									} else {
	    										$error[] = '<p><span class="registerfail">' . __( 'Invalid email. Please try again.', 'wp-user-control' ) . '</span></p>';
	    									}
	    									// if user exists, then proceed
	    									if ( $user_exists ) {
	    										$user_login = $reset_user->user_login;
	    										$user_email = $reset_user->user_email;
	    										// generate password
	    										$temp_password = wp_PluginUtilities::generatePassword();
	    										// insert new password into WP DB
	    										wp_update_user( array( 'ID' => $reset_user->ID, 'user_pass' => $temp_password ) );
	    										// create password reset email message
	    										$message = wp_user_control_user_email_msg( 'reset', $temp_password, home_url(), $user_login );
												wp_user_control_mail( $message, 'Password Reset', $user_email );
	    									}
	    									// output errors, if appropriate
	    									if ( isset( $error) && count( $error ) > 0 ) {
	    										foreach ( $error as $e ) {
	    											echo $e;
	    										}
	    										$reset = false;
	    									// otherwise password reset was successful, so output message
	    									} else { ?>
	    										<p><?php _e( 'Check your email for your new password.', 'wp-user-control' ); ?></p><?php 
	    									}
	    								} else { ?>
	    									<p><?php _e( 'Enter your email address to reset your password.', 'wp-user-control' ); ?></p><?php 
	    								} ?>
	    								<form method="post" action="<?php echo wp_user_control_cleanURI( $_SERVER['REQUEST_URI'] ); ?>?reset=true" class="wp-user-form">
	    									<div class="username">
	    										<label for="user_email" class="hide"><?php _e( 'Email', 'wp-user-control' ); ?>: </label>
	    										<input type="text" name="user_email" value="<?php 
	    											if ( !empty( $user_email ) ) {
	    												echo $user_email;
	    											}
	    										?>" id="user_email" tabindex="1001" />
	    									</div>
	    									<div class="login_fields">
	    										<?php do_action( 'login_form', 'resetpass' ); ?>
	    										<input type="submit" name="user-submit" value="<?php echo $resetButtonLabel; ?>" class="user-submit" tabindex="1002" />
		    										<input type="hidden" name="user-cookie" value="1" />
		    									</div>
		    								</form>
		    							</div>
		    						</div>
    					<?php // RESET FORM END ?>
    					<?php // LOGGED IN USER BEGIN ?>
    					<?php 
						} else { // is logged in
    						// output logged in user control box
    						wp_user_control_logged_in_user( $customLink, $customLinkTitle, $displayAvatar );    					
    					} ?>
    					<!-- WP User Control Widget JS -->
    					<script type="text/javascript">
						wp_user_control_widget_js( '<?php echo $active_tab; ?>' );
    					</script>
    					<!-- WP User Control Widget JS -->
    				</div>
    				<?php 
				
		    } catch ( wp_PluginException $e ) {
		    	echo $e->getError();
		    }
			// output custom WP widget wrapper
			echo $after_widget;
		}
	}
}

// hook into widgets_init action
if ( class_exists( 'wp_user_control_widget' ) ) {
	add_action('widgets_init', create_function('', 'register_widget("wp_user_control_widget");'));
}
?>