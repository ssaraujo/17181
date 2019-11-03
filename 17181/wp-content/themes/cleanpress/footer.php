<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package CleanPress
 */
?>
	</div>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer row" role="contentinfo">
	<div class="container">
		<div class="site-info col-md-4">
			<?php do_action( 'cleanpress_credits' ); ?>
			<?php printf( __( 'CleanPress Theme by %1$s.', 'cleanpress' ), '<a href="http://inkhive.com/product/cleanpress" rel="designer">InkHive</a>' ); ?>
		</div><!-- .site-info -->
		<div id="footertext" class="col-md-7">
        	<?php
			if ( (function_exists( 'of_get_option' ) && (of_get_option('footertext2', true) != 1) ) ) {
			 	echo of_get_option('footertext2', true); } ?>
        </div>
	</div>   
	</footer><!-- #colophon -->
	
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>