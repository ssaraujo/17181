<?php
/*
Plugin Name: GB Gallery Slideshow
Plugin URI: http://gb-plugins.com/
Description: GB gallery is an awesome new type of gallery slideshow. It is an easy to use, innovative plugin that allows you to create your own effects.
Version: 1.2
Author: GB-plugins
Author URI: http://gb-plugins.com/
*/

register_activation_hook( __FILE__, 'gb_gallery_install' );

add_action( 'admin_enqueue_scripts', 'gb_gallery_load_scripts' );
add_action( 'wp_enqueue_scripts', 'gb_gallery_load_scripts_frontend' );
add_action('admin_menu', 'gb_gallery_load_admin');

// add the custom post type to the admin dashboard
add_action('init','gb_gallery_cpt');

add_action( 'admin_init', 'gb_gallery_post_meta' );
add_action( 'save_post', 'add_gb_gallery_fields', 10, 2 );
add_action('widgets_init','start_widget');

// before publishing the custom post type
global $gb_post_type;
if(! isset($gb_post_type)){
    gb_global_set();
}
add_action('publish_'.$gb_post_type, 'gb_save_post',10,2);
add_action('new_to_publish_'.$gb_post_type, 'gb_save_post',10,2);
add_action('draft_to_publish_'.$gb_post_type, 'gb_save_post',10,2);
add_action('pending_to_publish_'.$gb_post_type, 'gb_save_post',10,2);

// ajax to change options
add_action('wp_ajax_gb_ajax_delete_option','gb_change_delete_option');
add_action('wp_ajax_gb_ajax_help_option','gb_change_help_option');
add_action('wp_ajax_gb_ajax_donate_option','gb_change_donate_option');

// ajax to custom post type meta box
add_action('wp_ajax_gb_ajax_get_index','get_group_index');

// ajax to groups admin
add_action('wp_ajax_gb_ajax_get_group','get_group_posts');

//Start Free v
add_action('wp_ajax_gb_ajax_save_group','gb_save_group');
//End Free v

add_action('wp_ajax_gb_ajax_add_group','gb_add_group');
add_action('wp_ajax_gb_ajax_delete_group','gb_delete_group');
add_action('wp_ajax_gb_ajax_show_combo_groups','get_groups');

// ajax to effect admin
add_action('wp_ajax_gb_ajax_show_effect','gb_show_effect');
add_action('wp_ajax_gb_ajax_save_effect','gb_save_effect');
add_action('wp_ajax_gb_ajax_copy_effect','gb_copy_effect');


// ajax to short code
add_action('wp_ajax_gb_gallery_check_short_group','gb_check_group');

// add custom column to posts list screen
add_filter('manage_edit-'.$gb_post_type.'_columns', 'gb_add_column');
add_action('manage_'.$gb_post_type.'_posts_custom_column', 'gb_column_content', 10, 2);
add_filter('manage_edit-'.$gb_post_type.'_sortable_columns', 'concerts_sort');
add_filter('request', 'gb_column_order_by');

add_shortcode('gb_gallery','get_short_code');

