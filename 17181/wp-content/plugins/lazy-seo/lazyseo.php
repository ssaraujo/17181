<?php
/**
 * @package Lazy_SEO
 * @version 1.7.0
 */
/*
Plugin Name: Lazy SEO
Plugin URI: http://www.lazy-seo-plugin.com/
Description: The Lazy SEO plugin will help automatically optimize a site for SEO best pracices using a specific set of keywords and locations. After installation, go to the <a href="admin.php?page=lazy-slug">settings page</a>.
Author: Daniel Morris
Author URI: http://www.linkedin.com/in/danielryanmorris
Version: 1.7.0

    Copyright 2013  Daniel Morris  (email : danielryanmorris@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* Filters */
//replaces first h1 with exact match geo
add_filter ( 'the_content', 'lazy_seo_h1');

//Adds title tags to site - 20 priority to ensure last run
add_filter( 'wp_title', 'lazy_seo_set_title', 20 );

/* need to test */
//Adds settings option to installed plugin page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lazy_seo_add_plugin_action_links' );

/* Actions */
//Runs admin menu
add_action( 'admin_menu', 'lazy_seo_admin_menu' );

//adds meta description to <head>
add_action( 'wp_head', 'lazy_seo_meta_desc' );

// Define the custom box 
add_action( 'add_meta_boxes', 'lazy_seo_add_custom_box' );

// Do something with the data entered 
add_action( 'save_post', 'lazy_seo_save_postdata' );

//removes meta description  from <head>
remove_action('wp_head', 'description');

/* Custom Functions */
//returns an array of key/geo combined geos
function lazy_seo_get_titles( $keys, $geo) {
    
    //removes blank values
    $keys = (array) array_values( array_unique( array_filter( $keys ) ) );
    $geo = (array) array_values( array_unique( array_filter( $geo ) ) ); 
    
    //gets the number of options used
    $num = (int) get_option( 'lazy-number' );
    $geonum = (int) get_option( 'lazy-number-geo' );
    
    //makes sure that only the number of keywords set in options is used
    $keys = array_slice($keys, 0, max(0, $num ) );
    $geo = array_slice($geo, 0, max(0, $geonum ) );
    
    //updates sanitized options, probably better place for this somewhere
    update_option( 'lazy-number', count($keys) );
    update_option( 'lazy-number-geo', count($geo) );
    update_option( 'lazy-keywords', $keys );
    update_option( 'lazy-geo', $geo );

    // shuffles both arrays to ensure random
    shuffle($keys);
    shuffle($geo);
    
    //removed duplicates then re-index
    $keys = array_values( array_unique( $keys ) );
    $geo = array_values( array_unique( $geo ) );
    
    //creates every combo of keywords and geolocations
    $titles = array();
  
  //makes sure there are at least 3 kw's and same number as geos
  while(count($keys) <= max(3, $geonum) and $num > 0) {
    $keys[] = $keys[array_rand($keys)];
  } 
  
  //makes sure there are at least 3 geo's and same number a keys
  while(count($geo) <= max(3, $num) and $geonum > 0) {
    $geo[] = $geo[array_rand($geo)];     
  }      
     
  if($geonum > 0 && $num > 0){    
    //if both keys and goes, sets smallest possible random, no repeats of key or geo
    for($i=0; $i< max( count($keys), count($geo) ); $i++){      
      $titles[$i] = $keys[$i].", ".$geo[$i];      
    }
  } elseif ( $num > 0) {
    //if there are no geos, sets titles to keywords
    $titles = $keys;
  } elseif ($geonum > 0) {
    //if no keys, sets titles to geos
    $titles = $geo;
  } else {
    //if no keys or geos, mock webmaster
    $titles = array("Are", "you", "really", "that", "lazy?");
  }
    //returns the new title array
    return $titles;
}
function lazy_seo_unshift( $array, $first) {
  
  //checks to make sure that the new string is valid  
  if( strlen($first) > 0 ) {  
    //adds priority kw to beginning
    array_unshift($array, $first);
    
    //makes sure that titles are unique
    return array_values(array_unique($array));
  }
}

