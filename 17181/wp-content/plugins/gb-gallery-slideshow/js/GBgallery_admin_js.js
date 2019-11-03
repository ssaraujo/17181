jQuery(document).ready(function(){
    jQuery('#gb_the_img_btn').on('click',function(){uploadImg(this);});
    jQuery(document).on('click','.validation',function(){check_fields();});
    jQuery(document).on('change','.GB_size_con select[id$="size"]',function(){set_screen_size(jQuery(this).attr('id'));});

    jQuery(document).ajaxSuccess(function(e) {
        var this_id = document.activeElement.id;
        if(this_id.indexOf("gb_gallery_widget")>=0){
            gb_gallery_widget_submit(this_id);
        }
    });
    jQuery('#gb_save_option').change(function(){
        jQuery(this).attr("disabled", true);
        data = {
            action      : 'gb_ajax_delete_option'
        }
        jQuery.post(ajaxurl, data, function(response){
            var new_response = get_message(0,response);
            if(new_response == 0){
                jQuery('.gb_delete_option').find('.gb_option_update').find('span').html('Not saved');
                jQuery('.gb_delete_option').find('.gb_option_update').find('span').css({color:'#FF0000'});
            }else{
                jQuery('.gb_delete_option').find('.gb_option_update').find('span').html('Saved');
                jQuery('.gb_delete_option').find('.gb_option_update').find('span').css({color:'#008d1e'});
            }
        });
        jQuery(this).removeAttr("disabled");
    });
    jQuery('#gb_help_option').change(function(){
        jQuery(this).attr("disabled", true);
        data = {
            action      : 'gb_ajax_help_option'
        }
        jQuery.post(ajaxurl, data, function(response){
            var new_response = get_message(0,response);
        });
        jQuery(this).removeAttr("disabled");
    });
    jQuery('#gb_donate_option').change(function(){
        jQuery(this).attr("disabled", true);
        data = {
            action      : 'gb_ajax_donate_option'
        }
        jQuery.post(ajaxurl, data, function(response){
            var new_response = get_message(0,response);
        });
        jQuery(this).removeAttr("disabled");
    });
    MyonLoad();
});
/* Do wan the page is loaded */
function MyonLoad(){
    if(jQuery('#gb_gallery_meta').length>0){
        var $validation_div = "<div class='validation'></div>";
        var $validation_error = "<div class='validation_error'></div>";
        jQuery('#publishing-action').append($validation_div);
        jQuery('#major-publishing-actions').append($validation_error);
    }
    if(jQuery('#gb_the_img').attr('value')!=''){
        openprevadminimg(jQuery('#gb_the_img').attr('value'));
    }
    jQuery('.widget-liquid-right').find('.GB_size_con').each(function(){
        set_screen_size(jQuery(this).find('select').attr('id'));
    });
    if(jQuery('.gb_premium_container').length){
        jQuery('.gb_premium_container').each(function(){
            jQuery(this).attr({
                title   : 'Only available on premium version',
                alt     : 'Only available on premium version'
            });
        });
    }
    if(jQuery('.gb_delete_option').find('.gb_option_update').find('span').html() == 'Saved'){
        jQuery('.gb_delete_option').find('.gb_option_update').find('span').css({color:'#008d1e'});
    }else{
        jQuery('.gb_delete_option').find('.gb_option_update').find('span').css({color:'#FF0000'});
    }
}

/* Retrieve the real message and gb_close_all = 1 = close all divs or gb_close_all = div name = open */
function get_message(gb_close_all, gb_message){
    if(gb_close_all != 0){
        jQuery('#bg_group_message').hide();
        jQuery('#group_posts').hide();
        jQuery('#gb_image_effect').hide();
        jQuery('#'+gb_close_all).show();
        if(gb_close_all == "gb_image_effect"){
            jQuery('.step1').hide('highlight','',function(){
                jQuery('.step2').show('highlight');
            });
        }else{
            jQuery('.step2').hide('highlight','',function(){
                jQuery('.step1').show('highlight');
            });
        }
    }
    if(gb_message != ""){
        var new_gb_message = gb_message.substring(gb_message.indexOf("gb=")+3, gb_message.indexOf("=gb"));
        return new_gb_message;
    }
}