if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 900, 500 );
    set_post_thumbnail_size( 900, 324 );
}
/* Set the widget in the Dashboard */
class gb_gallery_widget extends WP_Widget{
    function __construct(){
        parent::__construct('gb_gallery_widget', $name = __('GB Gallery Slideshow'), array( 'description' => __( 'Slideshow that reveals images with your own special effects' ) ));
    }
    public function widget($args, $instance){
        echo $args['before_widget'];

        echo include dirname( __FILE__ ) .'/gbgallery/the_gallery.php';

        echo $args['after_widget'];
    }
    public function form($instance){
        global $gb_group_table,$wpdb;
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
        $the_size_mobile = "280X154";
        $the_size_tablet = "400X222";
        if(isset($instance["mobile_size"])){
            $the_size_mobile = $instance["mobile_size"];
        }
        if(isset($instance["tablet_size"])){
            $the_size_tablet = $instance["tablet_size"];
        }


        $gb_general_effects = gb_get_general_effects();
        $gb_premium_effects = array(
            "Bounce",
            "Drop",
            "Explode",
            "Puff",
            "Shake",
            "Slide"
        );
        $groups = "";
        $the_group = "General";
        $the_size = "900X500";


        $the_duration = "4000";
        $the_effect = "";
        $the_class = "";
        $master_class_error = "none";
        if(isset($instance["group"])){
            $the_group = $instance["group"];
        }
        if(isset($instance["size"])){
            $the_size = $instance["size"];
        }

        if(isset($instance["auto_resize"]) && $instance["auto_resize"] == "on"){
            $auto_resize = "checked";
        }else{
            if(!isset($instance["auto_resize"]))
                $auto_resize = "checked";
            else
                $auto_resize = "";
        }
        if(isset($instance["duration"])){
            $the_duration = $instance["duration"];
        }
        if(isset($instance["special_effect"])){
            $the_effect = "checked";
        }
        if(isset($instance["general_effect"])){
            $the_general_effect = $instance["general_effect"];
        }else{
            $the_general_effect = '';
        }
        if(isset($instance["master_class"])){
            if($instance["master_class"]!="@"){
                $the_class = $instance["master_class"];
                $master_class_error = "none";
            }else{
                $the_class = $instance["master_class"] = "";
                $master_class_error = "block";
            }
        }
        $sql = "SELECT * from $gb_group_table";
        $groups = $wpdb->get_results($sql);
        echo "<div class='gb_widget_con'>";
        echo "<label for='". $this->get_field_id('group') ."'>Select a group</label>";
        echo "<select name='". $this->get_field_name('group') ."' id='". $this->get_field_id('group') ."'>";
        foreach($groups as $group){
            echo "<option ".($the_group == $group->id ? 'selected' : '')." value='". $group->id ."'>". $group->groups ."</option>";
        }
        echo "</select>";
        echo "<br><br>";
        echo "<div class='GB_size_con'>";
        echo "<label for='". $this->get_field_id('size') ."'>Select the images size</label>";
        echo "<label class='gb_small_desc in_desc'><b class='gb_premium_asterisk'>*</b> Only in premium version</label>";
        echo "<select name='". $this->get_field_name('size') ."' id='". $this->get_field_id('size') ."'>";
        foreach($gb_image_size as $size){
            if(in_array($size,$gb_premium_size))
                echo "<option disabled >".$size." *</option>";
            else
                echo "<option value='". $size ."' ".($size == $the_size ? 'selected':'')." >".$size ."</option>";
        }
        echo "</select>";
        $size_for_load = "&#39;".$this->get_field_id('size')."&#39;";
        echo "<div class='GB_sizes_con' onload='javascript:set_screen_size(". $size_for_load .");'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
        echo "</div>";
        echo "<br><br>";
        echo "<label for='". $this->get_field_id('auto_resize') ."'>Auto resize for media devices</label>";
        echo "<input disabled checked class='gb_gallery_auto_resize' type='checkbox' name='". $this->get_field_name('auto_resize') ."' id='". $this->get_field_id('auto_resize') ."' ". $auto_resize .">";
        echo "<label class='gb_small_desc in_desc'> Checked = resize to fit the media device</label>";
        echo "<div class='gb_gallery_media_size gb_premium_container'>";
        echo "<div class='GB_size_con'>";
        echo "<label for='". $this->get_field_id('mobile_size') ."'>Mobile display size</label>";
        echo "<select name='". $this->get_field_name('mobile_size') ."' id='". $this->get_field_id('mobile_size') ."'>";
        foreach($gb_image_size_mobile as $size){
            echo "<option value='". $size ."' ".($size == $the_size_mobile ? 'selected':'').">". $size ."</option>";
        }
        echo "</select>";
        $size_for_mobile_load = "&#39;".$this->get_field_id('mobile_size')."&#39;";
        echo "<div class='GB_sizes_con' onload='javascript:set_screen_size_mobile(". $size_for_mobile_load .");'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
        echo "</div>";
        echo "<br><br>";
        echo "<div class='GB_size_con'>";
        echo "<label for='". $this->get_field_id('tablet_size') ."'>Tablet display size</label>";
        echo "<select name='". $this->get_field_name('tablet_size') ."' id='". $this->get_field_id('tablet_size') ."'>";
        foreach($gb_image_size_tablet as $size){
            echo "<option value='". $size ."' ".($size == $the_size_tablet ? 'selected':'').">". $size ."</option>";
        }
        echo "</select>";
        $size_for_tablet_load = "&#39;".$this->get_field_id('tablet_size')."&#39;";
        echo "<div class='GB_sizes_con' onload='javascript:set_screen_size_tablet(". $size_for_tablet_load .");'><div class='GB_size_screen'><div class='GB_size_widget'></div></div></div>";
        echo "</div></div>";


        echo "<br>";
        echo "<label for='". $this->get_field_id('duration') ."'>Set the duration</label>";
        echo "<label class='gb_small_desc'>Duration between the images in mile seconds</label>";
        echo "<input type='text' name='". $this->get_field_name('duration') ."' id='". $this->get_field_id('duration') ."' value='". $the_duration ."'>";
        echo "<br><br>";
        echo "<label for='". $this->get_field_id('special_effect') ."'>Special Effect</label>";
        echo "<input type='checkbox' name='". $this->get_field_name('special_effect') ."' id='". $this->get_field_id('special_effect') ."' ". $the_effect ."><label class='gb_small_desc in_desc'> Checked = run each image is own effect</label>";
        echo "<br><br>";
        echo "<label for='". $this->get_field_id('general_effect') ."'>Select a General Effect</label>";
        echo "<label class='gb_small_desc'>Select the jquery ui general effect</label>";
        echo "<label class='gb_small_desc in_desc'><b class='gb_premium_asterisk'>*</b> Only in premium version</label>";
        echo "<select name='". $this->get_field_name('general_effect') ."' id='". $this->get_field_id('general_effect') ."'>";
        foreach($gb_general_effects as $gb_general_effect){
            if(in_array($gb_general_effect,$gb_premium_effects)){
                echo "<option disabled >". $gb_general_effect ." *</option>";
            }else{
                echo "<option ".($the_general_effect == strtolower($gb_general_effect) ? 'selected' : '')." value='".strtolower($gb_general_effect)."'>". $gb_general_effect ."</option>";
            }
        }
        echo "</select>";
        echo "<br><br>";
        echo "<label for='". $this->get_field_id('master_class') ."'>Add a master class</label>";
        echo "<label class='gb_text_error gb_small_desc' style='display: ". $master_class_error ."'>No special characters allowed</label>";
        echo "<input type='text' name='". $this->get_field_name('master_class') ."' id='". $this->get_field_id('master_class') ."' value='". $the_class ."'>";
        echo "<br><br>";
        echo "</div>";
    }
    public function update($new_instance, $old_instance){
        if(isset($new_instance["group"]))
            $instance["group"] = $new_instance["group"];
        if(isset($new_instance["size"]))
            $instance["size"] = $new_instance["size"];
        if(isset($new_instance["mobile_size"]))
            $instance["mobile_size"] = $new_instance["mobile_size"];
        if(isset($new_instance["tablet_size"]))
            $instance["tablet_size"] = $new_instance["tablet_size"];
        $instance["auto_resize"] = "on";
        if(isset($new_instance["duration"]))
            $instance["duration"] = $new_instance["duration"];
        if(isset($new_instance["special_effect"]))
            $instance["special_effect"] = $new_instance["special_effect"];
        if(isset($new_instance["general_effect"]))
            $instance["general_effect"] = $new_instance["general_effect"];
        if(!preg_match("/^[-a-zA-Z0-9_ ]+$/", $new_instance["master_class"]) && $new_instance["master_class"] != ""){
            $instance["master_class"] = "@";
        }else{
            $instance["master_class"] = $new_instance["master_class"];
        }

        return $instance;
    }
}