//Defines and sets the title tags based for the add_filter call
function lazy_seo_set_title ( $title ) {
    
    //gets priority keyword
    $firstkw  = (string) get_post_meta(get_the_ID(), 'lazy_seo_meta_key', true );
    
    //gets prioritty geo
    $firstgeo  = (string) get_post_meta(get_the_ID(), 'lazy_seo_meta_key_geo', true );
    
    //imports options
    $keywords = (array) get_option('lazy-keywords');
    $geo = (array) get_option('lazy-geo');
    
    //sets random first kw if no priority keyword
    if (strlen ($firstkw) < 1 and count($keywords)>0 ) {
        $firstkw = $keywords[array_rand($keywords)];
    }
    //sets random first geo if no priority geo 
    if (strlen ($firstgeo) < 1 and count($geo) > 0) {  
        $firstgeo = $geo[array_rand($geo)];
    }
    
    //gets the combo of titles then shuffles them
    $titles = (array) lazy_seo_get_titles($keywords, $geo);
    shuffle($titles);
    
    //sets first keyword/geo combo
    $first = "";
    if(strlen($firstkw) > 0 ) {
      $first .= $firstkw;
      if(strlen($firstgeo) > 0) {
        $first .= " ";  
      }
    }
    
    //adds geo if it is not null
    if(strlen($firstgeo) > 0) {
      $first .= $firstgeo;
    } 
    
    //adds first keyword/geo combo to front of array, removes duplicates
    $titles = lazy_seo_unshift( $titles, $first);
    
  if(count($titles) > 0 && !is_home() ){
    //Returns the first three keywords separated by |
    return join( " | ", array_slice($titles,0,min(3, count($titles) ) ) ); 
  } else {    
    return $title;
  }   
}     

//generates the meta description from the_content
function lazy_seo_meta_desc() {
  //global $post;
  $ident = get_the_ID();
  
 if(get_post_meta( $ident, 'lazy_seo_meta_check', true) >0 ) {
  echo "<meta name='description' content='".esc_attr( get_post_meta( $ident, 'lazy_seo_meta_desc', true) )."' />"; 

 } else {   
  //pulls in meta box data for post
  $kw  = (string) get_post_meta( $ident, 'lazy_seo_meta_key', true );
  $geo  = (string) get_post_meta( $ident, 'lazy_seo_meta_key_geo', true );
  
  //removes any unnessicary scripts or code from post  
  $meta = (string) sanitize_text_field( get_the_content() );
  $meta = preg_replace( '/\[[^\]]*\]/', '', ( $meta ) );
  $meta = str_replace( array( "\\n", "\\r", "\\t" ), ' ', $meta);
  
  $add = (string) "";
  
  //adds info in about priority keyword and geo if they exhist

  if(strlen($kw) > 0) {
    $add = "For information about ".$kw;
    
    if(strlen($geo) > 0) {  
      $add .=" in ".$geo; 
    }
    $add .= ", c";    
  }elseif(strlen($geo) > 0) {
    $add = "If you are in ".$geo.", c";
  }else {
    $add = "C";
  }
  
  $add .= "all us today! ";

  //sets length of $meta substring 
  $len = (int) 155 - strlen($add);

  //gets two possible $meta - 1) substring of max length after $add, 2) first sentence
  $temp1 = (string) substr( $meta, 0, $len );
  $temp2 = (array) explode(".", $meta, 2);
  
  $temp1len = (int) strlen($temp1);
  $temp2len = (int) strlen( $temp2[0] );

 //if it is the home page, sets the meta description to the default
  if(is_home() or ($temp1len < 10 and $temp2len < 10) ) {
    $meta = get_bloginfo( 'description' ); 
  //sets $meta to whatever is smaller between 2 possible $meta
  }elseif ( $temp1len < $temp2len ) {    
    $meta = $temp1;  
  } else { 
    $meta = $temp2[0].".";
  }
  
  //adds meta description to <head>
  echo "<meta name='description' content='".esc_attr($add.$meta)."' />";
  
 }
}


