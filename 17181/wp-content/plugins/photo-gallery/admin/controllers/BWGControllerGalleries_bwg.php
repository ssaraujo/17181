<?php

class BWGControllerGalleries_bwg {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function execute() {
    $task = ((isset($_POST['task'])) ? esc_html(stripslashes($_POST['task'])) : '');
    $id = ((isset($_POST['current_id'])) ? esc_html(stripslashes($_POST['current_id'])) : 0);
    if (method_exists($this, $task)) {
      $this->$task($id);
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once WD_BWG_DIR . "/admin/models/BWGModelGalleries_bwg.php";
    $model = new BWGModelGalleries_bwg();

    require_once WD_BWG_DIR . "/admin/views/BWGViewGalleries_bwg.php";
    $view = new BWGViewGalleries_bwg($model);
    $this->delete_unknown_images();
    $view->display();
  }

  public function add() {
    require_once WD_BWG_DIR . "/admin/models/BWGModelGalleries_bwg.php";
    $model = new BWGModelGalleries_bwg();

    require_once WD_BWG_DIR . "/admin/views/BWGViewGalleries_bwg.php";
    $view = new BWGViewGalleries_bwg($model);
    $view->edit(0);
  }

  public function edit() {
    require_once WD_BWG_DIR . "/admin/models/BWGModelGalleries_bwg.php";
    $model = new BWGModelGalleries_bwg();

    require_once WD_BWG_DIR . "/admin/views/BWGViewGalleries_bwg.php";
    $view = new BWGViewGalleries_bwg($model);
    $id = ((isset($_POST['current_id']) && esc_html(stripslashes($_POST['current_id'])) != '') ? esc_html(stripslashes($_POST['current_id'])) : 0);
    $view->edit($id);
  }

  public function save_images() {
    $this->save_db();
    global $wpdb;
    if (!isset($_POST['current_id']) || (esc_html(stripslashes($_POST['current_id'])) == 0) || (esc_html(stripslashes($_POST['current_id'])) == '')) {
      $_POST['current_id'] = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'bwg_gallery');
    }
    $this->save_image_db();
    $this->edit();
  }

  public function save_order_images() {
    global $wpdb;
    $imageids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    if ($imageids_col) {
      foreach ($imageids_col as $imageid) {
        if (isset($_POST['order_input_' . $imageid])) {
          $order_values[$imageid] = (int) $_POST['order_input_' . $imageid];
        }
        else {
          $order_values[$imageid] = (int) $wpdb->get_var($wpdb->prepare('SELECT `order` FROM ' . $wpdb->prefix . 'bwg_image WHERE `id`="%d"', $imageid));
        }
      }
      asort($order_values);
      $i = 1;
      foreach ($order_values as $key => $order_value) {
        $wpdb->update($wpdb->prefix . 'bwg_image', array('order' => $i), array('id' => $key));
        $i++;
      }
    }
  }

  public function ajax_search() {
    $this->save_image_db();
    $this->save_order_images();
    if (isset($_POST['ajax_task']) && esc_html($_POST['ajax_task']) != '') {
      $ajax_task = esc_html($_POST['ajax_task']);
      $this->$ajax_task();
    }
    $this->edit();
  }

