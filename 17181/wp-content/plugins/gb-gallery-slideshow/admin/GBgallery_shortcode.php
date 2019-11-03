<?php
wp_enqueue_script('jquery-ui-core',array( 'jquery' ));
wp_enqueue_script('gbgallery_shortcode',array( 'jquery' ));

wp_localize_script('gbgallery_effect_ajax_core', 'gb_vars', array(
    'gb_nonce' => wp_create_nonce('gb_nonce')
));
wp_enqueue_style( 'gbgallery-style' );



global $gb_group_table,$wpdb,$group_name,$gb_gallery_donate_option;
if(! isset($gb_gallery_donate_option))
    gb_global_set();
$gb_premium_size = array(
    '1000X556',
    '1000X360',//
    '800X444',
    '800X288',//
    '600X334',
    '600X216',//
    '500X278',
    '500X180',//
    '400X222',
    '400X144',//
    '300X168',
    '300X108',//
    '200X112',
    '200X72',//
    '100X56',
    '100X36'//
);
$gb_premium_effects = array(
    "Bounce",
    "Drop",
    "Explode",
    "Puff",
    "Shake",
    "Slide"
);
$gb_image_size = array(
    '1000X556',
    '1000X360',//
    '900X500',
    '900X324',//
    '800X444',
    '800X288',//
    '700X390',//for Tablet
    '700X252',//
    '600X334',
    '600X216',//
    '500X278',
    '500X180',//
    '400X222',
    '400X144',//
    '300X168',
    '300X108',//
    '280X154',//for Mobiles
    '280X100',//
    '200X112',
    '200X72',//
    '100X56',
    '100X36'//
);
//for Tablet
$gb_image_size_tablet = array(
    '700X390',//for Tablet
    '700X230',//
    '600X334',
    '600X196',//
    '500X278',
    '500X160',//
    '400X222',
    '400X128',//
    '300X168',
    '300X96',//
    '280X154',//for Mobiles
    '250X80',//
    '200X112',
    '200X64',//
    '100X56',
    '100X32'//
);
//for Mobiles
$gb_image_size_mobile = array(
    '280X154',
    '250X80',//
    '200X112',
    '200X64',//
    '100X56',
    '100X32'//
);

$gb_general_effects = gb_get_general_effects();
$groups = "";
$the_group = "General";
$the_size = "900X500";
$the_duration = "4000";
$the_effect = "";
$the_class = "";
$sql = "SELECT * from $gb_group_table";
$groups = $wpdb->get_results($sql);
$gb_short_form = "";
$gb_short_form .= "<div id='gb_short_code_con' class='postbox gb_short_code_con'>";
$gb_short_form .= "<h3 class='hndle'>GB Gallery Slideshow Shortcode</h3>";
$gb_short_form .= "<div class='gb_short_code_form_con'>";
$gb_short_form .= "<label for='gb_short_group'>Select a group</label><br />";
$gb_short_form .= "<select name='gb_short_group' id='gb_short_group'>";
foreach($groups as $group){
    $gb_short_form .= "<option value='". $group->id ."'>". $group->groups ."</option>";
}
$gb_short_form .= "</select>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<div class='GB_size_con'>";
$gb_short_form .= "<label for='gb_short_size'>Select the images size</label>";
$gb_short_form .= "<br>";
$gb_short_form .= "<label class='gb_small_desc in_desc'><b class='gb_premium_asterisk'>*</b> Premium version only</label>";
$gb_short_form .= "<br>";
$gb_short_form .= "<select name='gb_short_size' id='gb_short_size'>";
foreach($gb_image_size as $size){
    if(in_array($size,$gb_premium_size))
        $gb_short_form .= "<option disabled >".$size." *</option>";
    else
        $gb_short_form .= "<option value='". $size ."'>". $size ."</option>";
}
$gb_short_form .= "</select>";
$gb_short_form .= "<div class='GB_sizes_con'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
$gb_short_form .= "</div>";
$gb_short_form .= "<br><br>";

