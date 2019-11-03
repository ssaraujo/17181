jQuery(document).ready(function($){

    //Ajax get all the group related posts
    $('#groups-form').on('submit',function(){
        $('#group_submit_con').find('.group_withing').css({display:'block'});
        data = {
            action          : 'gb_ajax_get_group',
            gb_nonce        : gb_vars.gb_nonce,
            selected_group  : $('#groups').find(":selected").val()
        }
        $.post(ajaxurl, data, function(response){
            var new_response = get_message('group_posts',response);
            $('#group_posts').html(new_response);
            $('#group_submit_con').find('.group_withing').css({display:'none'});
        });
        return false;
    });

    //Ajax set the image + helpers and save buttons
    $(document).on('click','.gb_group_admin_item',function(){
        $('#group_submit_con').find('.group_withing').css({display:'block'});
        var gb_post_id = $(this).attr('id');
        gb_post_id = gb_post_id.substr(gb_post_id.lastIndexOf('-')+1);
        $('#group-submit').remove();
            data = {
                action      : 'gb_ajax_show_effect',
                gb_nonce    : gb_vars.gb_nonce,
                gb_post_id  : gb_post_id,
                gb_con_width: $('#gb_image_effect').width()
            }
            $.post(ajaxurl, data, function(response){
                var new_response = get_message(0,response);
                if(new_response == 0){
                    get_message('bg_group_message','');
                    $('#bg_group_message').html('<h2 class="gb_error_message">Error loading the image</h2>');
                }else{
                    get_message('gb_image_effect','');
                    $('#gb_image_effect').html(new_response);
                    gb_run_effect_div();
                }
                $('#group_submit_con').find('.group_withing').css({display:'none'});
            });
    });

    //Mark for effect in the clicked index
    $(document).on('click','div[class^="GB_gallery_helper-"]',function(){set_effect_index($(this).attr('class'));});

    //Ajax to save the effect
    $(document).on('click','.gb_save_effect',function(){
        var conf = true;
        if(!jQuery('div[gb_on]').length){
            conf = confirm("Are you sure you want to save an EMPTY effect?");
        }
        if(conf == true){
            var post_id = jQuery('.gb_post_img').attr('id').substring(jQuery('.gb_post_img').attr('id').lastIndexOf("-")+1);
            data = {
                action          : 'gb_ajax_save_effect',
                gb_nonce        : gb_vars.gb_nonce,
                gb_effect_matrix: $('#gb_effect_index').attr('value'),
                gb_post_id      : post_id
            }
            $.post(ajaxurl, data, function(response){
                var new_response = get_message('bg_group_message',response);
                $('#bg_group_message').html(new_response);

            });
        }
    });

    //Copy to Clipboard the effect
    $(document).on('click','.gb_copy_effect',function(){
        data = {
            action          : 'gb_ajax_copy_effect',
            gb_nonce        : gb_vars.gb_nonce,
            gb_effect_matrix: $('#gb_effect_index').attr('value')
        }
        $.post(ajaxurl, data, function(response){
            var new_response = get_message('',response);
            gb_copy_effect(new_response);

        });

    });

    //Load the old effect
    $(document).on('click','.gb_load_effect',function(){load_old_effect();});

    //New effect
    $(document).on('click','.gb_new_effect',function(){new_effect();});

    //close the group div
    $(document).on('click','.gb_close',function(){$(this).parent().remove();});

});

/* set the helper div in #gb_post_img */
function gb_run_effect_div(){
    var new_helper = jQuery('.GB_gallery_helper').clone();
    new_helper.css({display:'block'});
    jQuery('.gb_post_img').append(new_helper);
    setHelper();
}

