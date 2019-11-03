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

if ( !class_exists( 'wp_PluginUtilities' ) ) {

	/** wp_PluginUtilities class
	 *
	 * static PHP class for general tools/utilities.
	 * @author wmsedgar
	 *
	 */
	abstract class wp_PluginUtilities {
		/* public variables */
		
		/* private variables */
		
		/**
		 * old style constructor method for backward PHP compatibility
		 */ 
		public function wp_PluginUtilities() {
			$this->__construct();
		}
		
		/**
		 * public constructor method
		 */
		public function __construct() {
			
		}
		
		/*
		 * private functions
		 */
		
		/*
		 * public functions
		 */
		
		/**
		*
		* Get html from external file using file_get_contents, return html output.
		*
		* @param string $target
		* @param array $replacement_array
		*
		* @return string $html
		*/
		public function getFilteredHtml( $target, $replacement_array = array() ) {
			// output linechart settings begin html
			$html = file_get_contents(
			$filename = $target,
			$use_include_path = false
			);
			if ( !$html ) {
				throw new Exception( 'wp_PluginUtilities::getFilteredHtml unable to load contents from ' . $target );
			} else {
				if ( !empty( $replacement_array ) ) {
					foreach ( $replacement_array as $key => $value ) {
						$html = str_replace( $key, $value, $html );
					}
				}
				return $html;
			}
		}
		
		/**
		 * 
		 * Generate random password string of desired length
		 * 
		 * @param number $length
		 * @return string
		 */
		public function generatePassword ($length = 10) {
		
			// start with a blank password
			$password = "";
		
			// define possible characters - any character in this string can be
			// picked for use in the password, so if you want to put vowels back in
			// or add special characters such as exclamation marks, this is where
			// you should do it
			$possible = "0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
		
			// we refer to the length of $possible a few times, so let's grab it now
			$maxlength = strlen( $possible );
		
			// check for length overflow and truncate if necessary
			if ( $length > $maxlength ) {
				$length = $maxlength;
			}
		
			// set up a counter for how many characters are in the password so far
			$i = 0;
		
			// add random characters to $password until $length is reached
			while ( $i < $length ) {
		
				// pick a random character from the possible ones
				$char = substr( $possible, mt_rand( 0, $maxlength-1 ), 1 );
		
				// have we already used this character in $password?
				if ( !strstr( $password, $char ) ) {
					// no, so it's OK to add it onto the end of whatever we've already got...
					$password .= $char;
					// ... and increase the counter by one
					$i++;
				}
		
			}
		
			// done!
			return $password;
		
		}
		
		
		/**
		 *
		 * method to check if array is associative
		 *
		 * @param array $arr
		 *
		 * @return boolean
		 */
		public function isAssociative( $arr ) {
			if ( is_array( $arr ) ) {
				foreach ( $arr as $key => $value ) {
					if ( !is_int( $key ) ) {
						return true;
					}
				}
			}
			return false;
		}
		
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
		
	} // end class wp_PluginUtilities
	
} // end if class exists
?>