function start_widget(){
    register_widget('gb_gallery_widget');
}

function get_short_code($instance){
    return include dirname( __FILE__ ) .'/gbgallery/the_gallery.php';
}

//Return a list of jQuery ui effects
function gb_get_general_effects(){
    $gb_effects_arr = array(
        "Blind",
        "Clip",
        "Fade",
        "Fold",
        "Highlight",
        "Pulsate",
        "Bounce",
        "Drop",
        "Explode",
        "Puff",
        "Shake",
        "Slide"
    );
    return $gb_effects_arr;
}

/* on activation */
function gb_gallery_install () {
    global $wpdb, $gb_group_table, $gb_group_post_table, $gb_gallery_help_option, $gb_effect_table;
    gb_global_set();
    $group_name = "General";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    if($wpdb->get_var("SHOW TABLES LIKE '$gb_group_table'") != $gb_group_table){
        $sql = "CREATE TABLE $gb_group_table (
            id INTEGER UNSIGNED AUTO_INCREMENT,
            groups text,
            PRIMARY KEY  (id)
            )";

        dbDelta( $sql );
        $rows_affected = $wpdb->insert( $gb_group_table, array( 'groups' => $group_name ) );
        if($rows_affected<=0){
            //Error: DB problem in creating the group table or insert the first row
        }
    }
    $sql = "";
    if($wpdb->get_var("SHOW TABLES LIKE '$gb_group_post_table'") != $gb_group_post_table){
        $sql = "CREATE TABLE $gb_group_post_table (
            id INTEGER UNSIGNED AUTO_INCREMENT,
            groupsid INTEGER,
            postid INTEGER,
            PRIMARY KEY  (id)
            )";
        dbDelta( $sql );
    }

    $sql = "";
    if($wpdb->get_var("SHOW TABLES LIKE '$gb_effect_table'") != $gb_effect_table){
        $sql = "CREATE TABLE $gb_group_post_table (
            id INTEGER UNSIGNED AUTO_INCREMENT,
            effects text,
            name text,
            PRIMARY KEY  (id)
            )";
        dbDelta( $sql );
    }
    update_option( $gb_gallery_help_option, '1' );
}

/* Set all global vars */
function gb_global_set(){
    global $wpdb, $gb_group_table, $gb_group_post_table, $gb_effect_table, $gb_gallery_option, $gb_post_type, $gb_gallery_help_option, $GB_this_name,$gb_gallery_donate_option;
    $GB_this_name = "gb-gallery-slideshow";
    $gb_group_table = $wpdb->prefix."gb_gallery_group";
    $gb_group_post_table = $wpdb->prefix."gb_gallery_group_post";
    $gb_effect_table = $wpdb->prefix."gb_gallery_effects";
    $gb_gallery_option = 'gb_gallery_option';
    $gb_post_type = 'gb_gallery_post';
    $gb_gallery_help_option = "gb_gallery_help";
    $gb_gallery_donate_option = "gb_gallery_donate";
}

/* Add to admin scripts and css */
function gb_gallery_load_scripts ($hook) {
    global $GBgallery_add_settings, $temps;
    // Respects SSL, Style.css is relative to the current file

    wp_register_style( 'gbgallery-style', plugins_url('style.css', __FILE__) );
    wp_register_script( 'gbgallery_admin_js_core', plugins_url( 'js/GBgallery_admin_js.js', __FILE__ ) );
    wp_register_script( 'gbgallery_groups_ajax_core', plugins_url( 'js/GBgallery_groups_ajax.js', __FILE__ ) );
    wp_register_script( 'gbgallery_ajax_core', plugins_url( 'js/GBgallery_groups_ajax.js', __FILE__ ) );
    wp_register_script( 'gbgallery_effect_ajax_core', plugins_url( 'js/GBgallery_effect_ajax.js', __FILE__ ) );
    wp_register_script( 'gbgallery_shortcode', plugins_url( 'js/GBgallery_shortcode.js', __FILE__ ) );


    wp_enqueue_style( 'gbgallery-style' );
    wp_localize_script('gbgallery_admin_js_core', 'gb_vars', array(
        'gb_nonce_admin' => wp_create_nonce('gb_nonce_admin')
    ));

    wp_enqueue_script('gbgallery_admin_js_core',array( 'jquery' ));
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui-core',array( 'jquery' ));
}