  public function recover() {
    global $wpdb;
    $id = ((isset($_POST['image_current_id'])) ? esc_html(stripslashes($_POST['image_current_id'])) : 0);
    $options = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'bwg_option WHERE id=1');
    $thumb_width = $options->thumb_width;
    $thumb_height = $options->thumb_height;    
    $this->recover_image($id, $thumb_width, $thumb_height);
  }
  
  public function image_recover_all() {
    global $wpdb;
    $options = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'bwg_option WHERE id=1');
    $thumb_width = $options->thumb_width;
    $thumb_height = $options->thumb_height;    
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $this->recover_image($gal_id, $thumb_width, $thumb_height);
      }
    }
  }
  
  public function recover_image($id, $thumb_width, $thumb_height) {
    global $WD_BWG_UPLOAD_DIR;
    global $wpdb;
    $image_data = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
    $filename = ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->image_url;
    $thumb_filename = ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->thumb_url;
    copy(str_replace('/thumb/', '/.original/', ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->thumb_url), ABSPATH . $WD_BWG_UPLOAD_DIR . $image_data->image_url);    
    list($width_orig, $height_orig, $type_orig) = getimagesize($filename);
    $percent = $width_orig / $thumb_width;
    $thumb_height = $height_orig / $percent;
    
    if ($type_orig == 2) {
      $img_r = imagecreatefromjpeg($filename);
      $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
      imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
      imagejpeg($dst_r, $thumb_filename, 100);
      imagedestroy($img_r);
      imagedestroy($dst_r);
    }
    elseif ($type_orig == 3) {
      $img_r = imagecreatefrompng($filename);
      $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
      imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
      imagealphablending($dst_r, FALSE);
      imagesavealpha($dst_r, TRUE);
      imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
      imagealphablending($dst_r, FALSE);
      imagesavealpha($dst_r, TRUE);
      imagepng($dst_r, $thumb_filename, 9);
      imagedestroy($img_r);
      imagedestroy($dst_r);
    }
    elseif ($type_orig == 1) {
      $img_r = imagecreatefromgif($filename);
      $dst_r = ImageCreateTrueColor($thumb_width, $thumb_height);
      imageColorAllocateAlpha($dst_r, 0, 0, 0, 127);
      imagealphablending($dst_r, FALSE);
      imagesavealpha($dst_r, TRUE);
      imagecopyresampled($dst_r, $img_r, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
      imagealphablending($dst_r, FALSE);
      imagesavealpha($dst_r, TRUE);
      imagegif($dst_r, $thumb_filename);
      imagedestroy($img_r);
      imagedestroy($dst_r);
    }
    ?>
    <script language="javascript">
      var image_src = window.parent.document.getElementById("image_thumb_<?php echo $id; ?>").src;
      document.getElementById("image_thumb_<?php echo $id; ?>").src = image_src + "?date=<?php echo date('Y-m-y H:i:s'); ?>";
    </script>
    <?php
  }

  public function image_publish() {
    $id = ((isset($_POST['image_current_id'])) ? esc_html(stripslashes($_POST['image_current_id'])) : 0);
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'bwg_image', array('published' => 1), array('id' => $id));
  }

  public function image_publish_all() {
    global $wpdb;
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $wpdb->update($wpdb->prefix . 'bwg_image', array('published' => 1), array('id' => $gal_id));
      }
    }
  }

  public function image_unpublish() {
    $id = ((isset($_POST['image_current_id'])) ? esc_html(stripslashes($_POST['image_current_id'])) : 0);
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'bwg_image', array('published' => 0), array('id' => $id));
  }

  public function image_unpublish_all() {
    global $wpdb;
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $wpdb->update($wpdb->prefix . 'bwg_image', array('published' => 0), array('id' => $gal_id));
      }
    }
  }

  public function image_delete() {
    $id = ((isset($_POST['image_current_id'])) ? esc_html(stripslashes($_POST['image_current_id'])) : 0);
    global $wpdb;
    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_comment WHERE image_id="%d"', $id));
    $tag_ids = $wpdb->get_col($wpdb->prepare('SELECT tag_id FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d"', $id));
    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d"', $id));
    // Increase tag count in term_taxonomy table.
    if ($tag_ids) {
      foreach ($tag_ids as $tag_id) {
        $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
      }
    }
  }

  public function image_delete_all() {
    global $wpdb;
    $image_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_image');
    foreach ($image_ids_col as $image_id) {
      if (isset($_POST['check_' . $image_id])) {
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $image_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_comment WHERE image_id="%d"', $image_id));
        $tag_ids = $wpdb->get_col($wpdb->prepare('SELECT tag_id FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d"', $image_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d"', $image_id));
        // Increase tag count in term_taxonomy table.
        if ($tag_ids) {
          foreach ($tag_ids as $tag_id) {
            $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
          }
        }
      }
    }
  }
  
  public function image_set_watermark() {
    global $wpdb;
    global $WD_BWG_UPLOAD_DIR;
    $options = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'bwg_option WHERE id=1');
    switch ($options->built_in_watermark_type) {
      case 'text':
        $images = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_image');
        foreach ($images as $image) {
          if (isset($_POST['check_' . $image->id])) {
            $this->set_text_watermark(ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, $options->built_in_watermark_text, $options->built_in_watermark_font, $options->built_in_watermark_font_size, '#' . $options->built_in_watermark_color, $options->built_in_watermark_opacity, $options->built_in_watermark_position);
          }
        }
        break;
      case 'image':
        $images = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_image');
        foreach ($images as $image) {
          if (isset($_POST['check_' . $image->id])) {
            $this->set_image_watermark (ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, ABSPATH . $WD_BWG_UPLOAD_DIR . $image->image_url, $options->built_in_watermark_url, $options->built_in_watermark_size, $options->built_in_watermark_size, $options->built_in_watermark_position);
          }
        }
        break;
    }    
  }
  
  function bwg_hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);
    return $rgb;
  }
  
  function bwg_imagettfbboxdimensions($font_size, $font_angle, $font, $text) {
    $box = @ImageTTFBBox($font_size, $font_angle, $font, $text) or die;
    $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
    $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
    $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
    $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
    return array(
      "width"  => ($max_x - $min_x),
      "height" => ($max_y - $min_y)
    );
  }

  function set_text_watermark ($original_filename, $dest_filename, $watermark_text, $watermark_font, $watermark_font_size, $watermark_color, $watermark_transparency, $watermark_position) {
    $watermark_transparency = 127 - ($watermark_transparency * 1.27);
    list($width, $height, $type) = getimagesize($original_filename);
    $watermark_image = imagecreatetruecolor($width, $height);

    $watermark_color = $this->bwg_hex2rgb($watermark_color);
    $watermark_color = imagecolorallocatealpha($watermark_image, $watermark_color[0], $watermark_color[1], $watermark_color[2], $watermark_transparency);
    $watermark_font = WD_BWG_DIR . '/fonts/' . $watermark_font;
    $watermark_font_size = ($height * $watermark_font_size / 500) . 'px';
    $watermark_position = explode('-', $watermark_position);
    $watermark_sizes = $this->bwg_imagettfbboxdimensions($watermark_font_size, 0, $watermark_font, $watermark_text);

    $top = $height - 5;
    $left = $width - $watermark_sizes['width'] - 5;
    switch ($watermark_position[0]) {
      case 'top':
        $top = $watermark_sizes['height'] + 5;
        break;
      case 'middle':
        $top = ($height + $watermark_sizes['height']) / 2;
        break;
    }
    switch ($watermark_position[1]) {
      case 'left':
        $left = 5;
        break;
      case 'center':
        $left = ($width - $watermark_sizes['width']) / 2;
        break;
    }

    if ($type == 2) {
      $image = imagecreatefromjpeg($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagejpeg ($image, $dest_filename, 100);
      imagedestroy($image);  
    }
    elseif ($type == 3) {
      $image = imagecreatefrompng($original_filename);
      imagettftext($image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imageColorAllocateAlpha($image, 0, 0, 0, 127);
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $dest_filename, 9);
      imagedestroy($image);
    }
    elseif ($type == 1) {
      $image = imagecreatefromgif($original_filename);
      imageColorAllocateAlpha($watermark_image, 0, 0, 0, 127);
      imagecopy($watermark_image, $image, 0, 0, 0, 0, $width, $height);
      imagettftext($watermark_image, $watermark_font_size, 0, $left, $top, $watermark_color, $watermark_font, $watermark_text);
      imagealphablending($watermark_image, FALSE);
      imagesavealpha($watermark_image, TRUE);
      imagegif($watermark_image, $dest_filename);
      imagedestroy($image);
    }
    imagedestroy($watermark_image);
  }

  function set_image_watermark ($original_filename, $dest_filename, $watermark_url, $watermark_height, $watermark_width, $watermark_position) {
    list($width, $height, $type) = getimagesize($original_filename);
    list($width_watermark, $height_watermark, $type_watermark) = getimagesize($watermark_url);

    $watermark_width = $width * $watermark_width / 100;
    $watermark_height = $height_watermark * $watermark_width / $width_watermark;
        
    $watermark_position = explode('-', $watermark_position);
    $top = $height - $watermark_height - 5;
    $left = $width - $watermark_width - 5;
    switch ($watermark_position[0]) {
      case 'top':
        $top = 5;
        break;
      case 'middle':
        $top = ($height - $watermark_height) / 2;
        break;
    }
    switch ($watermark_position[1]) {
      case 'left':
        $left = 5;
        break;
      case 'center':
        $left = ($width - $watermark_width) / 2;
        break;
    }
    
    if ($type_watermark == 2) {
      $watermark_image = imagecreatefromjpeg($watermark_url);        
    }
    elseif ($type_watermark == 3) {
      $watermark_image = imagecreatefrompng($watermark_url);
    }
    elseif ($type_watermark == 1) {
      $watermark_image = imagecreatefromgif($watermark_url);      
    }
    else {
      return false;
    }
    
    $watermark_image_resized = imagecreatetruecolor($watermark_width, $watermark_height);
    imagecolorallocatealpha($watermark_image_resized, 255, 255, 255, 127);
    imagealphablending($watermark_image_resized, FALSE);
    imagesavealpha($watermark_image_resized, TRUE);
    imagecopyresampled ($watermark_image_resized, $watermark_image, 0, 0, 0, 0, $watermark_width, $watermark_height, $width_watermark, $height_watermark);
        
    if ($type == 2) {
      $image = imagecreatefromjpeg($original_filename);
      imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      if ($dest_filename <> '') {
        imagejpeg ($image, $dest_filename, 100); 
      } else {
        header('Content-Type: image/jpeg');
        imagejpeg($image, null, 100);
      };
      imagedestroy($image);  
    }
    elseif ($type == 3) {
      $image = imagecreatefrompng($original_filename);
      imagecopy($image, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $dest_filename, 9);
      imagedestroy($image);
    }
    elseif ($type == 1) {
      $image = imagecreatefromgif($original_filename);
      $tempimage = imagecreatetruecolor($width, $height);
      imagecopy($tempimage, $image, 0, 0, 0, 0, $width, $height);
      imagecopy($tempimage, $watermark_image_resized, $left, $top, 0, 0, $watermark_width, $watermark_height);
      imagegif($tempimage, $dest_filename);
      imagedestroy($image);
      imagedestroy($tempimage);
    }
    imagedestroy($watermark_image);
  }

  public function save_image_db() {
    global $wpdb;
    $gal_id = (isset($_POST['current_id']) ? (int) $_POST['current_id'] : 0);
    $image_ids = (isset($_POST['ids_string']) ? esc_html(stripslashes($_POST['ids_string'])) : '');
    $image_id_array = explode(',', $image_ids);
    foreach ($image_id_array as $image_id) {
      if ($image_id) {
        $filename = ((isset($_POST['input_filename_' . $image_id])) ? esc_html(stripslashes($_POST['input_filename_' . $image_id])) : '');
        $image_url = ((isset($_POST['image_url_' . $image_id])) ? esc_html(stripslashes($_POST['image_url_' . $image_id])) : '');
        $thumb_url = ((isset($_POST['thumb_url_' . $image_id])) ? esc_html(stripslashes($_POST['thumb_url_' . $image_id])) : '');
        $description = ((isset($_POST['image_description_' . $image_id])) ? esc_html((stripslashes($_POST['image_description_' . $image_id]))) : '');
        $alt = ((isset($_POST['image_alt_text_' . $image_id])) ? esc_html(stripslashes($_POST['image_alt_text_' . $image_id])) : '');
        $date = ((isset($_POST['input_date_modified_' . $image_id])) ? esc_html(stripslashes($_POST['input_date_modified_' . $image_id])) : '');
        $size = ((isset($_POST['input_size_' . $image_id])) ? esc_html(stripslashes($_POST['input_size_' . $image_id])) : '');
        $filetype = ((isset($_POST['input_filetype_' . $image_id])) ? esc_html(stripslashes($_POST['input_filetype_' . $image_id])) : '');
        $resolution = ((isset($_POST['input_resolution_' . $image_id])) ? esc_html(stripslashes($_POST['input_resolution_' . $image_id])) : '');
        $order = ((isset($_POST['order_input_' . $image_id])) ? esc_html(stripslashes($_POST['order_input_' . $image_id])) : '');
        $author = get_current_user_id();
        $tags_ids = ((isset($_POST['tags_' . $image_id])) ? esc_html(stripslashes($_POST['tags_' . $image_id])) : '');
        if (strpos($image_id, 'pr_') !== FALSE) {
          $save = $wpdb->insert($wpdb->prefix . 'bwg_image', array(
            'gallery_id' => $gal_id,
            'slug' => $alt,
            'filename' => $filename,
            'image_url' => $image_url,
            'thumb_url' => $thumb_url,
            'description' => $description,
            'alt' => $alt,
            'date' => $date,
            'size' => $size,
            'filetype' => $filetype,
            'resolution' => $resolution,
            'author' => $author,
            'order' => $order,
            'published' => 1,
            'comment_count' => 0,
          ), array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%d',
          ));
          $image_id = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'bwg_image');
          $_POST['check_' . $image_id] = 'on';
          if (isset($_POST['image_current_id']) && strpos(esc_html($_POST['image_current_id']), 'pr_') !== FALSE) {
            $_POST['image_current_id'] = $image_id;
          }
        }
        else {
          $save = $wpdb->update($wpdb->prefix . 'bwg_image', array(
            'gallery_id' => $gal_id,
            'slug' => $alt,
            'filename' => $filename,
            'image_url' => $image_url,
            'thumb_url' => $thumb_url,
            'description' => $description,
            'alt' => $alt,
            'date' => $date,
            'size' => $size,
            'filetype' => $filetype,
            'resolution' => $resolution,
            'author' => $author,
            'order' => $order), array('id' => $image_id));
        }
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d" AND gallery_id="%d"', $image_id, $gal_id));
        if ($save !== FALSE) {
          $tag_id_array = explode(',', $tags_ids);
          foreach ($tag_id_array as $tag_id) {
            if ($tag_id) {
              if (strpos($tag_id, 'pr_') !== FALSE) {
                $tag_id = substr($tag_id, 3);
              }
              $save = $wpdb->insert($wpdb->prefix . 'bwg_image_tag', array(
                'tag_id' => $tag_id,
                'image_id' => $image_id,
                'gallery_id' => $gal_id,
              ), array(
                '%d',
                '%d',
                '%d',
              ));
              // Increase tag count in term_taxonomy table.
              $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
            }
          }
        }
      }
    }
  }

  public function save() {
    $this->save_db();
    global $wpdb;
    if (!isset($_POST['current_id']) || (esc_html(stripslashes($_POST['current_id'])) == 0) || (esc_html(stripslashes($_POST['current_id'])) == '')) {
      $_POST['current_id'] = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'bwg_gallery');
    }
    $this->save_image_db();
    $this->display();
  }

  public function apply() {
    $this->save_db();
    global $wpdb;
    if (!isset($_POST['current_id']) || (esc_html(stripslashes($_POST['current_id'])) == 0) || (esc_html(stripslashes($_POST['current_id'])) == '')) {
      $_POST['current_id'] = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'bwg_gallery');
    }
    $this->save_image_db();
    $this->edit();
  }

  public function delete_unknown_images() {
    global $wpdb;
    $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'bwg_image WHERE gallery_id=0');
  }

  // Return random image from gallery for gallery preview.
  public function get_image_for_gallery($gallery_id) {
    global $wpdb;
    $preview_image = $wpdb->get_var($wpdb->prepare("SELECT thumb_url FROM " . $wpdb->prefix . "bwg_image WHERE gallery_id='%d' ORDER BY rand() limit 1", $gallery_id));
    return $preview_image;
  }
  
  public function bwg_get_unique_slug($slug, $id) {
    global $wpdb;
    $slug = sanitize_title($slug);
    if ($id != 0) {
      $query = $wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s AND id != %d", $slug, $id);
    }
    else {
      $query = $wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s", $slug);
    }
    if ($wpdb->get_var($query)) {
      $num = 2;
      do {
        $alt_slug = $slug . "-$num";
        $num++;
        $slug_check = $wpdb->get_var($wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s", $alt_slug));
      } while ($slug_check);
      $slug = $alt_slug;
    }
    return $slug;
  }
  
  public function bwg_get_unique_name($name, $id) {
    global $wpdb;
    if ($id != 0) {
      $query = $wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s AND id != %d", $name, $id);
    }
    else {
      $query = $wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s", $name);
    }
    if ($wpdb->get_var($query)) {
      $num = 2;
      do {
        $alt_name = $name . "-$num";
        $num++;
        $slug_check = $wpdb->get_var($wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s", $alt_name));
      } while ($slug_check);
      $name = $alt_name;
    }
    return $name;
  }
  
  public function save_db() {
    global $wpdb;
    $id = (isset($_POST['current_id']) ? (int) $_POST['current_id'] : 0);
    $name = ((isset($_POST['name']) && esc_html(stripslashes($_POST['name'])) != '') ? esc_html(stripslashes($_POST['name'])) : 'Gallery');
    $name = $this->bwg_get_unique_name($name, $id);
    $slug = ((isset($_POST['slug']) && esc_html(stripslashes($_POST['slug'])) != '') ? esc_html(stripslashes($_POST['slug'])) : $name);
    $slug = $this->bwg_get_unique_slug($slug, $id);
    $description = (isset($_POST['description']) ? stripslashes($_POST['description']) : '');
    $preview_image = (isset($_POST['preview_image']) ? esc_html(stripslashes($_POST['preview_image'])) : '');
    $random_preview_image = (($preview_image == '') ? $this->get_image_for_gallery($id) : '');
    $published = (isset($_POST['published']) ? (int) $_POST['published'] : 1);
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'bwg_gallery', array(
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'preview_image' => $preview_image,
        'random_preview_image' => $random_preview_image,
        'author' => get_current_user_id(),
        'published' => $published), array('id' => $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'bwg_gallery', array(
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'preview_image' => $preview_image,
        'random_preview_image' => $random_preview_image,
        'order' => ((int) $wpdb->get_var('SELECT MAX(`order`) FROM ' . $wpdb->prefix . 'bwg_gallery')) + 1,
        'author' => get_current_user_id(),
        'published' => $published,
      ), array(
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
      ));
    }
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Saved.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
  }

  public function save_order($flag = TRUE) {
    global $wpdb;
    $gallery_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_gallery');
    if ($gallery_ids_col) {
      foreach ($gallery_ids_col as $gallery_id) {
        if (isset($_POST['order_input_' . $gallery_id])) {
          $order_values[$gallery_id] = (int) $_POST['order_input_' . $gallery_id];
        }
        else {
          $order_values[$gallery_id] = (int) $wpdb->get_var($wpdb->prepare('SELECT `order` FROM ' . $wpdb->prefix . 'bwg_gallery WHERE `id`="%d"', $gallery_id));
        }
      }
      asort($order_values);
      $i = 1;
      foreach ($order_values as $key => $order_value) {
        $wpdb->update($wpdb->prefix . 'bwg_gallery', array('order' => $i), array('id' => $key));
        $i++;
      }
      if ($flag) {
        echo WDWLibrary::message('Ordering Succesfully Saved.', 'updated');
      }
    }
    $this->display();
  }

  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_gallery WHERE id="%d"', $id);
    $query_image = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image WHERE gallery_id="%d"', $id);
    $query_album_gallery = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_album_gallery WHERE alb_gal_id="%d" AND is_album="%d"', $id, 0);
    if ($wpdb->query($query)) {
      $wpdb->query($query_image);
      $wpdb->query($query_album_gallery);
      echo WDWLibrary::message('Item Succesfully Deleted.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_gallery');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $flag = TRUE;
        $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_gallery WHERE id="%d"', $gal_id);
        $wpdb->query($query);
        $query_image = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image WHERE gallery_id="%d"', $gal_id);
        $wpdb->query($query_image);
        $query_album_gallery = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_album_gallery WHERE alb_gal_id="%d" AND is_album="%d"', $gal_id, 0);
        $wpdb->query($query_album_gallery);
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Deleted.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }

  public function publish($id) {
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'bwg_gallery', array('published' => 1), array('id' => $id));
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Published.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function publish_all() {
    global $wpdb;
    $flag = FALSE;
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_gallery');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $flag = TRUE;
        $wpdb->update($wpdb->prefix . 'bwg_gallery', array('published' => 1), array('id' => $gal_id));
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Published.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }

  public function unpublish($id) {
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'bwg_gallery', array('published' => 0), array('id' => $id));
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Unpublished.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function unpublish_all() {
    global $wpdb;
    $flag = FALSE;
    $gal_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_gallery');
    foreach ($gal_ids_col as $gal_id) {
      if (isset($_POST['check_' . $gal_id])) {
        $flag = TRUE;
        $wpdb->update($wpdb->prefix . 'bwg_gallery', array('published' => 0), array('id' => $gal_id));
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Unpublished.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }

  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}