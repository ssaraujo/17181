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

function wp_user_control_widget_js( target ) {

	jQuery( ".tab_content_login" ).hide();
	jQuery( "ul.tabs_login #" + target + "_tab" ).addClass( "active_login" ).show();
	jQuery( "#" + target + "_div" ).show();
	jQuery( "ul.tabs_login li" ).click(function() {
		jQuery( "ul.tabs_login li" ).removeClass( "active_login" );
		jQuery( this ).addClass( "active_login" );
		jQuery( ".tab_content_login" ).hide();
		var activeTab = jQuery( this ).find( "a" ).attr( "href" );
		if ( navigator.appVersion.indexOf( "MSIE" ) !== -1 ) {
			jQuery( activeTab ).show();
		} else { 
			jQuery( activeTab ).show();
		}
		return false;
		});
	
}