function lazy_seo_h1( $content) {  
  //exits function if $content is not in main 
  if (!in_the_loop () || !is_main_query ()) {
        return $content;
    }
        
  //Auto set focus if not already selected. Runs before h1's so randomness does not affect.
  lazy_seo_auto_set( $content );
    
    $ident = get_the_ID();
     
  //makes sure that the WM wants to replace h1's in the content, and that the content is for a page  
  if( (get_option('lazy-check') > 0 or get_post_meta( $ident, 'lazy_seo_meta_h1', true ) > 0 )&& is_page() ) {
     
    //imports options
    $keys = (array) get_option('lazy-keywords');
    $geos = (array) get_option('lazy-geo');
    
    //gets the number of options used
    $num = (int) get_option( 'lazy-number' );
    $geonum = (int) get_option( 'lazy-number-geo' );
    
    //makes sure that only the number of keywords set in options is used
    $keys = array_slice($keys, 0, max(0, $num ) );
    $geos = array_slice($geos, 0, max(0, $geonum ) );
    
    //adds blank option
    $keys[] = "";
    $geos[] = "";
    
    //removed duplicates then re-index
    $keys = array_values( array_unique($keys) );
    $geos = array_values( array_unique($geos) );
    
    //imports the priority keyword/geo combo set in edit page
    $kw  = (string) get_post_meta( $ident, 'lazy_seo_meta_key', true );
    $geo  = (string) get_post_meta( $ident, 'lazy_seo_meta_key_geo', true );    
    
    if( in_array($kw,$keys) and in_array($geo,$geos) ) {
      
        //the regex used for the replacement
        $regex = (string) '#<\s*?h1\b[^>]*>(.*?)</h1\b[^>]*>#s';
      
        //start of replacement text - openning <h1>
        $replace = (string) "<h1>";
      
        //checks if the priority keyword exists, and places it in the replacement h1 if it does
        if( strlen($kw) > 0 ) {
          $replace .= $kw;
          //selects a random keyword and places it in replacement if no prioity keyword exists
        } else {
          $keywords = get_option('lazy-keywords');
          $replace .= $keywords[array_rand($keywords)];
        }
        //checks if prioity geo exists and adds to replacement h1 if it does
        if(strlen($geo) > 0){
          $replace .= " in ".$geo;
        }
        
        //closing h1 tag for replacement header
        $replace .= "</h1>";
        
        //checks if there already is an h1 tag in the_content, if false add replacement to beginning, if true replaces it  
        if( strpos($content,'<h1') === false ) {
          return $replace.$content;
        } else {
          return preg_replace( $regex, $replace, $content, 1 );      
        }    
     } else {
        //if priority is not in possible kw/geo, update to ""
        update_post_meta( $ident, 'lazy_seo_meta_key', "" );
        update_post_meta( $ident, 'lazy_seo_meta_key_geo', "" );
     }
  }
  //returns unmodified content 
  return $content;
}

