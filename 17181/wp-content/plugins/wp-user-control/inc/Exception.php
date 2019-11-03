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

if ( !class_exists( 'wp_PluginException' ) ) {

	/** wp_PluginException class
	 *
	 * PHP class for custom exception handling.
	 * @author wmsedgar
	 *
	 */
	class wp_PluginException extends Exception {
		/* public variables */
		
		/* private variables */
		private $msg = 				null;
		/**
		 * old style constructor method for backward PHP compatibility
		 */
		public function wp_PluginException ( $msg ) {
			$this->__construct( $msg );
		}

		/**
		 * public constructor method
		 */
		public function __construct( $msg ) {
			$this->msg = '<p>Error on line '.$this->getLine().' in '.$this->getFile()
		    .' : <b>' . $msg . '</b></p>';
		}
		
		public function getError() {
			return $this->msg;
		}
		
	} // end class wp_PluginException
	
} // end if class exists

?>