/* Arranges all the .GB_gallery_helper-* inside GB_gallery_helper */
function setHelper(){
    var i = 1,x = 0,y = 0;
    var font_size,line_height;
    var resolution = 100;
    var intWidth = jQuery('.gb_post_img').width();
    var intHeight = jQuery('.gb_post_img').height();
    var factorX = intWidth * 0.1;
    var factorY = intHeight * 0.1;
    var perrow = resolution * 0.1;
    switch(intWidth)
    {
        case 200:
            font_size = '30%';
            line_height = '3px';
            break;
        case 400:
            font_size = '70%';
            line_height = '8px';
            break;
        default:
            font_size = '100%';
            line_height = 'inherit';
    }
    jQuery('.gb_post_img').find('div[class^="GB_gallery_helper-"]').each(function(index){
        jQuery(this).css({
            width       :   (intWidth/perrow)+'px',
            height      :   (intHeight/perrow)+'px',
            top         :   (y * factorY).toFixed(2)+'px',
            left        :   (x * factorX).toFixed(2)+'px',
            fontSize    :   font_size,
            lineHeight  :   line_height
        });
        if(i==10){
            i=1;
            y++;
            x=0;
        }else{
            i++;
            x++;
        }
        jQuery(this).html('<div>'+(index+1)+'</div>');
    });
    jQuery('#gb_effect_index').attr('value','');
}

/* Show the effect index */
function set_effect_index(this_class){
    if(jQuery('#gb_effect_index').attr('value') == ''){
        var index_array = new Array();
    }else{
        var index_array = jQuery('#gb_effect_index').attr('value').split(',');
    }
    var index_div = jQuery('.gb_post_img').find("."+this_class).attr('class');
    var index_exists = '';
    index_div = index_div.substring(index_div.lastIndexOf("-")+1);
    index_exists = index_array.indexOf(index_div);
    if(index_exists >= 0){
        index_array.splice(index_exists,1);
        index_exists = '';
    }else{
        index_array.push(index_div);
        index_exists = index_array.length;
    }
    gb_show_hide_index(jQuery('.gb_post_img').find("."+this_class),index_exists);
    if(index_array.length > 0){
        jQuery('#gb_effect_index').attr('value',index_array.join(','));
    }else{
        jQuery('#gb_effect_index').attr('value',index_array);
    }
}

/* Load the old effect */
function load_old_effect(gb_effect_db){
    if(jQuery('div[gb_on]').length){
        new_effect();
    }
    if(jQuery('#gb_old_effect').attr('value') != '' || typeof gb_effect_db != 'undefined'){
        var font_size = Math.round(jQuery('.gb_post_img').find(".GB_gallery_helper-1").find('div').height()*0.7);
        var effect_array
        if(typeof gb_effect_db != 'undefined'){
            effect_array = jQuery('.gb_effects_con').find('li[value="'+gb_effect_db+'"]').html().split(', ');
        }
        else{
            effect_array = jQuery('#gb_old_effect').attr('value').split(', ');
        }
        var new_effect_var = new Array();
        for(i = 0; i < effect_array.length; i++){
            gb_show_hide_index(jQuery('.gb_post_img').find(".GB_gallery_helper-"+effect_array[i]),i+1);
            new_effect_var.push(effect_array[i]);
        }
        jQuery('#gb_effect_index').attr('value',new_effect_var.join(','));
    }else{
        alert('No special effect found for this image.');
    }
}

/* New effect, clear the effect and delete #gb_effect_index value */
function new_effect(){
    if(jQuery('div[gb_on]').length){
        var conf = confirm("Are you sure you want to remove this effect?");
        if(conf == true){
            jQuery('#gb_effect_index').attr('value','');
            jQuery('#gb_effect_index').removeAttr('value');
            jQuery('div[gb_on]').each(function(){
                gb_show_hide_index(jQuery(this),'');
            });
        }
    }
}

/* Show/hide the index on the image */
function gb_show_hide_index(item,index){
    if(item.attr( "gb_on")){
        remove_effect_index(item.find("p").html());
        item.find("p").remove();
        item.removeAttr('gb_on');
    }else{
        var font_size = Math.round(item.find('div').height()*0.7);
        item.attr( "gb_on",'1' );
        item.find('div').append("<p>"+ index +"</p>");
        item.find('div').find('p').css({fontSize:font_size});

    }
}

/* reArranges the index after deleting 1 index */
function remove_effect_index(index_to_remove){
    jQuery('.gb_post_img').find('div[gb_on]').each(function(index){
        if(parseInt(jQuery(this).find('p').html()) > parseInt(index_to_remove)){
            jQuery(this).find('p').html(parseInt(jQuery(this).find('p').html())-1);
        }
    });

}

/**/
function gb_copy_effect (gb_effect) {
    console.log(gb_effect);
    window.prompt ("Copy to clipboard: Ctrl+C, Enter", gb_effect);
}