function lazy_seo_search_str ( $num, $array, $content) {
//returns the most used word in the content    
    
    //sets up empty variables for use in function
    $max = 0;
    $select = 0;
    $maincount = array();
    $test = array();
    $testcount = array();
    
    //makes sure that case is not an issue
    $content = sanitize_text_field(strtolower($content));
    
    //removes blank and duplicate values then reassigns keys
    $array = array_values(array_unique(array_filter($array)));
    
    //randomizes array so priority is random in case of tie
    shuffle($array);
        
    //loops through array to count how many occurances exist
    for ($i = 0; $i < min( $num, count($array) ); $i++) {
      //splits array into words
      $test = explode( " ", $array[$i] );
      //makes sure each word is unique and not blank, then reindexes them
      $test = array_values(array_unique(array_filter($test)));
      
      $maincount = substr_count($content, strtolower($array[$i]));
      //counts the occurance of each word in current keyword within the_content
      for ($j = 0; $j < count($test); $j++){
        $testcount[$j] = substr_count($content, strtolower($test[$j]));
      }
      
      //sets current average count/word to $avg
      $avg = (array_sum($testcount) / count($testcount) ) + $maincount;
      
      //checks to see if current $avg is highest so far
      if( $max < $avg ){
        //sets new max average
        $max = $avg;
        
        //saves index
        $select = $i;
      }  
    }
    
    //makes sure a keyword was used, if it was, returns keyword with highest average usage, otherwise returns empty string
    if($max > 0)
      return $array[ $select ];
    else
      return "";     
}

function lazy_seo_auto_set( $content ) {
  //this will set a priority keyword based on the content
  $kwnum = get_option( 'lazy-number' );
  $keys = get_option('lazy-keywords');
  $geonum = get_option( 'lazy-number-geo' );
  $geo = get_option('lazy-geo');
   
  //if kw is not set, sets on if possible
  if ( !in_array( get_post_meta(get_the_ID(), 'lazy_seo_meta_key', true ), $keys ) ) {
    if( $kwnum > 1) {
      //only sets new keyword priority if one is not set.   
      update_post_meta( get_the_ID(), 'lazy_seo_meta_key', lazy_seo_search_str($kwnum, $keys, $content) );
    }
    if( $kwnum == 1) {
      //sets kw if there is only one
      $tempkw = array_values(array_unique(array_filter($keys)));   
      update_post_meta( get_the_ID(), 'lazy_seo_meta_key', $tempkw[0] );
    }
  }
  
  //if geo is not set, sets one if possible  
  if ( !in_array( get_post_meta(get_the_ID(), 'lazy_seo_meta_key_geo', true ), $geo ) ) {
    if ($geonum > 1) {
      //only sets new geo priority if one is not set.
      update_post_meta( get_the_ID(), 'lazy_seo_meta_key_geo', lazy_seo_search_str($geonum, $geo, $content) );
    }
    if( $geonum == 1) {    
      //sets geo if there is only one
      $tempgeo = array_values(array_unique(array_filter($geo)));
      update_post_meta( get_the_ID(), 'lazy_seo_meta_key_geo', $tempgeo[0] );
    }
  }       
  
  //returns
  return;  
}

/* Menus and Options */
function lazy_seo_admin_menu () {
//checks to make sure user is admin
if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __('You are not allowed to access this part of the site') );
	} 
       
    //Runs option page
    add_options_page( 'Lazy SEO Plugin Settings' , 'Lazy SEO Settings' , 'manage_options', 'lazy-slug', 'lazy_seo_settings_page');
    //runs admin init
    add_action( 'admin_init', 'register_lazy_seo_settings' );
}

