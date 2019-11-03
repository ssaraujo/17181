<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package CleanPress
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function cleanpress_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'cleanpress_jetpack_setup' );