/* Open the media dialog for WordPress in $gb_post_type */
function uploadImg(mybtn){
    var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = jQuery(mybtn);
    var id = button.attr('id').replace('_btn', '');
    _custom_media = true;
    wp.media.editor.send.attachment = function(props, attachment)
    {
        if ( _custom_media )
        {

            jQuery("#"+id).val(attachment.url);
            openprevadminimg(attachment.url);
            jQuery('#gb_the_img').attr('title',attachment.url);

        } else {
            jQuery('.gb_the_img_img').css('display', 'none');
            return _orig_send_attachment.apply( mybtn, [props, attachment] );
        };
    }
    wp.media.editor.open(button);
    return false;
}

function openprevadminimg(imgsrc){
    jQuery('.gb_the_img_img > img').attr('src',imgsrc);
    jQuery('.gb_the_img_img > img').attr('title',imgsrc);
    jQuery('.gb_the_img_img').css('display', 'block');
}

//validate all the fields
function check_fields(){
    var gb_post_image = new Image();
    var time_finish = check_time = GB_post_error = '';
    var all_done = image = index = false;
    var group = true;
        gb_post_image.src = jQuery('#gb_the_img').attr('value');
    jQuery(".validation").css({opacity:'1'});
    jQuery(gb_post_image).load(function(){
        image = true;
    });
    jQuery('#gb_the_index').removeAttr('disabled');
    if(jQuery('#gb_the_index').attr('value')>0){
        index = true;
    }
    if(jQuery('#gb_the_group').find(":selected").text()==""){
        group = false;
    }
    check_time = setInterval(function(){
        if(image && index && group){
            all_done = true;
            clearInterval(check_time);
        }
    },1000);
    jQuery(".validation").css({opacity:'1'});
    time_finish = setInterval(function(){
        if(all_done){
            jQuery(".validation").css({opacity:'0'});
            jQuery(".validation").css({disabled:'none'});
            clearInterval(check_time);
            clearInterval(time_finish);
            jQuery('#publish').trigger('click');
        }else{
            //Need to check image & index & group to find the problems
            if(!image)
                GB_post_error += "<label>The image does not exist or it takes too long to upload</label>";
            if(!index)
                GB_post_error += "<label>The index must to be higher than 0</label>";
            if(!group)
                GB_post_error += '<label>Please select a "Group To" field</label>';
            jQuery(".validation_error").html(GB_post_error);
            jQuery(".validation_error").show();
            jQuery(".validation").css({opacity:'0'});
            clearInterval(check_time);
            clearInterval(time_finish);
        }
    },3000);



}

//Set the GB gallery screen size in the .GB_size_screen div
function set_screen_size(this_id){
    if(jQuery('#'+this_id).length){
        var selected_size = jQuery('#'+this_id).find('option:selected').val();
        var selected_width = selected_size.substring(0,selected_size.indexOf("X"));
        var selected_height = selected_size.substring(selected_size.lastIndexOf("X")+1);
        var device_width;
        var device_height;
        if(this_id.indexOf("mobile")>0){
            var device_width = '320';
            var device_height = '480';
        }else if(this_id.indexOf("tablet")>0){
            var device_width = '768';
            var device_height = '1024';
        }else{
            var device_width = window.screen.width;
            var device_height = window.screen.height;
        }
        var screen_width = window.screen.width;
        var screen_height = window.screen.height;
        if((device_width/10)>100){
            var the_screen = 100;
            var screen_resolution = the_screen/(device_width/device_height);
        }
        else{
            var the_screen = device_width/10;
            var screen_resolution = device_height/10;
        }
        var width_result = (selected_width/device_width) * 100;
        var height_result = (selected_height/device_height) * 100;
        jQuery('#'+this_id).parent().find('.GB_size_screen').css({
            width   :   the_screen+'px',
            height  :   (screen_resolution)+'px',
            opacity :   1
        });
        jQuery('#'+this_id).parent().find('.GB_size_widget').css({
            width   :   width_result+'%',
            height  :   height_result+'%'
        });
    }
}

//after widget save set the preview divs
function gb_gallery_widget_submit(widget){
    var my_this = jQuery('#'+widget).closest('.widget');
    my_this.find('.GB_size_con').each(function(){
        set_screen_size(jQuery(this).find('select').attr('id'));
    });
}