//menu page for settings
function lazy_seo_settings_page(){
?>    
   <div class="wrap">
    <?php screen_icon(); ?>
    <h2>Lazy SEO</h2>
    <form method="post" action="options.php">
    <?php settings_fields( 'lazy-seo' ); ?> 
    <?php //do_settings_sections( 'lazy-seo' );?>
    <?php $keywords = get_option('lazy-keywords'); ?>
    <?php $geo = get_option('lazy-geo'); ?>
    <table class="form-table">
      <tr valign="top">
          <th scope="row">Replace h1's?</th>
          <td><input type="checkbox" name="lazy-check" value="1" <?php checked( get_option('lazy-check'), 1 ); ?> /></td>
        </tr>
      <tr></tr>
      <tr valign="top">
          <th scope="row">Number of Keywords</th>
          <td><input type="number" name="lazy-number" value="<?php echo esc_attr(get_option('lazy-number')); ?>" /></td>
        </tr>
        
      <?php for($i = 0; $i < get_option('lazy-number'); $i++) { ?>
        <tr valign="top">
          <th scope="row">Keyword <?php echo $i+1; ?></th>
          <td><input type="text" name="lazy-keywords[<?php echo $i; ?>]" value="<?php echo esc_attr($keywords[$i]); ?>" /></td>
        </tr>       
      <?php } ?>
      <tr></tr>
      <tr valign="top">
          <th scope="row">Number of Geo-locations</th>
          <td><input type="number" name="lazy-number-geo" value="<?php echo esc_attr(get_option('lazy-number-geo')); ?>" /></td>
        </tr>
        
      <?php for($j = 0; $j < get_option('lazy-number-geo'); $j++) { ?>
        <tr valign="top">
          <th scope="row">Geo <?php echo $j+1; ?></th>
          <td><input type="text" name="lazy-geo[<?php echo $j; ?>]" value="<?php echo esc_attr($geo[$j]); ?>" /></td>
        </tr>       
      <?php } ?>
      
      <?php /* This is where a checkbox will go that selects to run on all pages
      <br /><br />
      <tr valign="top">
          <th scope="row">Reset all Priorities</th>
          <td><input type="checkbox" name="lazy-prior" value="1" <?php checked( get_option('lazy-all-pages'), 1 );  /></td>
       </tr>


*/ ?>  
      
      
    </table>    
    <?php submit_button(); ?>
    </form>
      <br />
      <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=4XHBZVKFG47UE&lc=US&item_name=Lazy%20SEO%20Plugin&item_number=Lazy%20SEO%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">
      	<img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="" />
      </a>
    </div>
<?php       
}

//registers settings
function register_lazy_seo_settings() {
  if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __('You are not allowed to access this part of the site') );
	}  
  
  //adds all options for admin settings
  add_option('lazy-keywords', array( 'key1', 'key2', 'key3', 'key4', 'key5', 'key6' ) );
  add_option('lazy-geo', array( 'geo1' ) );
  add_option('lazy-number', 6);
  add_option('lazy-number-geo', 1);
  add_option('lazy-check', 0);  
  
  //registers all options for admin settings and includes sanitization callback
  register_setting( 'lazy-seo', 'lazy-number', 'absint' );
  register_setting( 'lazy-seo', 'lazy-number-geo', 'absint' );  
  register_setting( 'lazy-seo', 'lazy-keywords', 'sanitize_lazy_array' );
  register_setting( 'lazy-seo', 'lazy-geo', 'sanitize_lazy_array' );
  register_setting( 'lazy-seo', 'lazy-check', 'absint' );
  //register_setting( 'lazy-seo', 'lazy-all-pages', 'absint' ); 
}

//sanitizes arrays of text
function sanitize_lazy_array( $check ){
  return array_map( 'sanitize_text_field', $check);
}

// Adds a box to the main column on the Post and Page edit screens 
function lazy_seo_add_custom_box() {
    $screens = array( 'post', 'page' );
    foreach ($screens as $screen) {
        add_meta_box(
        'lazy-seo-box', // id, used as the html id att
        __( 'Lazy SEO Settings' ), // meta box title
        'lazy_seo_inner_custom_box', // callback function, spits out the content
        'post', 
        'side', // context, where on the screen
        'default' // priority, where should this go in the context
    );
    
        add_meta_box(
        'lazy-seo-box', // id, used as the html id att
        __( 'Lazy SEO Settings' ), // meta box title
        'lazy_seo_inner_custom_box', // callback function, spits out the content
        'page', 
        'side', // context, where on the screen
        'default' // priority, where should this go in the context
    );
    
    }
}

