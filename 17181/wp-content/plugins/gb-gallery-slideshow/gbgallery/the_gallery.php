<?php
global $gb_post_type, $more, $properties, $gb_gallery_help_option;
if(! isset($gb_post_type)){
    include_once( plugins_url('gb-gallery-slideshow/GBgallery.php') );
    gb_global_set();
}
$gb_args = array(
    'post_type' => $gb_post_type,
    'meta_key' => 'gb_index',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'gb_group',
            'compare' => '=',
            'value' => $instance["group"]
        )
    ),
    'posts_per_page' => -1,
    'post_status' => 'publish'
);
$gallery_query = null;
$gallery_query = new WP_Query($gb_args);
if( $gallery_query->have_posts() ) {
if(isset($instance["master_class"])&& $instance["master_class"]!="@"){
    $master_class = $instance["master_class"];
}else{
    $master_class = "";
}
$random_name = uniqid();
$thumbnail_arr = array();
$attachment_arr = array();
$attachment_unique = array();
$index = 0;
$size = "full"; // (thumbnail, medium, large, full or custom size)
$thumbnail = 'thumbnail';


$gb_load_script = "";
$properties = "";
if(isset($instance["size"])){
    $gb_size = $instance["size"];
    $gb_image_width = substr($gb_size, 0, strrpos($gb_size, "X"));
    $gb_image_height = substr($gb_size, strrpos($gb_size, "X")+1);
    if($properties != "")
        $properties .= ', ';
    $properties .= 'width: "'.$gb_image_width.'"';
    $properties .= ', height: "'.$gb_image_height.'"';

}
if(isset($instance["duration"]) && $instance["duration"] != "" && $instance["duration"]>0){
    if($properties != "")
        $properties .= ', ';
    $properties .= 'ImgTime: '.$instance["duration"];
    $properties .= ', EffectTime: '.round($instance["duration"]/1.5);
}
if(isset($instance["special_effect"])){
    if($properties != "")
        $properties .= ', ';
    $properties .= 'SpecialEffect: true';
}
if(isset($instance["auto_resize"]) && $instance["auto_resize"] == "on"){
    if($properties != "")
        $properties .= ', ';
    $properties .= 'AutoResize: true';
}else{
    if(isset($instance["mobile_size"])){
        if($properties != "")
            $properties .= ', ';
        $properties .= 'MobileSize: "'.$instance["mobile_size"].'"';
    }
    if(isset($instance["tablet_size"])){
        if($properties != "")
            $properties .= ', ';
        $properties .= 'TabletSize: "'.$instance["tablet_size"].'"';
    }
    if($properties != "")
        $properties .= ', ';
    $properties .= 'AutoResize: false';
}
if(isset($instance["general_effect"])){
    if($properties != "")
        $properties .= ', ';
    wp_enqueue_script( 'jquery-effects-'.$instance["general_effect"] );
    $properties .= 'Effect: "'.$instance["general_effect"].'"';
}
add_action( 'wp_footer', 'gb_add_script');
if(!function_exists('gb_add_script')){
    function gb_add_script(){
        global $properties;
        $gb_load_script ="<script type='text/javascript'>";
        $gb_load_script .="jQuery(document).ready(function() {";
        $gb_load_script .="if(jQuery('.GB_gallery').length > 1){";
        $gb_load_script .="jQuery('.GB_gallery').each(function(){";
        $gb_load_script .="jQuery(this).GBgallery(jQuery(this).find('.GB_helper').find('.GB_helper_prop').html());";
        $gb_load_script .="});";
        $gb_load_script .="}else{";
        $gb_load_script .="jQuery('.GB_gallery').GBgallery({".$properties."});";
        $gb_load_script .="}";
        $gb_load_script .="});";
        $gb_load_script .="</script>";
        echo $gb_load_script;
    }
}



$gb_to_echo = ""; "";
    $gb_to_echo .= "<div id='gb_gallery-".$random_name."' class='GB_gallery GB_widget_con ".$master_class."'>";
    $gb_to_echo .= "<div class='GB_gallery_slider'>";
    $gb_to_echo .= "<div class='GB_gallery_PP'></div>";
    $gb_to_echo .= "<div class='GB_gallery_desc_con'></div>";
    $gb_to_echo .= "<div class='GB_gallery_loader'>Loading images please wait <b></b>%</div>";
    $gb_to_echo .= "</div>";
    $gb_to_echo .= "<div class='GB_helper'>";
    $gb_to_echo .= "<div class='gb_gallery_device'></div>";
    $gb_to_echo .= "<div class='GB_helper_prop'>{".$properties."}</div>";
    $gb_to_echo .= "<div class='GB_divs_con'>";
     for($i=0;$i<100;$i++){
         $gb_to_echo .= "<div class='GB_gallery_slider-".$i."'><div></div></div>";
     }
    $gb_to_echo .= "</div>";
    $gb_to_echo .= "<div class='GB_img_con'>";
     while ($gallery_query->have_posts()) {
         $gallery_query->the_post();
         $more = 0;
         $gb_mata = "";
         $gb_mata = get_post_meta( get_the_ID() );
         if($gb_mata["gb_active"][0]=='on'){
             $attachment_unique[$index] = get_the_ID();
             $attachment_arr[$index] = $attachment_id = $gb_mata["gb_index"][0];
             $thumbnail_arr[$index] = $image = $gb_mata["gb_img"][0];
             if(isset($gb_mata["gb_link"][0])){
                $image_link_arr = $gb_mata["gb_link"][0];
             }else{
                 $image_link_arr = "";
             }
             if(isset($instance["special_effect"]) && isset($gb_mata['gb_effect'])){
                 $gb_effect = $gb_mata['gb_effect'];
             }else{
                 $gb_effect = "";
             }
             $gb_to_echo .= "<div id='GB_img_div_".$random_name."-".$attachment_unique[$index].$attachment_id."'>";
             $gb_to_echo .= "<img id='GB_img_".$random_name."-".$attachment_unique[$index].$attachment_id."' class='GB_img' src='".$image."' ".($gb_effect!="" ? "effect='".implode (', ', $gb_effect)."'" : "")." alt='".get_the_title()."' title='".get_the_title()."'>";
             $gb_to_echo .= "<div class='GB_gallery_desc' for='GB_img_div_".$random_name."-".$attachment_unique[$index].$attachment_id."'>";
             $gb_to_echo .= "<h1>".get_the_title()."</h1>";
             $gb_to_echo .= "<p>".get_the_content(false)." ".($image_link_arr != "" ? '<a class="read-more" href="'.$image_link_arr.'" target="_blank">Read more...</a>':'')."</p>";
             $gb_to_echo .= "</div></div>";

             $index++;
         }
     }
    $gb_to_echo .= "</div>";
    if(! isset($gb_gallery_help_option))
        gb_global_set();
    $help_option = get_option( $gb_gallery_help_option );
    if($help_option == '1')
        $gb_to_echo .= "<div clase='helpslideshow'><a href='http://gb-plugins.com/'>Gallery Slideshow</a></div>";
    $gb_to_echo .= "</div>";

    $gb_to_echo .= "<div id='GB_preview-".$random_name."'>";
    $gb_to_echo .= "<ul class='GB_preview_items'>";
    $li_row = 0;
    if(sizeof($thumbnail_arr)<=10){
        foreach($thumbnail_arr as $i => $the_image){
            $gb_to_echo .= "<li id='GB_gallery_small_".$random_name."-".$attachment_unique[$i].$attachment_arr[$i]."'  row='".$li_row."' class='GB_preview_item'><img class='gb_small_image' src='".$thumbnail_arr[$i]."'></li>";
        }
        $gb_to_echo .= "</ul></div></div>";
    }else{
        foreach($thumbnail_arr as $i => $the_image){
            if($i < 10)
            $gb_to_echo .= "<li id='GB_gallery_small_".$random_name."-".$attachment_unique[$i]."-".$attachment_arr[$i]."' row='".$li_row."' class='GB_preview_item'><img class='gb_small_image' src='".$thumbnail_arr[$i]."'></li>";
        }
        $gb_to_echo .= "</ul>";
        $gb_to_echo .= "<div class='GB_gallery_preview_more_con'>";
        $gb_to_echo .= "<div class='GB_gallery_preview_more_btn'><p class='d'>More</p>";
        $gb_to_echo .= "<div class='GB_gallery_preview_more'>";
        $gb_to_echo .= "<ul class='GB_preview_more_items'>";
        foreach($thumbnail_arr as $i => $the_image){
            if($i >= 10){
                if($i % 10 == 0)
                    $li_row++;
                $gb_to_echo .= "<li id='GB_gallery_small_".$random_name."-".$attachment_unique[$i]."-".$attachment_arr[$i]."' row='".$li_row."' class='GB_preview_item'><img class='gb_small_image' src='".$thumbnail_arr[$i]."'></li>";
            }
        }
        $gb_to_echo .= "</ul><div class='CLB GB_total' row='".$li_row."'></div></div>";
        $gb_to_echo .= "</div></div></div></div>";
    }
     return $gb_to_echo;
}else{
    global $gb_group_table, $wpdb;
    $result = $wpdb->get_var( "SELECT groups FROM $gb_group_table WHERE id = ".$instance["group"] );
     return __('No images was loaded to group: "').$result.'"';
 }