$gb_short_form .= "<label for='gb_short_auto_size'>Auto resize for media devices</label><br />";
$gb_short_form .= "<input disabled checked type='checkbox' name='gb_short_auto_size' id='gb_short_auto_size' class='gb_gallery_auto_resize' checked><label class='gb_small_desc in_desc'> Checked = resize to fit the media device</label>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<div class='gb_gallery_media_size gb_premium_container'>";
$gb_short_form .= "<div class='GB_size_con'>";
$gb_short_form .= "<label for='gb_short_mobile_size'>Mobile display size</label>";
$gb_short_form .= "<br>";
$gb_short_form .= "<select name='gb_short_mobile_size' id='gb_short_mobile_size'>";
foreach($gb_image_size_mobile as $size){
    $gb_short_form .= "<option value='". $size ."'>". $size ."</option>";
}
$gb_short_form .= "</select>";
$gb_short_form .= "<div class='GB_sizes_con'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
$gb_short_form .= "</div>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<div class='GB_size_con' style='height: 60px'>";
$gb_short_form .= "<label for='gb_short_tablet_size'>Tablet display size</label>";
$gb_short_form .= "<br>";
$gb_short_form .= "<select name='gb_short_tablet_size' id='gb_short_tablet_size'>";
foreach($gb_image_size_tablet as $size){
    $gb_short_form .= "<option value='". $size ."'>". $size ."</option>";
}
$gb_short_form .= "</select>";
$gb_short_form .= "<div class='GB_sizes_con'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
$gb_short_form .= "</div></div>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<label for='gb_short_duration'>Set the duration</label><br />";
$gb_short_form .= "<label class='gb_small_desc'>Duration between the images in mile seconds</label><br />";
$gb_short_form .= "<input type='text' name='gb_short_duration' id='gb_short_duration'>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<label for='gb_short_special_effect'>Special Effect</label><br />";
$gb_short_form .= "<input type='checkbox' name='gb_short_special_effect' id='gb_short_special_effect'><label class='gb_small_desc in_desc'> Checked = run each image is own effect</label>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<label for='gb_short_general_effect'>Select a General Effect</label><br />";
$gb_short_form .= "<label class='gb_small_desc'>Select the jquery ui general effect</label><br />";
$gb_short_form .= "<label class='gb_small_desc in_desc'><b class='gb_premium_asterisk'>*</b> Premium version only</label>";
$gb_short_form .= "<select name='gb_short_general_effect' id='gb_short_general_effect'>";
foreach($gb_general_effects as $gb_general_effect){
    if(in_array($gb_general_effect,$gb_premium_effects)){
        $gb_short_form .= "<option disabled >". $gb_general_effect ." *</option>";
    }else{
        $gb_short_form .= "<option value='".strtolower($gb_general_effect)."'>". $gb_general_effect ."</option>";
    }
}
$gb_short_form .= "</select>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<label for='gb_short_master_class'>Add a master class</label><br />";
$gb_short_form .= "<input type='text' name='gb_short_master_class' id='gb_short_master_class'>";
$gb_short_form .= "<br><br>";
$gb_short_form .= "<input type='button' id='gb_short_code_btn' class='button-primary gb_button-primary' value='". __('Generate ShortCode')."'>";
$gb_short_form .= "</div></div>";

$donate_option = get_option( $gb_gallery_donate_option );
?>
<div class="wrap gb_gallery_admin_shortcode">
    <header>
        <h2>GB Gallery Slideshow Shortcode - Generator</h2>
    </header>
    <section>
        <section class="gb_short_code_left_con widget-liquid-right">
            <?php echo $gb_short_form ?>
        </section>
        <section class="gb_short_code_right_con">
            <div class="gb_error_log"></div>
            <div class="gb_short_code_display">
                <h2>GB Gallery Slideshow Shortcode instruction</h2>
                <ol class="gb_short_to_do" type="1">
                    <li>
                        Copy the following text:<br>
                        <label></label>
                    </li>
                    <li>
                        Navigate to a post or page.
                    </li>
                    <li>
                        Paste the code in the content editor and Update or Publish.
                    </li>
                </ol>

            </div>
        </section>
        <?php if(!$donate_option): ?>
            <section class="gb_donate_section gb_box_shadow">
                <div class="gb_donate_con">
                    <div class="gb_donate">
                        <p>If you use and like this plugin, please help us to improve, upgrade, and create new free plugins by donating:</p>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick" />
                            <input type="hidden" name="hosted_button_id" value="2AJ2QDUWU39P2" />
                            <input type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/IL/i/btn/btn_donateCC_LG.gif" />
                            <img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" border="0" /></form>
                        <p>Or you can upgrade to our premium version of the GB Gallery Slideshow: <a href="http://gb-plugins.com/buy-now/" target="_blank" class="button-primary">Upgrade</a></p>
                    </div>
                </div>
            </section>
        <?php endif ?>
        <div class="CLB"></div>
    </section>
    <footer>
        <div class="gb_helper">
            <div class="gb_note"><ul><li></li></ul></div>
        </div>
    </footer>
</div>
