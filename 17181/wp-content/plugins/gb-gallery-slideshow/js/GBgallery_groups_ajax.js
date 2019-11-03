jQuery(document).ready(function($){
    $('#add_group').on('click',function(){
        close_open_message('gb_group_add','');
    });

    //Ajax get the highest index
    $('#gb_the_group').on('change',function(){
        gb_group_index(this);
    });

    $(document).on('change','input[id^="group_add"]',function(){gb_validate_group_name(this);});
    $(document).on('click','.gb_close',function(){$(this).parent().remove();});

    //Ajax get all the group related posts
    $('#groups-form').on('submit',function(){
        $('#group_submit_con').find('.group_withing').css({display:'block'});
        data = {
            action          : 'gb_ajax_get_group',
            gb_nonce        : gb_vars.gb_nonce,
            selected_group  : $('#groups').find(":selected").val()
        }
        $.post(ajaxurl, data, function(response){
            var new_response = close_open_message('group_posts',response);
            $('#group_posts').html(new_response);

            $('#group_submit_con').find('.group_withing').css({display:'none'});
        });
        return false;
    });

    //Ajax to add a group to DB
    $(document).on('submit','#group_add-form',function(){
        $('#group_add-form').find('.group_withing').css({display:'block'});
        close_open_message('gb_group_message','');
        var gb_add_groups_array = gb_add_groups_prepare();
        if(gb_add_groups_array.length){
            data = {
                action       : 'gb_ajax_add_group',
                gb_nonce     : gb_vars.gb_nonce,
                gb_add_groups: gb_add_groups_array
            }
            $.post(ajaxurl, data, function(response){
                console.log(response);
                var new_response = close_open_message('gb_group_message',response);
                gb_show_combo_groups();
                $('#gb_group_message').html(new_response);
                $('#group_add-form').find('.group_withing').css({display:'none'});
            });
        }else{
            $('#gb_group_message').html('<h2 class="gb_error_message">No groups was found</h2>');
        }
        return false;
    });

    //Ajax to delete a group from DB
    $('#delete_group').on('click',function(){
        $('#group_delete_con').find('.group_withing').css({display:'block'});
        conf = confirm("Are you sure you want to DELETE '"+$('#groups :selected').text()+"' group?\nNote that, all GB Gallery posts from '"+$('#groups :selected').text()+"' group Will be added to the 'General' group");
        if(conf == true){
            data = {
                action              : 'gb_ajax_delete_group',
                gb_nonce            : gb_vars.gb_nonce,
                selected_group_id   : $('#groups').find(":selected").val(),
                selected_group      : $('#groups').find(":selected").html()
            }
            $.post(ajaxurl, data, function(response){
                var new_response = close_open_message('gb_group_message',response);
                gb_show_combo_groups();
                $('#gb_group_message').html(new_response);
            });
        }
        $('#group_delete_con').find('.group_withing').css({display:'none'});
        return false;
    });
});
/* Reload the groups to combo box #groups*/
function gb_show_combo_groups(){
    data = {
        action       : 'gb_ajax_show_combo_groups',
        gb_nonce     : gb_vars.gb_nonce
    }
    jQuery.post(ajaxurl, data, function(response){
        var new_response = close_open_message(0,response);
        if(new_response!='Error'){
            jQuery('#groups').html(new_response);
        }
    });
    return false;
}

/* Retrieve the real message and gb_close_all = 1 = close all divs or gb_close_all = div name = open */
function close_open_message(gb_close_all, gb_message){
    if(gb_close_all != 0){
        jQuery('#gb_group_message').hide();
        jQuery('#gb_group_message').html('');
        jQuery('#group_posts').hide();
        jQuery('#group_posts').html('');
        jQuery('#gb_group_add').hide();
        if(gb_close_all == 'gb_group_add')
            reset_add_group();
        jQuery('#'+gb_close_all).show();
    }
    if(gb_message != ""){
        var new_gb_message = gb_message.substring(gb_message.indexOf("gb=")+3, gb_message.indexOf("=gb"));
        return new_gb_message;
    }
}

/* On text field change, add a note to the side of the text field with a gb_add_group_error */
function gb_validate_group_name(item){
    if(!jQuery(item).val().match(/^[A-Za-z0-9 _-]+$/)){
        var gb_add_group_error = "<b>this group will not be saved</b>";
        var gb_add_group_error_con = jQuery('.gb_note').clone();
        gb_add_group_error_con.find('li').html(gb_add_group_error);
        jQuery(item).css({borderColor:'#FF0000'});
        jQuery(item).parent('.group_add_item_con').append(gb_add_group_error_con);
    }else if(jQuery(item).css("border-color")=="rgb(255, 0, 0)"){
        jQuery(item).css({borderColor:'rgb(188, 188, 188)'});
        jQuery(item).parent('.group_add_item_con').find('.gb_note').remove();
    }
}

/* Prepare array gb_add_groups_arr of values from the add group fields to save */
function gb_add_groups_prepare(){
    if(jQuery('.group_add_the_group').length){
        var gb_add_groups_arr = new Array();
        jQuery('.group_add_the_group').each(function(){
            if(jQuery(this).val() && !jQuery(this).parent().find('.gb_note').length){
                gb_add_groups_arr.push(jQuery(this).val());
            }
        });
        return gb_add_groups_arr;
    }else{
        // need to stop the submit
        return;
    }

}


/* Prepare in hidden field value of array order by the index for the group post (Prepare the index of the post to save)  */
function gb_prepare_submit(){
    if(jQuery('.new_index').length){
        var prepare_array = new Array(jQuery("#gb_admin_group_items>li").length);
        jQuery("#gb_admin_group_items>li").each(function(index){
            var gb_post_id = jQuery(this).find('.gb_group_admin_item').attr('id');
            gb_post_id = gb_post_id.substr(gb_post_id.lastIndexOf('-')+1);
            prepare_array[index] = gb_post_id;
        });
        jQuery('#to_submit').attr('value', prepare_array);
    }else{
        jQuery('#to_submit').attr('value', 'non');
    }
}

/* reset the add group div #gb_group_add */
function reset_add_group(){
    var add_group = jQuery('.gb_helper').find('.group_add_con').clone();
    jQuery('#gb_group_add').html(add_group);
}

//Ajax get the highest index
function gb_group_index(my_this){
    jQuery(my_this).prop('disabled', 'disabled');
    jQuery('#gb_the_index').attr('for', jQuery(my_this).find(':selected').val());
    data = {
        action       : 'gb_ajax_get_index',
        gb_nonce_home: gb_vars.gb_nonce_home,
        group_index  : jQuery(my_this).find(':selected').val(),
        group_id    : jQuery('#gb_the_index').attr('for')
    }
    jQuery.post(ajaxurl, data, function(response){
        var new_response = close_open_message(0,response);
        if(new_response != '-1')
            jQuery('#gb_the_index').attr('value', new_response);
        else{
            alert('The index was not saved please reload the page');
            jQuery('#gb_the_index').attr('value', '0');

        }
    });
    jQuery(my_this).prop('disabled', false);
    return false;
}
















