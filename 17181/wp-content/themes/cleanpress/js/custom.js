!function(a){a.fn.inkEqual=function(){var b=0,c=a(this);return c.each(function(){var c=a(this).innerHeight();c>b&&(b=c)}),c.css("height",b)},a("[data-equal]").each(function(){var b=a(this),c=b.data("equal");b.find(c).inkEqual()})}(jQuery);    
jQuery(document).ready(function() {
	//Set up the Slider 
	/*
if (jQuery(window).width() > 991 ) {
		setTimeout(function() {
			for (var i = 0; i < 15; i++) {
				jQuery('#primary-home .row-'+i+' article').inkEqual();
				}
	      }, 1250);
	 }  
*/ 
	
/*
	jQuery('.main-navigation ul.menu').mobileMenu({
		switchWidth: 768
	});
*/
	jQuery('#site-navigation li').find('ul').hide();
	jQuery('#site-navigation li').hover(
		function(){
			
			jQuery(this).find('> ul').slideDown('fast');
		},
		function(){
			jQuery(this).find('ul').hide();
		});	
	
	jQuery('.menu-toggle').toggle(function() {
			jQuery('#site-navigation ul.menu').slideDown();
			jQuery('#site-navigation div.menu').fadeIn();
		},
		function() {
			jQuery('#site-navigation ul.menu').hide();
			jQuery('#site-navigation div.menu').hide();
	});
	
	jQuery(".top-search-icon").toggle(
	
		function(){
			jQuery("#top-search-form").slideDown('slow');
			jQuery("#top-navigation, #header-top,#header-2,.slider-wrapper, #content,.cancel-search").click(function() { jQuery("#top-search-form").slideUp('fast'); });
		},
		function(){
			jQuery("#top-search-form").slideUp('fast');
			
		});
});	