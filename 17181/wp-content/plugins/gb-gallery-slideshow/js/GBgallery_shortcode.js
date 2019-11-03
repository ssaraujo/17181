jQuery(document).ready(function($){
    $('#gb_short_code_btn').on('click',function(){get_short_code();});
});



//peeper the sort code
function get_short_code(){
    var short_group = jQuery('#gb_short_group option:selected').val();
    var short_size = jQuery('#gb_short_size option:selected').val();
    var short_mobile_size = jQuery('#gb_short_mobile_size option:selected').val();
    var short_tablet_size = jQuery('#gb_short_tablet_size option:selected').val();
    var short_duration = jQuery('#gb_short_duration').val();
    var short_general_effect = jQuery('#gb_short_general_effect option:selected').val();
    var short_master_class = jQuery('#gb_short_master_class').val();
    var gb_short_code_prop = "";
    var short_error_log=false;
    var short_special_effect = jQuery('#gb_short_special_effect').attr('checked') ? 'yes' : 'no';
    var short_auto_size = 'on';


    //Validate group selection, must
    if(short_group != "" && validation(short_group, 1)){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'group="'+short_group+'"';
    }else{
        if(!short_error_log){
            short_error_log = "";
        }
        short_error_log += "<p><b>Group:</b> there was an error in the group selection please try again.</p>";
    }
    //Validate size selection, must
    if(short_size != "" && validation(short_size, 2)){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'size="'+short_size+'"';
    }else{
        if(!short_error_log){
            short_error_log = "";
        }
        short_error_log += "<p><b>Images Size:</b> there was an error in the images size selection please try again.</p>";
    }
    if(short_auto_size == "on"){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'auto_resize="'+short_auto_size+'"';
    }else{
        //Validate mobile size selection, must
        if(short_mobile_size != "" && validation(short_mobile_size, 2)){
            gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'mobile_size="'+short_mobile_size+'"';
        }else{
            if(!short_error_log){
                short_error_log = "";
            }
            short_error_log += "<p><b>Images Mobile Size:</b> there was an error in the images size selection please try again.</p>";
        }
        //Validate tablet size selection, must
        if(short_tablet_size != "" && validation(short_tablet_size, 2)){
            gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'tablet_size="'+short_tablet_size+'"';
        }else{
            if(!short_error_log){
                short_error_log = "";
            }
            short_error_log += "<p><b>Images Tablet Size:</b> there was an error in the images size selection please try again.</p>";
        }
    }
    //Validate duration selection
    if(short_duration != "" && validation(short_duration, 1) && short_duration >= 1000){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'duration="'+short_duration+'"';
    }else{
        if(short_duration != ""){
            if(!short_error_log){
                short_error_log = "";
            }
            short_error_log += "<p><b>Duration:</b> only numbers <b>bigger</b> than 1000 are allowed in the duration field.</p>";
        }
    }

    if(short_general_effect != ""){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'general_effect="'+short_general_effect+'"';
    }
    if(short_special_effect != ""){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'special_effect="'+short_special_effect+'"';
    }
    //Validate master class selection
    if(short_master_class != "" && validation(short_master_class, 3)){
        gb_short_code_prop += (gb_short_code_prop !="" ? " ":"") + 'master_class="'+short_master_class+'"';
    }else{
        if(short_master_class != ""){
            if(!short_error_log){
                short_error_log = "";
            }
            short_error_log += "<p><b>Master Class:</b> must start with letters and no special characters</p>";
        }
    }
    if(short_error_log){
        jQuery('.gb_short_code_display').hide();
        jQuery('.gb_error_log').show();
        jQuery('.gb_error_log').html(short_error_log);
    }else{
        jQuery('.gb_error_log').hide();
        jQuery('.gb_short_code_display').show();
        gb_short_code_prop = print_short_code(gb_short_code_prop);
        gb_gallery_check_short_group(gb_short_code_prop,short_group);
    }

}
//ajax to check if the selected group hes images
function gb_gallery_check_short_group(short, group){
    data = {
        action      : 'gb_gallery_check_short_group',
        gb_nonce    : gb_vars.gb_nonce_admin,
        gb_group  : group
    }
    jQuery.post(ajaxurl, data, function(response){
        var new_response = get_message(0,response);
        jQuery('#gb_gallery_no_images').remove();
        if(new_response == 0){
            jQuery('.gb_short_code_display').find('ol li:first-child').find('label').css('color','#00a225');
        }else{
            jQuery('.gb_short_code_display').find('ol li:first-child').find('label').css('color','#FF0000').after('<div id="gb_gallery_no_images" class="gb_qa"><div class="gb_q"></div><div class="gb_a">'+new_response+'</div></div>');
        }
        jQuery('.gb_short_code_display').find('ol li:first-child').find('label').html(short);
    });

}

//Validation 1=numbers only, 2=Size, 3=master 4=general effect class
function validation(validate, validate_type){
    switch (validate_type){
        //numbers only
        case 1:
            return !(/\D/).test(validate);
        //Size
        case 2:
            var gb_size = new Array(
                '900X500',
                '900X324',//
                '700X390',//for Tablet
                '700X252',//
                '280X154',//for Mobiles
                '280X100'//
            );
            if(jQuery.inArray(validate, gb_size)<0){
                return false;
            }
            return (/\d\d\d[X]/).test(validate);
        //master class
        case 3:
            return (/^[A-Za-z]+[-a-zA-Z0-9_ ]+$/).test(validate);
        case 4:
            return (/^[A-Za-z]+[-a-zA-Z0-9_ ]+$/).test(validate);
    }
}

//input the short code
function print_short_code(args){
    return '[gb_gallery '+ args +']';
}