/* Add to frontend scripts and css */
function gb_gallery_load_scripts_frontend(){
    wp_register_style( 'gbgallery_frontend-style', plugins_url('css/GBgallery.css', __FILE__) );
    wp_register_script( 'gbgallery_frontend', plugins_url( 'js/GBgallery_js.js', __FILE__ ),array( 'jquery' ) );

    wp_enqueue_script("gbgallery_frontend",array( 'jquery' ));
    wp_enqueue_style("gbgallery_frontend-style");
}

/* Add to admin menu + submenus */
function gb_gallery_load_admin () {
    global $GBgallery_add_settings;
    add_menu_page(__("GB Gallery"),__("GBGallery admin"), 'manage_options', 'gb-gallery-slideshow/admin/GBgallery-admin.php', '', plugins_url( 'gb-gallery-slideshow/images/admin-icon.png' ), 100 );
    $GBgallery_add_settings = add_submenu_page( 'gb-gallery-slideshow/admin/GBgallery-admin.php', __('Group Admin'), __('Group Admin'), 'manage_options', "gb-gallery-slideshow/admin/GBgallery_group-admin.php");
    add_submenu_page( 'gb-gallery-slideshow/admin/GBgallery-admin.php', __('Special Effect Admin'), __('Special Effect Admin'), 'manage_options', "gb-gallery-slideshow/admin/GBgallery_effect-admin.php");
    add_submenu_page( 'gb-gallery-slideshow/admin/GBgallery-admin.php', __('ShortCode - Generator'), __('ShortCode Generator'), 'manage_options', "gb-gallery-slideshow/admin/GBgallery_shortcode.php");
}

/* Add GB Gallery custom post types */
function gb_gallery_cpt () {
    global $gb_post_type;
    register_post_type($gb_post_type, array(
        'label' => __('GB Gallery Post'),
        'description' => __('Images gallery'),
        'public' => true,
        'menu_position' => 5,
        'menu_icon' => plugins_url( 'gb-gallery-slideshow/images/GB_Gallery_icon.png' ),
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'gallery', 'with_front' => '1'),
        'query_var' => true,
        'exclude_from_search' => false,
        'supports' => array('title','editor'),
        'labels' => array (
            'name' => __('GB Gallery'),
            'singular_name' => __('GB Gallery'),
            'menu_name' => __('GB Gallery'),
            'add_new' => __('Add Image'),
            'add_new_item' => __('Add New Image'),
            'edit' => __('Edit'),
            'edit_item' => __('Edit Image'),
            'new_item' => __('New Image'),
            'view' => __('View Image'),
            'view_item' => __('View Image'),
            'search_items' => __('Search GB Gallery Images'),
            'not_found' => __('No GB Gallery Images Found'),
            'not_found_in_trash' => __('No GB Gallery Images Found in Trash'),
            'parent' => __('Parent Items')
        )
    ) );
}

/* Add GB Gallery custom post type meta box */
function gb_gallery_post_meta() {
    global $gb_post_type;
    add_meta_box( 'gb_gallery_meta', 'GB Gallery Post Details', 'gb_gallery_meta_init', $gb_post_type, 'side', 'high');
}

