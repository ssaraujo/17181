<?php
wp_enqueue_script('gbgallery_groups_ajax_core',array( 'jquery' ));
wp_enqueue_script('jquery-ui-sortable',array( 'jquery' ));
wp_localize_script('gbgallery_groups_ajax_core', 'gb_vars', array(
    'gb_nonce' => wp_create_nonce('gb_nonce')
));
wp_enqueue_style( 'gbgallery-style' );
global $wpdb, $gb_group_table, $GBgallery_add_settings,$gb_gallery_donate_option;
if(! isset($gb_gallery_donate_option))
    gb_global_set();
$donate_option = get_option( $gb_gallery_donate_option );
$group_list = $wpdb->get_results( "SELECT * FROM $gb_group_table ");
?>
<div class="wrap gb_gallery_admin_group">
    <header>
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
        <h2><?php echo __("GB Gallery Group - Admin") ?></h2>
        <p class="small_title"><?php echo __('Select a group to modify')?></p>
    </header>
    <section>
        <header>
            <form id="groups-form" action="" method="POST">
                <select name="groups" id="groups">
                    <?php
                    foreach($group_list as $i => $group){
                        echo "<option value='".$group->id."'>".$group->groups."</option>";
                    }
                    ?>
                </select>
                <div id="group_submit_con" class="group_submit_con">
                    <input type="submit" name="groups-form-submit" id="groups-form-submit" class="button-primary" value="<?php echo __('Show Group') ?>">
                    <div class='group_withing'></div>
                </div>
                <div class="group_submit_con">
                    <input type="button" id="add_group" class="button-primary gb_button-primary" value="<?php echo __('Add a group') ?>">
                </div>
                <div id="group_delete_con" class="group_submit_con">
                    <input type="button" id="delete_group" class="button-primary gb_button-secondary" value="<?php echo __('Delete a group') ?>">
                    <div class='group_withing'></div>
                </div>
            </form>
        </header>
        <section class="group_posts_con">
            <div id="gb_group_message"></div>
            <div id="group_posts"></div>
            <input type='hidden' name='to_submit' id='to_submit'>
            <div id="gb_group_add"></div>
        </section>
    </section>
    <footer>
        <div class="gb_helper">
            <div class="gb_note"><ul><li></li></ul></div>
            <div class="group_add_con">
                <form id="group_add-form" action="" method="POST">
                    <div id="group_add_items-main" class="group_add_items_con">
                        <p class="gb_add_group_desc"><?php echo __('Only letters, numbers, spaces,<br> underscores and dashes are allowed.')?></p>
                        <div class="group_add_item_con">
                            <?php echo __("Group Name: ")?><input name="group_add[]" id="group_add-1" type="text" class="group_add_the_group">
                        </div>
                    </div>
                    <div class="group_submit_con">
                        <input type="submit" name="group_add-submit" id="group_add-submit" class="button-primary gb_button-primary" value="<?php echo __('Save') ?>">
                        <div class='group_withing'></div>
                    </div>
                </form>
            </div>
        </div>
    </footer>
</div>