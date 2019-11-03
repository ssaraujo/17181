<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = wp_get_theme();
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'cleanpress'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	$options = array();
	$imagepath =  get_template_directory_uri() . '/images/';
	
	//Basic Settings
	
	$options[] = array(
		'name' => __('Basic Settings', 'cleanpress'),
		'type' => 'heading');
			
	$options[] = array(
		'name' => __('Site Logo', 'cleanpress'),
		'desc' => __('Leave Blank to use text Heading.', 'cleanpress'),
		'id' => 'logo',
		'class' => '',
		'type' => 'upload');	
		
	$options[] = array(
		'name' => __('Copyright Text', 'cleanpress'),
		'desc' => __('Some Text regarding copyright of your site, you would like to display in the footer.', 'cleanpress'),
		'id' => 'footertext2',
		'std' => '',
		'type' => 'text');
		
	$options[] = array(
		'desc' => __('To have more customization options including Favicon, Analytics, Custom Scripts, etc. <a href="http://rohitink.com/product/cleanpress-pro" target="_blank">Upgrade to Pro</a> at Just $19.45. Pro Version also supports customization of theme with unlimited colors. With CleanPress Pro, you can design your own unique theme.', 'cleanpress'),
		'std' => '',
		'type' => 'info');	
				
	//Design Settings
		
	$options[] = array(
		'name' => __('Layout Settings', 'cleanpress'),
		'type' => 'heading');	
	
	$options[] = array(
		'name' => "Sidebar Layout",
		'desc' => __('Select Layout for Posts & Pages.', 'cleanpress'),
		'id' => "sidebar-layout",
		'std' => "right",
		'type' => "images",
		'options' => array(
			'left' => $imagepath . '2cl.png',
			'right' => $imagepath . '2cr.png')
	);
	
	$options[] = array(
		'name' => __('Custom CSS', 'cleanpress'),
		'desc' => __('Some Custom Styling for your site. Place any css codes here instead of the style.css file.', 'cleanpress'),
		'id' => 'style2',
		'std' => '',
		'type' => 'textarea');
	
	//SLIDER SETTINGS

	$options[] = array(
		'name' => __('Slider Settings', 'cleanpress'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Enable Slider', 'cleanpress'),
		'desc' => __('Check this to Enable Slider.', 'cleanpress'),
		'id' => 'slider_enabled',
		'type' => 'checkbox',
		'std' => '0' );
		
	$options[] = array(
		'name' => __('Using the Slider', 'cleanpress'),
		'desc' => __('This Slider supports upto 5 Images. To show only 3 Slides in the slider, upload only 3 images. Leave the rest Blank. For best results, upload images of same size.', 'cleanpress'),
		'type' => 'info');

	$options[] = array(
		'desc' => __('To Customize the the slider with more settings like Slider Speed, Transition Effects, Transition Duration, Adaptive Heights, Random Start, AutoPlay, etc. <a href="http://rohitink.com/product/cleanpress-pro" target="_blank">Upgrade to Pro</a> at Just $19.45. Pro Version also supports More than 3 Slides.', 'cleanpress'),
		'std' => '',
		'type' => 'info');	
		
	$options[] = array(
		'name' => __('Slider Image 1', 'cleanpress'),
		'desc' => __('First Slide', 'cleanpress'),
		'id' => 'slide1',
		'class' => '',
		'type' => 'upload');
	
	$options[] = array(
		'desc' => __('Title', 'cleanpress'),
		'id' => 'slidetitle1',
		'std' => '',
		'type' => 'text');
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'cleanpress'),
		'id' => 'slidedesc1',
		'std' => '',
		'type' => 'textarea');			
		
	$options[] = array(
		'desc' => __('Url', 'cleanpress'),
		'id' => 'slideurl1',
		'std' => '',
		'type' => 'text');		
	
	$options[] = array(
		'name' => __('Slider Image 2', 'cleanpress'),
		'desc' => __('Second Slide', 'cleanpress'),
		'class' => '',
		'id' => 'slide2',
		'type' => 'upload');
	
	$options[] = array(
		'desc' => __('Title', 'cleanpress'),
		'id' => 'slidetitle2',
		'std' => '',
		'type' => 'text');	
	
	$options[] = array(
		'desc' => __('Description or Tagline', 'cleanpress'),
		'id' => 'slidedesc2',
		'std' => '',
		'type' => 'textarea');		
		
	$options[] = array(
		'desc' => __('Url', 'cleanpress'),
		'id' => 'slideurl2',
		'std' => '',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Slider Image 3', 'cleanpress'),
		'desc' => __('Third Slide', 'cleanpress'),
		'id' => 'slide3',
		'class' => '',
		'type' => 'upload');	
	
	$options[] = array(
		'desc' => __('Title', 'cleanpress'),
		'id' => 'slidetitle3',
		'std' => '',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('Description or Tagline', 'cleanpress'),
		'id' => 'slidedesc3',
		'std' => '',
		'type' => 'textarea');	
			
	$options[] = array(
		'desc' => __('Url', 'cleanpress'),
		'id' => 'slideurl3',
		'std' => '',
		'type' => 'text');			
			
	//Social Settings
	
	$options[] = array(
	'name' => __('Social Settings', 'cleanpress'),
	'type' => 'heading');

	$options[] = array(
		'name' => __('Facebook', 'cleanpress'),
		'desc' => __('Facebook Profile or Page URL i.e. http://facebook.com/username/ ', 'cleanpress'),
		'id' => 'facebook',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Twitter', 'cleanpress'),
		'desc' => __('Twitter Username', 'cleanpress'),
		'id' => 'twitter',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Google Plus', 'cleanpress'),
		'desc' => __('Google Plus profile url, including "http://"', 'cleanpress'),
		'id' => 'google',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Feeburner', 'cleanpress'),
		'desc' => __('URL for your RSS Feeds', 'cleanpress'),
		'id' => 'feedburner',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Pinterest', 'cleanpress'),
		'desc' => __('Your Pinterest Profile URL', 'cleanpress'),
		'id' => 'pinterest',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Instagram', 'cleanpress'),
		'desc' => __('Your Instagram Profile URL', 'cleanpress'),
		'id' => 'instagram',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Linked In', 'cleanpress'),
		'desc' => __('Your Linked In Profile URL', 'cleanpress'),
		'id' => 'linkedin',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'name' => __('Youtube', 'cleanpress'),
		'desc' => __('Your Youtube Channel URL', 'cleanpress'),
		'id' => 'youtube',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Flickr', 'cleanpress'),
		'desc' => __('Your Flickr Profile URL', 'cleanpress'),
		'id' => 'flickr',
		'std' => '',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'desc' => __('For More Social Icons. <a href="http://rohitink.com/product/cleanpress-pro/" target="_blank">Upgrade to Pro</a> at Just $19.45. Pro Version Allows you to add Icons of your own choice. ', 'cleanpress'),
		'std' => '',
		'type' => 'info');							
		
	$options[] = array(
	'name' => __('Support', 'cleanpress'),
	'type' => 'heading');
	
	$options[] = array(
		'desc' => __('CleanPress WordPress theme has been Designed and Created by <a href="http://rohitink.com" target="_blank">Rohit Tripathi</a>. For any Queries or help regarding this theme, <a href="http://wordpress.org/support/theme/cleanpress/" target="_blank">use the free version support forums</a>. Dedicated & Faster Support is available for <a href="http://rohitink.com/product/cleanpress-pro/" target="_blank">Pro Version</a> only. Check out the Pro Version for Other Tons of Features.', 'cleanpress'),
		'type' => 'info');		
		
	$options[] = array(
		'name' => __('Live Demo Blog', 'cleanpress'),
		'desc' => __('For your convenience, we have created a <a href="http://demo.inkhive.com/cleanpress/" target="_blank">Live Demo Blog of CleanPress Plus</a>. You can take a look at and find out how your site would look once complete.', 'cleanpress'),
		'type' => 'info');	
		
	 $options[] = array(
	 	'name' => __('Follow Me','cleanpress'),
		'desc' => __('<a href="http://twitter.com/rohitinked" target="_blank">Follow Me on Twitter</a> or <a href="http://plus.google.com/+RohitTripathi/ target="_blank">Add Me to your Circles on Google Plus</a> to know about my upcoming themes.', 'cleanpress'),
		'type' => 'info');	
		
	return $options;
}