/* Form GB Gallery custom post type meta box */
function gb_gallery_meta_init( $gb_gallery_img ) {
    wp_enqueue_script( 'gbgallery_groups_ajax_core',array( 'jquery' ) );
    wp_localize_script('gbgallery_groups_ajax_core', 'gb_vars', array(
        'gb_nonce_home' => wp_create_nonce('gb_nonce_home')
    ));
    global $wpdb,$gb_group_table;
    $mygroups = $wpdb->get_results( "SELECT * FROM $gb_group_table" );
    wp_enqueue_style( 'gbgallery-style',array( 'jquery' ) );
    wp_enqueue_script('gbgallery_admin_js_core',array( 'jquery' ));
    $gb_the_img = get_post_meta( $gb_gallery_img->ID, 'gb_img', true );
    $gb_the_link = '';
    $gb_the_group = get_post_meta( $gb_gallery_img->ID, 'gb_group', true );
    $gb_the_index = get_post_meta( $gb_gallery_img->ID, 'gb_index', true );
    $gb_the_effect = get_post_meta( $gb_gallery_img->ID, 'gb_effect', true);
    $gb_the_active = get_post_meta( $gb_gallery_img->ID, 'gb_active', true );
    ?>
    <div class="gb_gallery_meta_con">
        <div class="gb_the_img_con">
            <label><span>*</span> Select Image:</label>
            <ul class="gb_required">
                <li>size option 1:<br>&nbsp;&nbsp;&nbsp;900X500, 800X444, 600X333, ...</li>
                <li>size option 2:<br>&nbsp;&nbsp;&nbsp;900X324, 800X288, 700X252, ...</li>
            </ul>
            <input type="text" name="gb_the_img" id="gb_the_img" value="<?php echo $gb_the_img ?>" title="<?php echo $gb_the_img ?>">
            <input type="button" value="Select" id="gb_the_img_btn">
            <div class="gb_the_img_img"><img src=""></div>
        </div>
        <div class="gb_the_link_con gb_premium_container">
            <label>Link To:</label>
            <br>
            <input type="text" disabled="disabled" name="gb_the_link" id="gb_the_link" value="<?php echo $gb_the_link ?>">
        </div>
        <div class="gb_the_group_con">
            <label><span>*</span> Group To:</label>
            <br>
            <select name="gb_the_group" id="gb_the_group">
                <?php
                if($gb_the_group == '')
                    $gb_the_group = 1;
                foreach($mygroups as $group){
                    echo "<option value='".$group->id."' ".($gb_the_group == $group->id? 'selected' : '').">".$group->groups."</option>";
                }
                ?>
            </select>
        </div>
        <div class="gb_the_index_con gb_premium_container">
            <label><span>*</span> Show Index:</label>
            <br>
            <label class="gb_required">&nbsp;&nbsp;&nbsp;0 = last</label>
            <br>
            <?php
                //locate in database the last index and to insert it to
            if($gb_the_index == ""){
                $gb_the_index = get_index($gb_the_group);
            }
            ?>
            <input type="number" disabled="disabled" name="gb_the_index" for="<?php echo $gb_the_group ?>" id="gb_the_index" value="<?php echo $gb_the_index; ?>">
        </div>
        <div class="gb_the_effect_con">
            <label>Special Effect:</label>
            <br>
            <input type="text" name="gb_the_effect" id="gb_the_effect" value="<?php echo $gb_the_effect ?>">
        </div>
        <div class="gb_the_active_con gb_premium_container">
            <label>Active:</label>
            &nbsp;<input type="checkbox" disabled="disabled" checked name="gb_the_active" id="gb_the_active">
        </div>
        <br>
        <label class="required"><span>*</span> Required field</label>
    </div>
<?php
}