// Prints the box content 
function lazy_seo_inner_custom_box() {
  global $post;
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'lazy_seo_noncename' );
  
  //gets list of options from settings page 
  $kws = get_option('lazy-keywords');
  $geo = get_option('lazy-geo');
   
  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $value = get_post_meta( $post->ID, 'lazy_seo_meta_key', true);
  $geovalue = get_post_meta( $post->ID, 'lazy_seo_meta_key_geo', true);
  $h1 = get_post_meta( $post->ID, 'lazy_seo_meta_h1', true);
  $check = get_post_meta( $post->ID, 'lazy_seo_meta_check', true); 
  $desc = get_post_meta( $post->ID, 'lazy_seo_meta_desc', true); 
  
  //adds current selection to beginning
  array_unshift($kws, $value);
  array_unshift($geo, $geovalue);

  //adds blank option
  $kws[] = "";
  $geo[] = "";
  
  //removes duplicate values  
  $kws = array_values(array_unique($kws));
  $geo = array_values(array_unique($geo));

  //Keyword and Geo forms below  
?>
  <label for="lazy_meta_h1">H1 Replace?</label>
  <input type="checkbox" name="lazy_meta_h1" value="1" <?php checked( $h1, 1 ); ?> /><br />
    
  <label for="lazy_meta_kw">Page Focus Keyword</label>  
  <select name="lazy_meta_kw">
  <?php for($k = 0; $k < count($kws); $k++){?>
    <option value="<?php echo esc_attr($kws[$k]); ?>"><?php echo esc_attr($kws[$k]); ?></option>
  <?php } ?>
  </select><br />
  
  <label for="lazy_meta_geo">Page Focus Location</label>  
  <select name="lazy_meta_geo">
  <?php for($h = 0; $h < count($geo); $h++){?>
    <option value="<?php echo esc_attr($geo[$h]); ?>"><?php echo esc_attr($geo[$h]); ?></option>
  <?php } ?>
  </select><br />
  
  <label for="lazy_meta_check">Custom Meta Descripiton?</label>
  <input type="checkbox" name="lazy_meta_check" value="1" <?php checked( $check, 1 ); ?> /><br />
  
  <label for="lazy_meta_desc">Enter Custom Meta Description</label>
  <textarea cols="25" maxlength="255" name="lazy_meta_desc"><?php echo esc_attr($desc); ?></textarea>

<?php 
}

// When the post is saved, saves our custom data 
function lazy_seo_save_postdata( $post_id ) {

  // First we need to check if the current user is authorized to do this action. 
  if ( 'page' == $_REQUEST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['lazy_seo_noncename'] ) || ! wp_verify_nonce( $_POST['lazy_seo_noncename'], plugin_basename( __FILE__ ) ) )
      return;
  
  //get options to check to make sure input is one of them
  $kws = get_option('lazy-keywords');
  $geos = get_option('lazy-geo');
  
  //sanitizes input
  $kw = sanitize_text_field( (string) esc_attr($_POST['lazy_meta_kw']) );
  $geo = sanitize_text_field( (string) esc_attr($_POST['lazy_meta_geo']) );
  $h1 = absint( $_POST['lazy_meta_h1'] );
  $check = absint( $_POST['lazy_meta_check'] );
  $desc = sanitize_text_field( (string) esc_attr($_POST['lazy_meta_desc']) );
  
  //validates input, if not valid, converts to empty string
  if(!in_array($kw,$kws) ) {
    $kw = "";
  }
  if(!in_array($geo,$geos) ) {
    $geo = "";
  }
    
  // Thirdly we can save the value to the database
  update_post_meta( (int) $post_id, 'lazy_seo_meta_key', $kw );
  update_post_meta( (int) $post_id, 'lazy_seo_meta_key_geo', $geo );
  update_post_meta( (int) $post_id, 'lazy_seo_meta_h1', $h1 );
  update_post_meta( (int) $post_id, 'lazy_seo_meta_check', $check );  
  update_post_meta( (int) $post_id, 'lazy_seo_meta_desc', $desc );
}

//Adds Settings link in plugin menu
function lazy_seo_add_plugin_action_links( $links ) {
   
  return array_merge( array( 'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=lazy-slug">Settings</a>'),$links);
   
}