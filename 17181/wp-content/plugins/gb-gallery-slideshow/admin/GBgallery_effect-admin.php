<?php
wp_enqueue_script('gbgallery_effect_ajax_core',array( 'jquery' ));
wp_enqueue_script('jquery-ui-core',array( 'jquery' ));
wp_localize_script('gbgallery_effect_ajax_core', 'gb_vars', array(
    'gb_nonce' => wp_create_nonce('gb_nonce')
));
wp_enqueue_style( 'gbgallery-style' );
global $wpdb, $gb_group_table, $GBgallery_add_settings,$gb_gallery_donate_option;
if(! isset($gb_gallery_donate_option))
    gb_global_set();
$donate_option = get_option( $gb_gallery_donate_option );
$group_list = $wpdb->get_results( "SELECT * FROM $gb_group_table ");
?>
<div class="wrap gb_gallery_admin_effect">
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
        <h2>GB Gallery Special Effect - Admin</h2>
        <div class="steps">
            <p class="step1"><b>Step 1:</b> select the group with the image you want to modify</p>
            <p class="step2"><b>Step 2:</b> click on the effect area 1 to 100 and save</p>
        </div>
    </header>
    <section class="gb_effect_admin">
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
                    <div class="group_withing"></div>
                </div>
            </form>
        </header>
        <section>
            <div id="bg_group_message"></div>
            <div id="group_posts"></div>
            <div id="gb_image_effect"></div>
        </section>
    </section>
    <footer>
        <div class="gb_helper">
            <input type="hidden" id="gb_effect_index">
            <div class="GB_gallery_helper">
            <div class="GB_gallery_helper-0"></div>
            <div class="GB_gallery_helper-1"></div>
            <div class="GB_gallery_helper-2"></div>
            <div class="GB_gallery_helper-3"></div>
            <div class="GB_gallery_helper-4"></div>
            <div class="GB_gallery_helper-5"></div>
            <div class="GB_gallery_helper-6"></div>
            <div class="GB_gallery_helper-7"></div>
            <div class="GB_gallery_helper-8"></div>
            <div class="GB_gallery_helper-9"></div>

            <div class="GB_gallery_helper-10"></div>
            <div class="GB_gallery_helper-11"></div>
            <div class="GB_gallery_helper-12"></div>
            <div class="GB_gallery_helper-13"></div>
            <div class="GB_gallery_helper-14"></div>
            <div class="GB_gallery_helper-15"></div>
            <div class="GB_gallery_helper-16"></div>
            <div class="GB_gallery_helper-17"></div>
            <div class="GB_gallery_helper-18"></div>
            <div class="GB_gallery_helper-19"></div>

            <div class="GB_gallery_helper-20"></div>
            <div class="GB_gallery_helper-21"></div>
            <div class="GB_gallery_helper-22"></div>
            <div class="GB_gallery_helper-23"></div>
            <div class="GB_gallery_helper-24"></div>
            <div class="GB_gallery_helper-25"></div>
            <div class="GB_gallery_helper-26"></div>
            <div class="GB_gallery_helper-27"></div>
            <div class="GB_gallery_helper-28"></div>
            <div class="GB_gallery_helper-29"></div>

            <div class="GB_gallery_helper-30"></div>
            <div class="GB_gallery_helper-31"></div>
            <div class="GB_gallery_helper-32"></div>
            <div class="GB_gallery_helper-33"></div>
            <div class="GB_gallery_helper-34"></div>
            <div class="GB_gallery_helper-35"></div>
            <div class="GB_gallery_helper-36"></div>
            <div class="GB_gallery_helper-37"></div>
            <div class="GB_gallery_helper-38"></div>
            <div class="GB_gallery_helper-39"></div>

            <div class="GB_gallery_helper-40"></div>
            <div class="GB_gallery_helper-41"></div>
            <div class="GB_gallery_helper-42"></div>
            <div class="GB_gallery_helper-43"></div>
            <div class="GB_gallery_helper-44"></div>
            <div class="GB_gallery_helper-45"></div>
            <div class="GB_gallery_helper-46"></div>
            <div class="GB_gallery_helper-47"></div>
            <div class="GB_gallery_helper-48"></div>
            <div class="GB_gallery_helper-49"></div>

            <div class="GB_gallery_helper-50"></div>
            <div class="GB_gallery_helper-51"></div>
            <div class="GB_gallery_helper-52"></div>
            <div class="GB_gallery_helper-53"></div>
            <div class="GB_gallery_helper-54"></div>
            <div class="GB_gallery_helper-55"></div>
            <div class="GB_gallery_helper-56"></div>
            <div class="GB_gallery_helper-57"></div>
            <div class="GB_gallery_helper-58"></div>
            <div class="GB_gallery_helper-59"></div>

            <div class="GB_gallery_helper-60"></div>
            <div class="GB_gallery_helper-61"></div>
            <div class="GB_gallery_helper-62"></div>
            <div class="GB_gallery_helper-63"></div>
            <div class="GB_gallery_helper-64"></div>
            <div class="GB_gallery_helper-65"></div>
            <div class="GB_gallery_helper-66"></div>
            <div class="GB_gallery_helper-67"></div>
            <div class="GB_gallery_helper-68"></div>
            <div class="GB_gallery_helper-69"></div>

            <div class="GB_gallery_helper-70"></div>
            <div class="GB_gallery_helper-71"></div>
            <div class="GB_gallery_helper-72"></div>
            <div class="GB_gallery_helper-73"></div>
            <div class="GB_gallery_helper-74"></div>
            <div class="GB_gallery_helper-75"></div>
            <div class="GB_gallery_helper-76"></div>
            <div class="GB_gallery_helper-77"></div>
            <div class="GB_gallery_helper-78"></div>
            <div class="GB_gallery_helper-79"></div>

            <div class="GB_gallery_helper-80"></div>
            <div class="GB_gallery_helper-81"></div>
            <div class="GB_gallery_helper-82"></div>
            <div class="GB_gallery_helper-83"></div>
            <div class="GB_gallery_helper-84"></div>
            <div class="GB_gallery_helper-85"></div>
            <div class="GB_gallery_helper-86"></div>
            <div class="GB_gallery_helper-87"></div>
            <div class="GB_gallery_helper-88"></div>
            <div class="GB_gallery_helper-89"></div>

            <div class="GB_gallery_helper-90"></div>
            <div class="GB_gallery_helper-91"></div>
            <div class="GB_gallery_helper-92"></div>
            <div class="GB_gallery_helper-93"></div>
            <div class="GB_gallery_helper-94"></div>
            <div class="GB_gallery_helper-95"></div>
            <div class="GB_gallery_helper-96"></div>
            <div class="GB_gallery_helper-97"></div>
            <div class="GB_gallery_helper-98"></div>
            <div class="GB_gallery_helper-99"></div>
        </div>
        </div>
    </footer>
</div>