/* get the highest index from a group in the meta table */
function get_index($group_id){
    global $wpdb;
    $sql = $wpdb->prepare("SELECT gb_meta2.meta_value
            FROM ".$wpdb->prefix ."postmeta gb_meta1, ".$wpdb->prefix ."postmeta gb_meta2
            WHERE gb_meta1.post_id = gb_meta2.post_id
            AND (gb_meta1.meta_key = 'gb_group' AND gb_meta1.meta_value = %s)
            AND gb_meta2.meta_key = 'gb_index'
            ORDER BY gb_meta2.meta_value * 1 DESC
            LIMIT 1",$group_id);
    return $wpdb->get_var($sql) + 1;
}

/* Ajax get the highest index from a group in the meta table */
function get_group_index(){
    if(isset($_POST['group_index']) && isset($_POST['group_id'])){
        echo 'gb='.get_index($_POST['group_id']).'=gb';
    }else{
        echo 'gb=-1=gb';
    }
    die();
}

/* Create/update GB Gallery custom post type meta box */
function add_gb_gallery_fields( $gb_gallery_id, $gb_gallery ) {
    global $gb_post_type;
    $old_image = "";
    if ( $gb_gallery->post_type == $gb_post_type ) {
        if ( isset( $_POST['gb_the_img'] ) && $_POST['gb_the_img'] != '' ) {
            update_post_meta( $gb_gallery_id, 'gb_img', $_POST['gb_the_img'] );
        }
        if ( isset( $_POST['gb_the_link'] ) && $_POST['gb_the_link'] != '' ) {
            update_post_meta( $gb_gallery_id, 'gb_link', $_POST['gb_the_link'] );
        }
        if ( isset( $_POST['gb_the_group'] ) && $_POST['gb_the_group'] != '' ) {
            update_post_meta( $gb_gallery_id, 'gb_group', $_POST['gb_the_group'] );
        }

        if ( isset( $_POST['gb_the_index'] ) && $_POST['gb_the_index'] != '' ) {
            update_post_meta( $gb_gallery_id, 'gb_index', $_POST['gb_the_index'] );
        }

        if ( isset( $_POST['gb_the_effect'] ) && $_POST['gb_the_effect'] != '' ) {
            update_post_meta( $gb_gallery_id, 'gb_effect', $_POST['gb_the_effect'] );
        }

        update_post_meta( $gb_gallery_id, 'gb_active', 'on' );

    }
}

/* add custom column to posts list screen */
function gb_add_column($concerts_columns) {
    $concerts_columns['group'] = __('Group');
    $concerts_columns['img'] = __('Image');
    return $concerts_columns;
}

/* Shoe the groups custom column to posts list screen */
function gb_column_content($column_name, $post_ID) {
    switch( $column_name ){
        case 'group' :
            global $wpdb, $gb_group_table;
            $post_group_id = get_post_meta( $post_ID , 'gb_group' , true );
            $sql = $wpdb->prepare("SELECT groups FROM $gb_group_table WHERE $gb_group_table.id = %d", $post_group_id);
            $result = $wpdb->get_var($sql);
            if($result)
                echo $result;
            else
                echo 'Unknown';
            break;
        case 'img' :
            $post_img = get_post_meta( $post_ID , 'gb_img' , true );
            if($post_img != "")
                echo '<div class="gb_img_post_list"><a href="'.get_edit_post_link($post_ID).'"><img src="'.$post_img.'" width="100"></a></div>';
            else
                echo '<div class="gb_img_post_list">Unknown</div>';
            break;
        default :
            break;
    }
}

/* save/update the $gb_group_post_table */
function gb_save_post($post_id, $post){
    global $wpdb, $gb_group_post_table;
    $the_group = $_POST['gb_the_group'];
    $my_result = "";
    $my_result = $wpdb->get_row("SELECT * FROM $gb_group_post_table WHERE postid = $post_id");
    if(!is_null($my_result)){
        $wpdb->update(
            $gb_group_post_table,
            array(
                'groupsid' => $the_group
            ),
            array( 'postid' => $post_id ),
            array(
                '%d'
            ),
            array( '%d' )
        );
    }else{
        $wpdb->insert(
            $gb_group_post_table,
            array(
                'groupsid' => $the_group,
                'postid' => $post_id
            ),
            array(
                '%d',
                '%d'
            )
        );
    }
}

/* Order the groups custom column */
function concerts_sort($columns) {
    $custom = array(
        'groups' 	=> 'groups'
    );
    return wp_parse_args($custom, $columns);
}

function gb_column_order_by( $vars ) {
    if ( isset( $vars['orderby'] ) && 'groups' == $vars['orderby'] ) {
        $args = array(
            'meta_query'=> array(
                array(
                    'key' => 'gb_index',
                    'compare' => '>=',
                    'value' => 5,
                    'type' => 'numeric',
                )
            )
        );

    }
    return $vars;
}

/* ajax return all the options for grope combo box(#groups)*/
function get_groups(){
    if(!isset($_POST['gb_nonce']) || !wp_verify_nonce($_POST['gb_nonce'], 'gb_nonce'))
        die('<h2 class="gb_error_message">Your access has been denied</h2>');
    global $gb_group_table, $wpdb;
    $group_list = $wpdb->get_results( "SELECT * FROM $gb_group_table ");
    $options = '';
    if($group_list){
        foreach($group_list as $i => $group){
            $options.="<option value='".$group->id."'>".$group->groups."</option>";
        }
    }else{
        $options="Error";
    }
    echo 'gb='.$options.'=gb';

    die();
}

/* ajax admin get all the group related posts */
function get_group_posts(){
    if(!isset($_POST['gb_nonce']) || !wp_verify_nonce($_POST['gb_nonce'], 'gb_nonce'))
        die('<h2 class="gb_error_message">Your access has been denied</h2>');
    if(isset($_POST['selected_group'])){
        global $gb_post_type, $gb_group_table, $wpdb;
        $my_group_id = $_POST['selected_group'];
        $my_group = $wpdb->get_results( "SELECT groups FROM $gb_group_table WHERE id = ".$my_group_id );
        $args = array(
            'post_type' => $gb_post_type,
            'meta_key' => 'gb_index',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'gb_group',
                    'compare' => '=',
                    'value' => $my_group_id
                )
            ),
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        query_posts( $args );
        if (have_posts()) :
            $gb_return_group = "<form id='group-submit' action='' method='POST'><div class='gb_close'>X</div><div class='gb_admin_group_con'><ul id='gb_admin_group_items'>";
            while (have_posts()) : the_post();
                $title = get_the_title();
                $img = get_post_meta( get_the_ID(), 'gb_img',true );
                $index = get_post_meta( get_the_ID(), 'gb_index',true );
                $gb_return_group .= "<li><div class='gb_group_admin_item' id='gb_post-".get_the_ID()."'>";
                $gb_return_group .= "<div class='gb_index'>".$index."</div>";
                $gb_return_group .= "<img src='".$img."'>";
                $gb_return_group .= "<h2>".$title."</h2>";
                $gb_return_group .= "</div></li>";
            endwhile;
            $gb_return_group .= "</ul></div><br>";
            $gb_return_group .= "<div class='group_submit_con'>";
            $gb_return_group .= "<div class='group_withing'></div>";
            $gb_return_group .= "</div></form>";
            echo 'gb='.$gb_return_group.'=gb';
        else :
            echo 'gb=<h2 class="gb_error_message">No post yet, created</h2>=gb';
        endif;
    }else{
        echo 'gb=<h2 class="gb_error_message">No group found</h2>=gb';
    }

    die();
}

