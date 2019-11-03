<?php
global $GB_this_name, $gb_gallery_option, $gb_gallery_help_option, $gb_gallery_donate_option;
wp_enqueue_style( 'gbgallery-style' );
if(! isset($gb_gallery_option))
    gb_global_set();
$option = get_option( $gb_gallery_option );
$help_option = get_option( $gb_gallery_help_option );
$donate_option = get_option( $gb_gallery_donate_option );
?>
<div class="wrap gb_gallery_admin">
    <header>
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
        <h2>GB Gallery - Admin</h2>
        <p>Hear you can manege your settings</p>
    </header>
    <section>
        <section class="gb_settings">
            <div class="gb_delete_option">
                <input type="checkbox" name="gb_save_option" id="gb_save_option" <?php echo $option == '1' ? 'checked' : '' ?>>
                <p>Please save my GB Gallery Slideshow data when deleting the plugin</p>
                <label class="gb_option_update">Your GB Gallery Slideshow data wil be: <span><?php echo $option == '1' ? 'Saved' : 'Not saved' ?></span></label>
            </div>
            <div class="gb_help_option">
                <input type="checkbox" name="gb_help_option" id="gb_help_option" <?php echo $help_option == '1' ? 'checked' : '' ?>>
                <p>Add me to favorite links - <a href="http://gb-plugins.com/" target="_blank">more info</a></p>
            </div>
            <div class="gb_donate_option">
                <input type="checkbox" name="gb_donate_option" id="gb_donate_option" <?php echo $donate_option == '1' ? 'checked' : '' ?>>
                <div class="gb_donate_p">I like using this plugin and I have made a donation. -
                    <section class="gb_more_q">
                        more info
                        <div class="gb_more_info gb_box_shadow">All donations will be used to further develop and upgrade this free plugin.</div>
                    </section>
                </div>
            </div>
        </section>
        <p class="admin_samll_title">The following links can be used to manage the groups and special effects of GB Gallery Slideshow:</p>
        <ul>
            <li><a href="admin.php?page=<?php echo $GB_this_name ?>/admin/GBgallery_group-admin.php">GB Gallery Group - Admin</a> - Manage the groups</li>
            <li><a href="admin.php?page=<?php echo $GB_this_name ?>/admin/GBgallery_effect-admin.php">GB Gallery Special Effect - Admin</a> - Manage the image special effect</li>
            <li><a href="admin.php?page=<?php echo $GB_this_name ?>/admin/GBgallery_shortcode.php">GB Gallery ShortCode - Generator</a> - Add the ShortCode at any location in the site (posts, page...)</li>
        </ul>
    </section>
    <footer>

    </footer>
</div>