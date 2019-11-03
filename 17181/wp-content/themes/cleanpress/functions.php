<?php
/**
 * CleanPress functions and definitions
 *
 * @package CleanPress
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

/**
 * Initialize Options Panel
 */
if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once get_template_directory() . '/inc/options-framework.php';
}

if ( ! function_exists( 'cleanpress_setup' ) ) :

function cleanpress_setup() {

	load_theme_textdomain( 'cleanpress', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size('homepage-banner',250,220,true);

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'cleanpress' ),
		'top' => __( 'Top Menu', 'cleanpress' ),
	) );

	add_theme_support( 'custom-background', apply_filters( 'cleanpress_custom_background_args', array(
		'default-color' => 'f7f7f7',
		'default-image' => '',
	) ) );
	add_theme_support( 'post-formats', array( 'video' ) );
	
	}
	
endif; // cleanpress_setup
add_action( 'after_setup_theme', 'cleanpress_setup' );

function cleanpress_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'cleanpress' ),
		'description'   => __( 'This is the Primary Sidebar. It will be displayed on Posts Pages.', 'cleanpress'),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Left', 'cleanpress' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Center', 'cleanpress' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Right', 'cleanpress' ),
		'id'            => 'sidebar-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'cleanpress_widgets_init' );

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('#example_showhidden').click(function() {
  		jQuery('#section-example_text_hidden').fadeToggle(400);
	});
	
	if (jQuery('#example_showhidden:checked').val() !== undefined) {
		jQuery('#section-example_text_hidden').show();
	}
	
});
</script>
<?php
}

function cleanpress_scripts() {
	wp_enqueue_style( 'cleanpress-fonts', '//fonts.googleapis.com/css?family=Open+Sans:300,400,700,600' );
	wp_enqueue_style( 'cleanpress-basic-style', get_stylesheet_uri() );
	if ( (function_exists( 'of_get_option' )) && (of_get_option('sidebar-layout', true) != 1) ) {
		if (of_get_option('sidebar-layout', true) ==  'right') {
			wp_enqueue_style( 'cleanpress-layout', get_template_directory_uri()."/css/layouts/content-sidebar.css" );
		}
		else {
			wp_enqueue_style( 'cleanpress-layout', get_template_directory_uri()."/css/layouts/sidebar-content.css" );
		}	
	}
	else {
		wp_enqueue_style( 'cleanpress-layout', get_template_directory_uri()."/css/layouts/content-sidebar.css" );
	}
				
	wp_enqueue_style( 'cleanpress-bootstrap-style', get_template_directory_uri()."/css/bootstrap/bootstrap.min.css", array('cleanpress-layout') );
		
	if ( (function_exists( 'of_get_option' )) && (of_get_option('theme-skin', true) != 1) ) {
		wp_enqueue_style( 'cleanpress-main-skin', get_template_directory_uri()."/css/skins/".of_get_option('theme-skin').".css", array('cleanpress-layout','cleanpress-bootstrap-style') );
	}
	else {
		wp_enqueue_style( 'cleanpress-main-skin', get_template_directory_uri()."/css/skins/default.css", array('cleanpress-layout','cleanpress-bootstrap-style') );
	}
	wp_enqueue_script( 'cleanpress-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'cleanpress-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
		wp_enqueue_style( 'cleanpress-nivo-slider-default-theme', get_template_directory_uri()."/css/nivo/slider/themes/default/default.css" );
	
		wp_enqueue_style( 'cleanpress-nivo-slider-style', get_template_directory_uri()."/css/nivo/slider/nivo.css" );
	}			
	
	if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
		wp_enqueue_script( 'cleanpress-nivo-slider', get_template_directory_uri() . '/js/nivo.slider.js', array('jquery') );
	}
		
	wp_enqueue_script( 'cleanpress-mm', get_template_directory_uri() . '/js/mm.js', array('jquery') );
	
	wp_enqueue_script( 'cleanpress-bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery') );
			
	wp_enqueue_script( 'cleanpress-custom-js', get_template_directory_uri() . '/js/custom.js', array('jquery') );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'cleanpress-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'cleanpress_scripts' );
function cleanpress_custom_head_codes() {
 if ( (function_exists( 'of_get_option' )) && (of_get_option('style2', true) != 1) ) {
	echo "<style>".of_get_option('style2', true)."</style>";
 }
 if ( (function_exists( 'of_get_option' )) && (of_get_option('slider_enabled') != 0) ) {
	echo "<script>jQuery(window).load(function() { jQuery('#slider').nivoSlider({effect:'boxRandom', pauseTime: 5000 }); });</script>";
 }
	 echo "<script>jQuery(document).ready( function() { jQuery('.main-navigation ul.menu').mobileMenu({switchWidth: 768}); });</script>";
	 echo "<style>h1.menu-toggle {display: none !important;}.td_mobile_menu_wrap select{margin-left:-20px;margin-top: 20px;}</style>";
}	
add_action('wp_head', 'cleanpress_custom_head_codes');

function cleanpress_nav_menu_args( $args = '' )
{
    $args['container'] = false;
    return $args;
} // function
add_filter( 'wp_page_menu_args', 'cleanpress_nav_menu_args' );

function cleanpress_pagination() {
	global $wp_query;
	$big = 12345678;
	$page_format = paginate_links( array(
	    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	    'format' => '?paged=%#%',
	    'current' => max( 1, get_query_var('paged') ),
	    'total' => $wp_query->max_num_pages,
	    'type'  => 'array'
	) );
	if( is_array($page_format) ) {
	            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
	            echo '<div class="pagination"><div><ul>';
	            echo '<li><span>'. $paged . ' of ' . $wp_query->max_num_pages .'</span></li>';
	            foreach ( $page_format as $page ) {
	                    echo "<li>$page</li>";
	            }
	           echo '</ul></div></div>';
	 }
}
/**
 * Custom Functions for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/jetpack.php';