/* ajax to add a group to DB */
function gb_add_group(){
    if(!isset($_POST['gb_nonce']) || !wp_verify_nonce($_POST['gb_nonce'], 'gb_nonce'))
        die('<h2 class="gb_error_message">Your access has been denied</h2>');
    if(isset($_POST['gb_add_groups'])){
        global $wpdb, $gb_group_table;
        $bg_groups_to_add = array_unique($_POST['gb_add_groups']);
        $bg_groups_all = $wpdb->get_results( "SELECT * FROM $gb_group_table" );
        foreach($bg_groups_all as $group){
            if(in_array($group->groups,$bg_groups_to_add)){
                $gb_not_to_insert[] =  $group->groups;
            }
        }
        if(isset($gb_not_to_insert)){
            foreach($bg_groups_to_add as $group){
                if(!in_array($group,$gb_not_to_insert)){
                    $bg_groups_to_insert[] = $group;
                }
            }
        }else{
            $bg_groups_to_insert = $bg_groups_to_add;
        }
        $insert_success = true;
        foreach($bg_groups_to_insert as $group){
            if($insert_success)
                $insert_success = $wpdb->insert( $gb_group_table, array('groups' => $group));
            else{
                $insert_success = false;
                if(isset($last_group))
                    $gb_not_to_insert[] = $last_group;
                else
                    $gb_not_to_insert[] = $group;
            }
            $last_group = $group;
        }
        if($insert_success && empty($gb_not_to_insert))
            echo 'gb=<h2 class="gb_good_message">The Group/s was added successfully</h2>=gb';
        else
            echo 'gb=<h2 class="gb_error_message">Error inserting to database '.implode(',', $gb_not_to_insert).'</h2>=gb';
    }else{
        echo 'gb=<h2 class="gb_error_message">No group was found to be added</h2>=gb';
    }

    die();
}

/* ajax to delete a group from DB */
function gb_delete_group(){
    if(!isset($_POST['gb_nonce']) || !wp_verify_nonce($_POST['gb_nonce'], 'gb_nonce'))
        die('<h2 class="gb_error_message">Your access has been denied</h2>');
    global $wpdb, $gb_group_table;
    $not_to_delete = $wpdb->get_var( "SELECT id FROM $gb_group_table WHERE groups = 'General'" );
    if(isset($_POST['selected_group_id']) && $_POST['selected_group_id'] != $not_to_delete){
        $group_id = $_POST["selected_group_id"];
        $sql = $wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = 1 WHERE meta_key = 'gb_group' and meta_value = %d",$group_id);
        $wpdb->query($sql);
        $result = $wpdb->delete( $gb_group_table, array( 'ID' => $_POST['selected_group_id'] ) );
        if($result > 0)
            echo 'gb=<h2 class="gb_good_message">Deleted Successfully</h2>=gb';
        else
            echo 'gb=<h2 class="gb_error_message">No Group was found</h2>=gb';
    }else{
        echo 'gb=<h2 class="gb_error_message">'.($_POST['selected_group_id'] == $not_to_delete ? 'This Group can not be Deleted':'Error no group id found').'</h2>=gb';
    }


    die();
}

/* check short code group */
function gb_check_group(){
    if(!isset($_POST['gb_nonce']) || !wp_verify_nonce($_POST['gb_nonce'], 'gb_nonce_admin'))
        die('<h2 class="gb_error_message">'.$_POST['gb_nonce'].'</h2>');
    if(isset($_POST['gb_group'])){
        global $wpdb, $gb_group_table,$gb_post_type;
        $group_id = $_POST['gb_group'];
        $gb_args = array(
            'post_type' => $gb_post_type,
            'meta_key' => 'gb_index',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'gb_group',
                    'compare' => '=',
                    'value' => $group_id
                )
            ),
            'posts_per_page' => 1,
            'post_status' => 'publish'
        );
        $gallery_query = null;
        $gallery_query = new WP_Query($gb_args);
        if( $gallery_query->have_posts() ) {
            echo 'gb=0=gb';
        }else{
            $sql = $wpdb->prepare( "SELECT groups FROM $gb_group_table WHERE id = %d", $group_id);
            $group_name = $wpdb->get_var($sql);
            echo 'gb=No images was found to group: "'.$group_name.'"=gb';
        }

    }else{
        echo 'gb=No group was found=gb';
    }

    die();
}

