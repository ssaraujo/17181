<?php
global $wpdb,$gb_gallery_option,$gb_group_post_table,$gb_group_table,$gb_effect_table,$gb_gallery_help_option,$gb_gallery_donate_option;
if(! isset($gb_gallery_option)){
    include_once( 'GBgallery.php' );
    gb_global_set();
}
$option = get_option( $gb_gallery_option );
if ( $option != '1' ) {
    $gb_meta_keys = array(
        'gb_img',
        'gb_link',
        'gb_group',
        'gb_index',
        'gb_effect',
        'gb_active'
    );
    $my_group = $wpdb->get_results( "SELECT postid FROM $gb_group_post_table" );
    foreach($my_group as $group){
        wp_delete_post($group->postid, true);
    }
    foreach($gb_meta_keys as $gb_key){
        $wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key = '".$gb_key."'");
    }
    $wpdb->query("DROP TABLE IF EXISTS $gb_group_table");
    $wpdb->query("DROP TABLE IF EXISTS $gb_group_post_table");
    $wpdb->query("DROP TABLE IF EXISTS $gb_effect_table");
    delete_option( $gb_gallery_option );
    delete_option( $gb_gallery_help_option );
    delete_option( $gb_gallery_donate_option );
}