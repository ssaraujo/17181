<?php

class BWGViewThumbnails {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display($params, $from_shortcode = 0, $bwg = 0) {
    global $wp;
    $current_url = $wp->query_string;
    global $WD_BWG_UPLOAD_DIR;
    require_once(WD_BWG_DIR . '/framework/WDWLibrary.php');

    if (!isset($params['image_title'])) {
      $params['image_title'] = 'none';
    }
    $from = (isset($params['from']) ? esc_html($params['from']) : 0);
    if ($from) {
      $options_row = $this->model->get_options_row_data();
      $params['gallery_id'] = $params['id'];
      $params['images_per_page'] = $params['count'];
      $params['sort_by'] = (($params['show'] == 'random') ? 'RAND()' : 'date');
      $params['image_enable_page'] = 0;
      $params['image_title'] = $options_row->image_title_show_hover;
      $params['thumb_height'] = $params['height'];
      $params['thumb_width'] = $params['width'];
      $params['image_column_number'] = $params['count'];
      $params['popup_width'] = $options_row->popup_width;
      $params['popup_height'] = $options_row->popup_height;
      $params['popup_effect'] = $options_row->popup_type;
      $params['popup_enable_filmstrip'] = $options_row->popup_enable_filmstrip;
      $params['popup_filmstrip_height'] = $options_row->popup_filmstrip_height;
      $params['popup_enable_ctrl_btn'] = $options_row->popup_enable_ctrl_btn;
      $params['popup_enable_fullscreen'] = $options_row->popup_enable_fullscreen;
      $params['popup_interval'] = $options_row->popup_interval;
      $params['popup_enable_comment'] = $options_row->popup_enable_comment;
      $params['popup_enable_facebook'] = $options_row->popup_enable_facebook;
      $params['popup_enable_twitter'] = $options_row->popup_enable_twitter;
      $params['popup_enable_google'] = $options_row->popup_enable_google;
      $params['watermark_type'] = $options_row->watermark_type;
      $params['watermark_link'] = $options_row->watermark_link;
      $params['watermark_opacity'] = $options_row->watermark_opacity;
      $params['watermark_position'] = $options_row->watermark_position;
      $params['watermark_text'] = $options_row->watermark_text;
      $params['watermark_font_size'] = $options_row->watermark_font_size;
      $params['watermark_font'] = $options_row->watermark_font;
      $params['watermark_color'] = $options_row->watermark_color;
      $params['watermark_url'] = $options_row->watermark_url;
      $params['watermark_width'] = $options_row->watermark_width;
      $params['watermark_height'] = $options_row->watermark_height;
    }
    $theme_row = $this->model->get_theme_row_data($params['theme_id']);
    if (!$theme_row) {
      echo WDWLibrary::message(__('There is no theme selected or the theme was deleted.', 'bwg'), 'error');
      return;
    }
    if (isset($params['type'])) {
      $type = $params['type'];
    }
    else {
      $type = "";
    }
    $gallery_row = $this->model->get_gallery_row_data($params['gallery_id']);
    if (!$gallery_row && ($type == '')) {
      echo WDWLibrary::message(__('There is no gallery selected or the gallery was deleted.', 'bwg'), 'error');
      return;
    }
    $image_rows = $this->model->get_image_rows_data($params['gallery_id'], $params['images_per_page'], $params['sort_by'], $bwg, $type);
    if (!$image_rows) {
      echo WDWLibrary::message(__('There are no images in this gallery.', 'bwg'), 'error');
    }
    if ($params['image_enable_page'] && $params['images_per_page']) {
      $page_nav = $this->model->page_nav($params['gallery_id'], $params['images_per_page'], $bwg, $type);
    }
    $rgb_page_nav_font_color = WDWLibrary::spider_hex2rgb($theme_row->page_nav_font_color);
    $rgb_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->thumbs_bg_color);
    ?>
    <style>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_span1_<?php echo $bwg; ?> {
        -moz-box-sizing: content-box;
        box-sizing: content-box;
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        border: <?php echo $theme_row->thumb_border_width; ?>px <?php echo $theme_row->thumb_border_style; ?> #<?php echo $theme_row->thumb_border_color; ?>;
        border-radius: <?php echo $theme_row->thumb_border_radius; ?>;
        box-shadow: <?php echo $theme_row->thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        margin: <?php echo $theme_row->thumb_margin; ?>px;
        padding: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: <?php echo $theme_row->thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->thumb_transparent; ?>);
        text-align: center;
        vertical-align: middle;
        <?php echo ($theme_row->thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        width: <?php echo $params['thumb_width']; ?>px;
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_span1_<?php echo $bwg; ?>:hover {
        -ms-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        z-index: 102;
        position: relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_span2_<?php echo $bwg; ?> {
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['thumb_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> {
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->thumb_bg_transparent / 100; ?>);
        display: inline-block;
        font-size: 0;
        max-width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * (2 + $theme_row->thumb_margin + $theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>px;
        text-align: <?php echo $theme_row->thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      <?php
      if ($params['image_title'] == 'show') { // Show image title at the bottom.
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_span1_<?php echo $bwg; ?> {
          display: block;
          margin: 0 auto;
          opacity: 1;
          filter: Alpha(opacity=100);
          text-align: center;
          width: <?php echo $params['thumb_width']; ?>px;
        }
        <?php
      }
      elseif ($params['image_title'] == 'hover') { // Show image title on hover.
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_span1_<?php echo $bwg; ?> {
          display: table;
          height: inherit;
          left: -3000px;
          opacity: 0;
          filter: Alpha(opacity=0);
          position: absolute;
          top: 0px;
          width: inherit;
        }
        <?php
      }
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_span1_<?php echo $bwg; ?>:hover .bwg_title_span1_<?php echo $bwg; ?> {
        left: <?php echo $theme_row->thumb_padding; ?>px;
        top: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: 1;
        filter: Alpha(opacity=100);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_span2_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->thumb_title_font_color; ?>;
        display: table-cell;
        font-family: <?php echo $theme_row->thumb_title_font_style; ?>;
        font-size: <?php echo $theme_row->thumb_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->thumb_title_font_weight; ?>;
        height: inherit;
        margin: <?php echo $theme_row->thumb_title_margin; ?>;
        text-shadow: <?php echo $theme_row->thumb_title_shadow; ?>;
        vertical-align: middle;
        width: inherit;
        word-break: break-all;
        word-wrap: break-word;
      }

      /*pagination styles*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> {
        text-align: <?php echo $theme_row->page_nav_align; ?>;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin: 6px 0 4px;
        display: block;
        height: 30px;
        line-height: 30px;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
          display: none;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin-right: 10px;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .paging-input_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:focus {
        cursor: default;
        color: rgba(<?php echo $rgb_page_nav_font_color['red']; ?>, <?php echo $rgb_page_nav_font_color['green']; ?>, <?php echo $rgb_page_nav_font_color['blue']; ?>, 0.5);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
        cursor: pointer;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->page_nav_padding; ?>;
        margin: <?php echo $theme_row->page_nav_margin; ?>;
        border-radius: <?php echo $theme_row->page_nav_border_radius; ?>;
        border-style: <?php echo $theme_row->page_nav_border_style; ?>;
        border-width: <?php echo $theme_row->page_nav_border_width; ?>px;
        border-color: #<?php echo $theme_row->page_nav_border_color; ?>;
        background-color: #<?php echo $theme_row->page_nav_button_bg_color; ?>;
        opacity: <?php echo $theme_row->page_nav_button_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->page_nav_button_bg_transparent; ?>);
        box-shadow: <?php echo $theme_row->page_nav_box_shadow; ?>;
        <?php echo ($theme_row->page_nav_button_transition ) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
    </style>

    <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <form id="gal_front_form_<?php echo $bwg; ?>" method="post" action="#">
          <div style="background-color:rgba(0, 0, 0, 0); text-align:center; width:100%;">
            <?php
            if ($params['image_enable_page']  && $params['images_per_page'] && ($theme_row->page_nav_position == 'top')) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $params['images_per_page'], $bwg, 'bwg_standart_thumbnails_' . $bwg);
            }
            ?>
            <div id="bwg_standart_thumbnails_<?php echo $bwg; ?>" class="bwg_standart_thumbnails_<?php echo $bwg; ?>">
              <div id="ajax_loading_<?php echo $bwg; ?>" style="position:absolute;">
                <div id="opacity_div_<?php echo $bwg; ?>" style="display:none; background-color:#FFFFFF; opacity:0.7; filter:Alpha(opacity=70); position:absolute; z-index:105;"></div>
                <span id="loading_div_<?php echo $bwg; ?>" style="display:none; text-align:center; position:relative; vertical-align:middle; z-index:107">
                  <img src="<?php echo WD_BWG_URL . '/images/ajax_loader.png'; ?>" class="spider_ajax_loading" style="float: none; width:50px;">
                </span>
              </div>
              <?php
              foreach ($image_rows as $image_row) {
                $params_array = array(
                  'tag_id' => (isset($params['type']) ? $params['gallery_id'] : 0),
                  'action' => 'GalleryBox',
                  'current_view' => $bwg,
                  'image_id' => (isset($_POST['image_id']) ? esc_html($_POST['image_id']) : $image_row->id),
                  'gallery_id' => $params['gallery_id'],
                  'theme_id' => $params['theme_id'],
                  'thumb_width' => $params['thumb_width'],
                  'thumb_height' => $params['thumb_height'],
                  'image_width' => $params['popup_width'],
                  'image_height' => $params['popup_height'],
                  'image_effect' => $params['popup_effect'],
                  'sort_by' => (isset($params['type']) ? 'date' : (($params['sort_by'] == 'RAND()') ? 'order' : $params['sort_by'])),
                  'enable_image_filmstrip' => $params['popup_enable_filmstrip'],
                  'image_filmstrip_height' => $params['popup_filmstrip_height'],
                  'enable_image_ctrl_btn' => $params['popup_enable_ctrl_btn'],
                  'enable_image_fullscreen' => $params['popup_enable_fullscreen'],
                  'slideshow_interval' => $params['popup_interval'],
                  'enable_comment_social' => $params['popup_enable_comment'],
                  'enable_image_facebook' => $params['popup_enable_facebook'],
                  'enable_image_twitter' => $params['popup_enable_twitter'],
                  'enable_image_google' => $params['popup_enable_google'],
                  'watermark_type' => $params['watermark_type'],
                  'current_url' => $current_url
                );
                if ($params['watermark_type'] != 'none') {
                  $params_array['watermark_link'] = $params['watermark_link'];
                  $params_array['watermark_opacity'] = $params['watermark_opacity'];
                  $params_array['watermark_position'] = $params['watermark_position'];
                }
                if ($params['watermark_type'] == 'text') {
                  $params_array['watermark_text'] = $params['watermark_text'];
                  $params_array['watermark_font_size'] = $params['watermark_font_size'];
                  $params_array['watermark_font'] = $params['watermark_font'];
                  $params_array['watermark_color'] = $params['watermark_color'];
                }
                elseif ($params['watermark_type'] == 'image') {
                  $params_array['watermark_url'] = $params['watermark_url'];
                  $params_array['watermark_width'] = $params['watermark_width'];
                  $params_array['watermark_height'] = $params['watermark_height'];
                }
                list($image_thumb_width, $image_thumb_height) = getimagesize(ABSPATH . $WD_BWG_UPLOAD_DIR . $image_row->thumb_url);
                $scale = max($params['thumb_width'] / $image_thumb_width, $params['thumb_height'] / $image_thumb_height);
                $image_thumb_width *= $scale;
                $image_thumb_height *= $scale;
                $thumb_left = ($params['thumb_width'] - $image_thumb_width) / 2;
                $thumb_top = ($params['thumb_height'] - $image_thumb_height) / 2;
                ?>
                <a style="font-size: 0;" href="javascript:spider_createpopup('<?php echo addslashes(add_query_arg($params_array, admin_url('admin-ajax.php'))); ?>', '<?php echo $bwg; ?>', '<?php echo $params['popup_width']; ?>', '<?php echo $params['popup_height']; ?>', 1, 'testpopup', 5);">
                  <span class="bwg_standart_thumb_<?php echo $bwg; ?>">
                    <span class="bwg_standart_thumb_span1_<?php echo $bwg; ?>">
                      <span class="bwg_standart_thumb_span2_<?php echo $bwg; ?>">
                        <img class="bwg_standart_thumb_img_<?php echo $bwg; ?>" style="max-height: none !important;  max-width: none !important; padding: 0 !important; width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;"
                             id="<?php echo $image_row->id; ?>"
                             src="<?php echo site_url() . '/' . $WD_BWG_UPLOAD_DIR . $image_row->thumb_url; ?>"
                             alt="<?php echo $image_row->alt; ?>"
                             title="<?php echo $image_row->alt; ?>" />
                        <?php
                        if ($params['image_title'] == 'hover') {
                          ?>
                          <span class="bwg_title_span1_<?php echo $bwg; ?>">
                            <span class="bwg_title_span2_<?php echo $bwg; ?>">
                              <?php echo $image_row->alt; ?>
                            </span>
                          </span>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                    <?php
                    if ($params['image_title'] == 'show') {
                      ?>
                      <span class="bwg_title_span1_<?php echo $bwg; ?>">
                        <span class="bwg_title_span2_<?php echo $bwg; ?>">
                          <?php echo $image_row->alt; ?>
                        </span>
                      </span>
                      <?php
                    }
                    ?>
                  </span>
                </a>
                <?php
              }
              ?>
            </div>
            <?php
            if ($params['image_enable_page']  && $params['images_per_page'] && ($theme_row->page_nav_position == 'bottom')) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $params['images_per_page'], $bwg, 'bwg_standart_thumbnails_' . $bwg);
            }
            ?>
          </div>
        </form>
        <div id="spider_popup_loading_<?php echo $bwg; ?>" class="spider_popup_loading"></div>
        <div id="spider_popup_overlay_<?php echo $bwg; ?>" class="spider_popup_overlay" onclick="spider_destroypopup(1000)"></div>
      </div>
    </div>
    <?php
    if ($from_shortcode) {
      return;
    }
    else {
      die();
    }
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