// START OF EFFECT ADMIN
// ajax Show the effect of the post
function gb_show_effect(){
    if(isset($_POST['gb_post_id'])){
        //Start load effects
        global $wpdb, $gb_effect_table;
        $sql = "SELECT * FROM  $gb_effect_table";
        $gb_effects = $wpdb->get_results($sql);
        $gb_effects_html = "<div class='gb_effects_con'>";
        $gb_effects_html .= "<select>";
        $gb_effects_html_helper = "<ul>";
        if ($gb_effects){
            foreach($gb_effects as $gb_the_effect){
                $gb_effects_html .= "<option value='".$gb_the_effect->id."'>".$gb_the_effect->name."</option>";
                $gb_effects_html_helper .= "<li value='".$gb_the_effect->id."'>".$gb_the_effect->effects."</li>";
            }
        }
        $gb_effects_html_helper .= "</ul>";
        $gb_effects_html .= "</select>";
        $gb_effects_html .= "<input type='button' value='".__("View special Effect")."' class='gb_db_effect button-primary gb_button-primary'>";
        $gb_effects_html .= $gb_effects_html_helper;
        $gb_effects_html .= "</div>";
        //End load effects
        $gb_post_id = $_POST['gb_post_id'];
        $gb_img_path = get_post_meta( $gb_post_id, 'gb_img',true);
        $gb_img_effect = get_post_meta( $gb_post_id, 'gb_effect',true);
            $gb_img_width = (round($_POST['gb_con_width'] / 100) * 100);
            switch ($gb_img_width) {
                case ($gb_img_width > 999):
                    $gb_img_width = 900;
                    break;
                case ($gb_img_width > 599):
                    $gb_img_width = 400;
                    break;
                default:
                    $gb_img_width = 200;
            }
            $gb_response = '';
            $gb_response .= "<div class='gb_btn_effect_con'><input class='button-primary gb_button-primary gb_save_effect' type='button' value='".__("Save Effect")."'>";
            $gb_response .= "<input class='button-primary gb_copy_effect' type='button' value='".__("Copy Effect")."'>";
            $gb_response .= "<input class='button-primary gb_button-secondary gb_load_effect' type='button' value='".__("Load Effect")."'>";
            $gb_response .= "<input class='button-primary gb_new_effect' type='button' value='".__("New Effect")."'></div>";
            $gb_response .= "<div class='gb_post_img_con'><div class='gb_post_img' id='gb_post-".$gb_post_id."' style='width:".$gb_img_width."px'>";
            $gb_response .= "<img width='$gb_img_width' height='auto' src='".$gb_img_path."'>";
            $gb_response .= "</div><input type='hidden' id='gb_old_effect' value='".$gb_img_effect."'></div>";
            $gb_response .= "<div class='gb_btn_effect_con'><input class='button-primary gb_button-primary gb_save_effect' type='button' value='".__("Save Effect")."'>";
            $gb_response .= "<input class='button-primary gb_copy_effect' type='button' value='".__("Copy Effect")."'>";
            $gb_response .= "<input class='button-primary gb_button-secondary gb_load_effect' type='button' value='".__("Load Effect")."'>";
            $gb_response .= "<input class='button-primary gb_new_effect' type='button' value='".__("New Effect")."'></div>";
            echo 'gb='.$gb_response.'=gb';
    }else{
        echo 'gb=1=gb';
    }

    die();
}

// ajax Save the effect to the meta
function gb_save_effect(){
    if(isset($_POST['gb_effect_matrix'])){
        if(isset($_POST['gb_post_id'])){
            $gb_new_matrix = gb_get_effect_matrix($_POST['gb_effect_matrix']);
            $result = update_post_meta($_POST['gb_post_id'], 'gb_effect', implode (", ", $gb_new_matrix));
            if($result > 0)
                echo 'gb=<h2 class="gb_good_message">The effect was saved successfully</h2>=gb';
            else
                echo 'gb=<h2 class="gb_error_message">Meta was not saved, there is nothing to update</h2>=gb';
        }else{
            echo 'gb=<h2 class="gb_error_message">Error no post for the effect was found</h2>=gb';
        }
    }else{
        echo 'gb=<h2 class="gb_error_message">Error no effect was found</h2>=gb';
    }

    die();
}
// ajax Copy the effect
function gb_copy_effect(){
    if(isset($_POST['gb_effect_matrix'])){
        $gb_effect_array = gb_get_effect_matrix($_POST['gb_effect_matrix']);
        $gb_the_effect = implode (", ", $gb_effect_array);
        echo 'gb='.$gb_the_effect.'=gb';
    }
}
//build GB effect, return the GB effect
function gb_get_effect_matrix($gb_effect){
    if($gb_effect){
        $gb_effect_matrix = explode(',', $gb_effect);
        $gb_new_matrix = array_fill(0,100,-1);
        foreach($gb_effect_matrix as $i => $index){
            if(is_numeric($gb_effect_matrix[$i])){
                $gb_new_matrix[$i] = $index;
            }
        }

        for($x = count($gb_effect_matrix), $next_index = 0, $new_matrix_length = count($gb_new_matrix);$next_index < $new_matrix_length;$next_index++){
            if(!in_array($next_index, $gb_new_matrix)){
                $gb_new_matrix[$x] = $next_index;
                $x++;
            }
        }
    }else{
        $gb_new_matrix = array('No Effect Found');
    }
    return $gb_new_matrix;
}

// change the option for saving the data on delete plugin
function gb_change_delete_option(){
    global $gb_gallery_option;
    if(! isset($gb_gallery_option))
        gb_global_set();
    $gb_delete_option = get_option( $gb_gallery_option );
    if($gb_delete_option == '0'){
        update_option( $gb_gallery_option, '1' );
        echo 'gb=1=gb';
    }else{
        update_option( $gb_gallery_option, '0' );
        echo 'gb=0=gb';
    }
    die();
}

// change the help option
function gb_change_help_option(){
    global $gb_gallery_help_option;
    if(! isset($gb_gallery_help_option))
        gb_global_set();
    $gb_help_option = get_option( $gb_gallery_help_option );
    if($gb_help_option == '0'){
        update_option( $gb_gallery_help_option, '1' );
        echo 'gb=1=gb';
    }else{
        update_option( $gb_gallery_help_option, '0' );
        echo 'gb=0=gb';
    }
    die();
}

// change the donate option
function gb_change_donate_option(){
    global $gb_gallery_donate_option;
    if(! isset($gb_gallery_donate_option))
        gb_global_set();
    $gb_donate_option = get_option( $gb_gallery_donate_option );
    if($gb_donate_option == '0'){
        update_option( $gb_gallery_donate_option, '1' );
        echo 'gb=1=gb';
    }else{
        update_option( $gb_gallery_donate_option, '0' );
        echo 'gb=0=gb';
    }
    die();
}
?>