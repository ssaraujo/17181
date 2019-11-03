<?php

/*
	WP Sidebar Widget Base Class
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

if ( !class_exists( 'wp_SidebarWidget' ) ) {
	
	// wp_SidebarWidget class
	abstract class wp_SidebarWidget extends WP_Widget {
		
		/*
		 * public variables
		 */
		
		/*
		 * private variables
		 */
		
		// set up default options array
		protected $defaults = 			array();
		// set up options array
		protected $options = 			array();
		// set up core widget variables
		protected $widgetDescription = 	null;
		protected $widgetLocale = 		null;
		protected $widgetName =			null;
		protected $widgetSlug = 		null;
		protected $widgetStyle = 		null;
		protected $widgetStyleHandle =  null;
		
		/* -------------------------------------------------------------------------*/
		/* Constructor
		/* -------------------------------------------------------------------------*/
		
		/*
		 * Widget constructor method. Must be overridden in child class.
		 */
		/*
		public function wp_sidebarWidget() {
			// initialize constants and widget specific settings
			$this->init( $args );
			
		} // end constructor method
		*/
		
		/* -------------------------------------------------------------------------*/
		/* Private Functions
		/* -------------------------------------------------------------------------*/
		
		public function init( $args ) {
			extract( $args );
			$this->widgetDescription = 	$widgetDescription;
			$this->widgetDomain = 		$widgetDomain;
			$this->widgetLocale = 		$widgetLocale;
			$this->widgetName = 		$widgetName;
			$this->widgetSlug = 		$widgetSlug;
			$this->widgetStyle = 		$widgetStyle;
			$this->widgetStyleHandle = 	$widgetStyleHandle;
			$this->setUpDefaults();
			// set up options array
			$widget_opts = array(
				'classname' => 			$this->widgetName,
				'description' => 		__( $this->widgetDescription, $this->widgetLocale ) 
				);
			// load plugin text domain
			load_plugin_textdomain(
				$domain = 				$widgetDomain,
				$abs_rel_path = 		false,
				$plugin_rel_path = 		'wp-user-control/languages'
				);
			// pass widget ID, description, and options array to WP_Widget class
			$this->WP_Widget(
				$id_base = $this->widgetSlug,
				$name = __( $this->widgetName, $this->widgetLocale ),
				$widget_options = $widget_opts
				);
			// load widget options
			$this->initOptions();
			// print stylesheet
			add_action(
				$tag = 'wp_head',
				$callback = array( &$this, 'addWidgetStyle' ),
				$priority = 1
				);
		}
	    
		/**
		 * Init options method. Initializes options array for this instance.
		 */
		protected function initOptions() {
			// initialize options array for this instance
			$this->options[$this->number] = array();
			// retrieve options if already saved, otherwise load defaults 
			// first get options array from wordpress database
			$arr = get_option(
				$show = $this->option_name,
				$default = false
				);
			// check to see if option array was successfully retrieved
			if ( $arr !== false ) {
				// loop through options array from database
				foreach ( $arr as $instance_id => $properties ) {
					// make sure we're dealing with a valid instance id
					if ( is_numeric( $instance_id ) && count( $properties ) > 1 ) {
						// initialize options array for this instance
						$this->options[$instance_id] = array();
						// loop through default option keys
						foreach ( $this->defaults as $key => $value ) {
							// if key exists in options array, save it
							if ( array_key_exists( $key, $properties ) ) {
								$this->options[$instance_id][$key] = $properties[$key];
							// otherwise, save default value
							} else {
								$this->options[$instance_id][$key] = $value;
							}
						}
					} // end if $instance_id is numeric
				} // end foreach $arr
			} // end if $arr !== false
		} // end method init_options
		
	    /** 
	     * Helper function for outputting form fields. 
	     * 
	     * @args array 		array of field attributes (name, id, class, type, value, onchange,
	     * onclick, onload, readonly, size, my_selection) 
	     */
	    protected function outputFormField( $args ) {
	    	// explode args array into directly addressable variables
	    	extract( $args );
	    	// check class, onchange, onclick, onload, and readonly properties
			$class = ( isset( $class ) ) ? 'class="' . $class . '" ' : '';
			$cols = ( isset( $cols ) ) ? 'cols="' . $cols . '" ' : '';
			$multiple = ( isset( $multiple ) ) ? $multiple : '';
			$onchange = ( isset( $onchange ) ) ? 'onchange="' . $onchange . '()" ' : '';
			$onclick = ( isset( $onclick ) ) ? 'onclick="' . $onclick . '()" ' : '';
			$onload = ( isset( $onload ) ) ? 'onload="' . $onload . '()" ' : '';
			$readonly = ( isset( $readonly ) ) ? 'readonly="' . $readonly . '" ' : '';
			$rows = ( isset( $rows ) ) ? 'rows="' . $rows . '" ' : '';
			$size = ( isset( $size ) ) ? 'size="' . $size . '" ' : '';
			$style = ( isset( $style ) ) ? 'style="' . $style .'" ' : '';
			if ( isset( $value ) ) {
				if ( $type === 'checkbox' ) {
					// detect check box and set value
					$checked = ( $value === 'enabled' ) ? 'checked="checked" ' : '';
					$value = 'value="enabled" ';
				} else {
					$value = 'value="' . $value . '" ';
					$checked = '';
				}
			} else {
				$value = '';
				$checked = '';
			}
			
			// determine open and close tags based on type
			if ( $type === 'dropdown' || $type === 'multi-select' ) {
				$open_tag = '<p><label for="' . $this->get_field_id( $field ) . '">' . $label . 
					'</label><select id="';
				$close_tag = '>';
			} else {
				$open_tag = '<p><label for="' . $this->get_field_id( $field ) . '">' . $label . '</label><input id="';
				$close_tag = ' />';
			}
			// alter name if it is a multi-select for array
			if ( $type != 'multi-select' ) {
				$name = 'name="' . $this->get_field_name( $field ) . '" ';
			} else {
				$name = 'name="' . $this->get_field_name( $field ) . '[]" ';
			}
			// output field
			$field = $open_tag . $this->get_field_id( $field ) . '" ' .
				$name .
				$checked .
				$class .
				$cols .
				$multiple .
				$onchange .
				$onclick .
				$onload .
				$readonly .
				$rows . 
				$size .
				$style;
			if ( $type != 'multi-select' ) {
				$field .= ' type="' . $type . '" ';
			}
			$field .= $value . $close_tag;
			echo $field;
			// continue to populate dropdown
			if ( $type === 'dropdown' || $type === 'multi-select' ) {
				// check to make sure selections is an array
				if ( is_array( $selections ) ) {
					if ( !wp_PluginUtilities::isAssociative( $selections ) ) {
						foreach ( $selections as $key => $item ) {
							if ( $type === 'dropdown' ) {
								$selected = ( $my_selection == $item ) ? 'selected="selected"' : '';
								echo "<option value='$item' $selected>$item</option>";
							} else {
								$selected = '';
								if ( is_array( $my_selections ) ) {
									foreach ( $my_selections as $selection ) {
										if ( $selection === $item ) {
											$selected = 'selected="selected"';
											break;
										}
									}
								}
								echo "<option value='$item' $selected>$item</option>";
							}
						}
					} else {
						foreach ( $selections as $key => $item ) {
							if ( $type === 'dropdown' ) {
								$selected = ( $my_selection == $key ) ? 'selected="selected"' : '';
								echo "<option value='$key' $selected>$item</option>";
							} else {
								$selected = '';
								if ( is_array( $my_selections ) ) {
									foreach ( $my_selections as $selection ) {
										if ( $selection == $key ) {
											$selected = 'selected="selected"';
											break;
										}
									}
								}
								echo "<option value='$key' $selected>$item</option>";
							}
						}
					}
					
				}
				echo "</select>";
			}
			echo "</p>";
	    }
		
	    /**
	     * 
	     * method to set up default options for widget. Must be overidden by child class.
	     */
	    protected function setUpDefaults() {
	    	
	    }
	    
		/* -------------------------------------------------------------------------*/
		/* WP_Widget API Functions
		/* -------------------------------------------------------------------------*/
		
	    // add widget style
	    public function addWidgetStyle() {
	    	$style = WP_USER_CONTROL_WIDGET_CSS . $this->widgetStyle;
	    	wp_register_style(
		    	$handle = $this->widgetStyleHandle,
		    	$src = $style
		    	);
	    	wp_enqueue_style( 
	    		$handle = $this->widgetStyleHandle
	    		);
	    }
	    
		/** 
	     * Outputs widget settings form. Overrides WP_Widget::form().  Must be overidden by child class.
	     * 
	     * @param object $instance	array of widget options. 
	     */
		public function form( $instance ) {
			/*
			 * first this function must merge instance options with defaults:
			 * $instance = wp_parse_args( ( array ) $instance, $this->defaults );
			 *
			 * then output each field for form.
			 */
		}
		
		/** 
	     * Processes widget options to be saved. Overrides WP_Widget::update().  Must be overidden by child class.
	     * 
	     * @param object $new_instance	previous instance values before update 
	     * @param object $old_instance  new instance values to be saved via update 
	     * @return object $instance
	     */ 
		public function update( $new_instance, $old_instance ) {
			/*
			* first this function must set $instance to point at $old_instance:
			* $instance = $old_instance;
			*
			* then process each option individually:
			* 
			* $instance['option_name'] = $new_instance['option_name'];
			* $this->options[$this->number]['option_name'] = $instance['option_name'];
			* 
			*/
		}
		
		/** 
	     * Outputs widget content. Overrides WP_Widget::widget(). Must be overidden by child class.
	     * 
	     * @param array $args		array of form elements 
	     * @param object $instance	widget instance
	     */
		public function widget( $args, $instance ) {
			/*
			 * extract( $args );
			 * echo $before_widget;
			 * if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
			 * 
			 * MAIN WIDGET CONTENT
			 * 
			 * echo $after_widget;
			 */
		}
	}
}
?>