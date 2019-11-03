(function($){
    $.fn.GBgallery = function(options){
        var main_this = this, The_img, Old_img, HideMath, MathLength, mainTime, effectTimer , gb_slider, gb_preview, next_click, preview_space, current_this, settings;
        var more = false, loop_num = 1, total_loop_num, image_load = 0, font_size;
        var defaultSettings = {
            width               :   900,
            height              :   500,
            ImgTime             :   6000,
            EffectTime          :   1500,
            Resolution          :   10,
            Effect              :   'bounce',
            SpecialEffect       :   false,
            FontSize            :   'gb_mid',
            AutoResize          :   true,
            MobileSize          :   '280X154',
            TabletSize          :   '700X390'
        }
        return main_this.each(function(){
            current_this = $(this);
            if(options){
                if(typeof(options)=='string'){
                    eval('var obj='+options);
                    settings = $.extend(true, {}, defaultSettings, obj);
                }else{
                settings = $.extend(true, {}, defaultSettings, options);
                }
            }


             settings.MobileSize = '280X154';
             settings.TabletSize = '700X390';
            var gb_gallery_device = current_this.find('.GB_helper').find('.gb_gallery_device');
            if(gb_gallery_device.width() == 1){
                settings.width = settings.MobileSize.substring(0,settings.MobileSize.indexOf('X'));
                settings.height = settings.MobileSize.substring(settings.MobileSize.indexOf('X')+1);
                settings.height = Math.round(settings.height / 10) * 10;
            }else if(gb_gallery_device.width() == 2){
                settings.width = settings.TabletSize.substring(0,settings.TabletSize.indexOf('X'));
                settings.height = settings.TabletSize.substring(settings.TabletSize.indexOf('X')+1);
                settings.height = Math.round(settings.height / 10) * 10;
            }else if(gb_gallery_device.width() == 3){
                settings.width = settings.TabletSize.substring(0,settings.TabletSize.indexOf('X'));
                settings.height = settings.TabletSize.substring(settings.TabletSize.indexOf('X')+1);
                settings.height = Math.round(settings.height / 10) * 10;
            }

            font_size = get_font_size(settings.width,settings.height);
            gb_slider = current_this.find('.GB_gallery_slider');
            gb_preview = current_this.find('div[id^="GB_preview-"]');
            if(!current_this.find('.GB_helper').find('.GB_img_con').find('div').length){
                console.log('No images found');
                return false;
            }
            pre_load(current_this.find('.GB_helper').find('.GB_img_con'));
            gb_loader();
            if(mainTime == true)
                gb_loader();
            Old_img = The_img = current_this.find('.GB_helper').find('.GB_img_con').find('div[id^="GB_img_div"]:first-child');
            MathLength = settings.Resolution * settings.Resolution;
            setMainSlider();
            //if there is more then 10 images
            if(gb_preview.find('.GB_total').length){
                more = true;
                //number of all the preview row's
                total_loop_num = gb_preview.find('.GB_total').attr('row');
            }
            gb_preview.find('.GB_preview_item').on('click',function(){getNext($(this).attr('id'));});
            gb_preview.find('.GB_gallery_preview_more_btn p').on('click',function(){seeMore();});
            //check if all images in the preview are loaded
            //if not load again 1 more time
            window.onload= function(){
                setTimeout(function(){
                    jQuery('div[id^="gb_gallery-"]').each(function(){
                        if(jQuery(this).find('.GB_gallery_slider').find('.GB_gallery_loader').css('display') == 'block'){
                            console.log('Preview error '+jQuery(this).attr('id'));
                            force_load_gb_gallery(jQuery(this));
                        }
                    });
                },3000);
            };
        });

        //Set the divs (class^="GB_gallery_slider-) in please
        function setMainSlider(){
            //set the width and height of the slider
            gb_slider.width(settings.width);
            gb_slider.height(settings.height);
            //set the font-size of the slider
            gb_slider.addClass(font_size);
            gb_preview.addClass(font_size);

            //set the first image to show
            gb_slider.css({
                backgroundSize  :   settings.width+'px '+settings.height+'px',
                backgroundImage : "url("+The_img.find('img').attr('src')+")"
            });
            build_divs();
            //PREVIEW
            //Set preview_space to the spaces between the preview divs depending on the GB gallery size
            switch (true){
                case font_size.indexOf('gb_big5') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.31;
                        break;
                    }else{
                        preview_space = 1.55;
                        break;
                    }
                case font_size.indexOf('gb_big4') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.3;
                        break;
                    }else{
                        preview_space = 1.50;
                        break;
                    }
                case font_size.indexOf('gb_big3') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.33;
                        break;
                    }else{
                        preview_space = 1.43;
                        break;
                    }
                case font_size.indexOf('gb_big2') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.33;
                        break;
                    }else{
                        preview_space = 1.4;
                        break;
                    }
                case font_size.indexOf('gb_big1') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.3;
                        break;
                    }else{
                        preview_space = 1.38;
                        break;
                    }
                case font_size.indexOf('gb_small5') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.13;
                        break;
                    }else{
                        preview_space = 1.15;
                        break;
                    }
                case font_size.indexOf('gb_small4') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 0.95;
                        break;
                    }else{
                        preview_space = 0.94;
                        break;
                    }
                case font_size.indexOf('gb_small3') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 0.5;
                        break;
                    }else{
                        preview_space = 0.5;
                        break;
                    }
                case font_size.indexOf('gb_small2') >= 0:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 0.5;
                        break;
                    }else{
                        preview_space = 0.5;
                        break;
                    }
                case font_size.indexOf('gb_small1') >= 0:
                    preview_space = 1;
                    break;
                default:
                    if(font_size.indexOf('wide') > 0){
                        preview_space = 1.23;
                        break;
                    }else{
                        preview_space = 1.26;
                        break;
                    }
            }
            // set the width and height of one div.GB_gallery_slider in the slider
            var factorH = settings.height / settings.Resolution;

            gb_preview.width(settings.width);

            //set the width and height of the preview ul
            gb_preview.find('ul.GB_preview_items').width(settings.width);
            gb_preview.find('ul.GB_preview_items').height((Math.ceil((factorH*1.5) / 10) * 10)+10);


            //Preview height
            if(font_size.indexOf('wide')>0){
                var Preview_height = Math.round(factorH*preview_space);
                var Preview_width = Math.round(factorH*preview_space)*1.8;
            }else{
                var Preview_height = Math.round(factorH*preview_space);
                var Preview_width = Math.round(factorH*preview_space);
            }
            //set the first preview li's width and height
            gb_preview.find('ul.GB_preview_items li').width(Preview_width);
            gb_preview.find('ul.GB_preview_items li').height(Preview_height);

            //if there is more then 10 images
            if(gb_preview.find('ul.GB_preview_more_items').length){
                //set the maxHeight to 3 rows only, 37 = css spacing
                gb_preview.find('.GB_gallery_preview_more').css({
                    maxHeight : (Preview_height*3)+37
                });

                //set the rest of the preview li's width and height
                gb_preview.find('ul.GB_preview_more_items li').width(Preview_width * .85);
                gb_preview.find('ul.GB_preview_more_items li').height(Preview_height * .85);
            }

            //show the first description div
            gb_slider.find('.GB_gallery_desc_con').append(The_img.find('.GB_gallery_desc').clone());
        }

        //Start the GB gallery loop
        function startGBgallery(my_this){
            //console.log('Start: '+main_this.attr('id'));
            if(mainTime != "pause"){
                mainTime= "";
                //The main Interval/loop start
                mainTime = setInterval(function(){
                    if(more && mainTime != "pause"){
                        //set 10 preview for a row
                        if(loop_num == gb_preview.find('.GB_preview_items li').length){
                            get_next_preview(loop_num);
                        }
                        if((loop_num / 10) >= total_loop_num)
                            loop_num = 0;
                        loop_num++;
                    }
                    //Because of the timer, need to check every step of the way is not paused
                    if(mainTime != "pause"){setSlider();}
                    if(mainTime != "pause"){runEffect();}
                    if(mainTime != "pause"){setDesc();}

                },(settings.ImgTime-settings.EffectTime));
            }
        }

        //Set the divs (class^="GB_gallery_slider-) backgroundImage and next image
        function setSlider(){
            var temp_divs_con = current_this.find('.GB_helper').find('.GB_divs_con').clone();
            //clear the effect divs in case of next image
            gb_slider.find('.GB_divs_con').remove();

            //Set all the matrix divs backgroundImage and display them
            temp_divs_con.find('div[class^="GB_gallery_slider-"]').find('div').css({
                backgroundImage :   "url("+The_img.find('img').attr('src')+")",
                display         :   'block'
            });

            //set the new effect divs in place
            gb_slider.find('.GB_gallery_loader').before(temp_divs_con);

            //check if there was a preview click
            if(!next_click){
                Old_img = The_img;
                //check if The_img is the last child
                if(The_img.is(":last-child")){
                    //get the first image
                    The_img = current_this.find('.GB_helper').find('.GB_img_con').find('div[id^="GB_img_div"]:first-child');
                }else{
                    //get the next image
                    The_img = The_img.next();
                }
                //set the image to display in the back ground
                gb_slider.css({
                    backgroundImage: "url("+The_img.find('img').attr('src')+")"
                });
            }else{
                next_click = false;
            }
        }

        //Set all the matrix divs in please + set the backgroundPosition and size
        function build_divs(){
            var divs_con = current_this.find('.GB_helper').find('.GB_divs_con');
            // set the width and height of one div.GB_gallery_slider in the slider
            var factorW = settings.width / settings.Resolution;
            var factorH = settings.height / settings.Resolution;
            //x represent the column's, y represent the row's
            var i = 1,x = 0,y = 0;



            //set all the GB_gallery_slider div's width, height, top, left
            divs_con.find('div[class^="GB_gallery_slider-"]').each(function(){
                jQuery(this).css({
                    width   :  factorW+"px",
                    height  :  factorH+"px",
                    top     :   (y * factorH).toFixed(2)+'px',
                    left    :   (x * factorW).toFixed(2)+'px'
                });
                jQuery(this).attr({
                    row     :   y,
                    column  :   x
                });

                //set all the GB_gallery_slider inner div's width, height, backgroundSize, backgroundPosition
                jQuery(this).find('div').css({
                    width               :   factorW+'px',
                    height              :   factorH+'px',
                    backgroundSize      :   settings.width+'px '+settings.height+'px',
                    backgroundPosition  :   '-'+ (x*factorW).toFixed(2) + 'px -'+ (y*factorH).toFixed(2) +'px'
                });
                //every 10 div's start a new row
                if(i==10){
                    i=1;
                    y++;
                    x=0;
                }else{
                    i++;
                    x++;
                }
            });
        }

        //on preview click get the clicked image
        function getNext(clicked_img){
            //stop the timers
            window.clearInterval(mainTime);
            mainTime = "pause";
            window.clearInterval(effectTimer);
            gb_loader();

            //get the row number
            var row_on = jQuery('#'+clicked_img).attr('row') * 10;

            //set the loop_num to the clicked li
            loop_num = jQuery('#'+clicked_img).attr('id').substring(jQuery('#'+clicked_img).attr('id').lastIndexOf("-")+1);

            //get the post id
            clicked_img = clicked_img.substring(clicked_img.indexOf("-")+1,clicked_img.length);
            clicked_img = clicked_img.replace("-","");

            //set the post to The_img
            The_img = current_this.find('.GB_helper').find('.GB_img_con').find('div[id$="'+clicked_img+'"]');

            //set the image to display in the back ground
            gb_slider.css({
                backgroundImage: "url("+The_img.find('img').attr('src')+")"
            });

            //clear the effect divs
            gb_slider.find('.GB_divs_con').remove();

            //set the new image to the GB gallery slider
            setTimeout(function(){setSlider();},800);

            //set the new description
            setDesc();

            //if the preview first line need to be replace
            if(row_on != gb_preview.find('ul.GB_preview_items li:first-child').attr('row')){
                //set the row in preview line
                get_next_preview(row_on);
            }

            //Indication foe a preview click
            next_click = true;

            gb_slider.find('div[class^="GB_gallery_slider-"]').css({
                    display         :   'block'
            });
            //Start the GB Gallery with the effect
            setTimeout(function(){
                if(mainTime == "pause")
                    mainTime = true;
                runEffect();
            },settings.ImgTime);
            gb_loader();
        }

        //Show the title and description
        function setDesc(){
            //clear the description
            gb_slider.find('.GB_gallery_desc_con').html('');
            //set the new description
            gb_slider.find('.GB_gallery_desc_con').append(The_img.find('.GB_gallery_desc').clone());
        }

        //Run the effect
        function runEffect(){
            if(mainTime != "pause"){
                window.clearInterval(mainTime);
                mainTime = "pause";
                var effectCon = current_this.find('.GB_gallery_slider').find('.GB_divs_con');
                var Math_i = 0, check_i = 0;
                //get the matrix for the effect
                buildHideMath();
                window.clearInterval(effectTimer);
                effectTimer = "";
                effectTimer = setInterval(function(){
                    //if it's not the lase one
                    if(Math_i < MathLength-1){
                        if(Math_i >= check_i){
                            check_i = Math_i;
                        }else{
                            window.clearInterval(effectTimer);
                            Math_i = check_i+1;
                            gb_slider.find('.GB_divs_con').remove();
                            //set the new effect divs in place
                            gb_slider.find('.GB_gallery_loader').before(current_this.find('.GB_helper').find('.GB_divs_con').clone());
                            runEffect();
                        }
                        //hide the div in please HideMath[Math_i]
                        effectCon.find('.GB_gallery_slider-'+HideMath[Math_i]).find('div').hide(settings.Effect,'',400,Math_i++);
                    }else{
                        //stop the effect Interval
                        window.clearInterval(effectTimer);
                        effectCon.find('.GB_gallery_slider-'+HideMath[Math_i]).find('div').hide(settings.Effect,function(){
                            gb_slider.find('.GB_divs_con').remove();
                            Math_i=MathLength;
                            setDesc();
                            setTimeout(function(){
                                mainTime = true;
                                startGBgallery();
                            },settings.EffectTime);});
                        Math_i++;
                    }
                },(settings.EffectTime/MathLength));
            }
        }

        //build the HideMath array
        function buildHideMath(){
            //if settings.Effect = true and the image hes effect
            if(settings.SpecialEffect && The_img.find('img[effect]').length){
                HideMath = The_img.find('img').attr('effect').split(", ");
            }else{
                HideMath = new Array();
                for(var i=0;i<MathLength;i++){
                    HideMath.push(i);
                }
            }
        }

        //Pause the loop
        function Pause(){
            window.clearInterval(mainTime);
            window.clearInterval(effectTimer);
            if(gb_slider.find(".GB_gallery_desc").attr("for") != The_img.attr('id')){
                setDesc();
            }
            gb_slider.find('.GB_divs_con').remove();
            gb_slider.find('.GB_gallery_PP').css({
                backgroundPosition  : '0px 39px',
                display             : 'block'
            });
            mainTime = "pause";
            setTimeout(function(){
                gb_slider.find('.GB_gallery_PP').css({
                    display : 'none'
                });
            },1000);
        }

        //Play the loop
        function Play(){
            mainTime = true;
            gb_slider.find('.GB_gallery_PP').css({
                backgroundPosition  : '0px 0px',
                display             : 'block'
            });
            setTimeout(function(){
                gb_slider.find('.GB_gallery_PP').css({
                    display : 'none'
                });
            },1000);
            startGBgallery();
        }

        //Open the 'More' div
        function seeMore(){
            if(gb_preview.find(".GB_gallery_preview_more").is(':visible')){
                gb_preview.find(".GB_gallery_preview_more_btn").find('p').removeClass('u');
                gb_preview.find(".GB_gallery_preview_more_btn").find('p').addClass('d');
            }else{
                gb_preview.find(".GB_gallery_preview_more_btn").find('p').removeClass('d');
                gb_preview.find(".GB_gallery_preview_more_btn").find('p').addClass('u');
            }
            gb_preview.find(".GB_gallery_preview_more").slideToggle({direction: 'up'});
        }

        //Load the next 10 preview to top
        function get_next_preview(row_num){
            //Index / 10 = the row number
            row_num = row_num / 10;

            //if the row is the last 1
            if(row_num > total_loop_num){
                row_num = 0;
            }
            if(row_num > 0 && row_num < 1){
                row_num = 0;
            }
            //from = the first line of the preview (visible)
            var from = gb_preview.find('.GB_preview_items');

            //to = the container of the preview (not visible)
            var to = gb_preview.find(".GB_gallery_preview_more").find('.GB_preview_more_items');

            //width and height of the first line of the preview (big)
            var from_h = from.find('li:last-child').height();
            var from_w = from.find('li:last-child').width();

            //width and height of the preview (small)
            var to_h =  to.find('li:last-child').height();
            var to_w =  to.find('li:last-child').width();

            //while there ar children in the preview(visible) set the width and height to the small preview
            while(from.children().length){
                var gb_this = from.find('li:last-child');
                gb_this.width(to_w).height(to_h).prependTo(to);
            }

            //the row to move need to be in the big size
            to.find('li[row='+row_num+']').each(function(){
                jQuery(this).width(from_w).height(from_h).appendTo(from).hide().show(settings.Effect,'',600);
            });
        }

        //Show/hide the loader image
        function gb_loader(){
            gb_slider.find('.GB_gallery_loader').toggle();
        }

        //pre load the GB gallery images
        function pre_load(Images) {
            Images.find('div[id^="GB_img_div"]').each(function(){
                jQuery('<img />')[0].src = jQuery(this).find('img').attr('src');
                jQuery('<img />')[0].onload = all_loaded();
            });
        }

        //check if the first 10 images are loaded
        function all_loaded(){
            var pre_img_length = gb_preview.find('.GB_preview_items').find('li').find('img').length;
            image_load++;
            if(image_load == pre_img_length){
                check_load();
            }
        }

        //check if all the preview images are loaded
        function check_load(){
            var pre_img_length = gb_preview.find('.GB_preview_items').find('li').find('img').length;
            var pre_img = gb_preview.find('.GB_preview_items').find('li').find('img');
            var img_num = 0;
            pre_img.each(function(){
                jQuery(this).load(function(){
                    img_num++;
                    if(img_num == pre_img_length){
                        load_done();
                    }else{
                        gb_slider.find('.GB_gallery_loader').find('b').html(Math.round(((pre_img_length/100)*img_num)*100));
                    }
                });
            });
        }

        //start the GB Gallery after all loaded
        function load_done(){
            mainTime = true;
            gb_slider.find('.GB_gallery_loader').hide();
            gb_preview.show();
            gb_slider.on({mouseenter: Pause,mouseleave: Play});
            setTimeout(function(){startGBgallery();},settings.EffectTime);
        }

        function force_load_gb_gallery(my_gallery){
            mainTime = true;
            jQuery(my_gallery).find('.GB_gallery_slider').find('.GB_gallery_loader').hide();
            jQuery(my_gallery).find('div[id^="GB_preview-"]').show();
            jQuery(my_gallery).find('.GB_gallery_slider').on({mouseenter: Pause,mouseleave: Play});

            startGBgallery();
        }

        //Return the FontSize
        function get_font_size(the_width, the_height){
            switch (true) {
                case the_width >= 1000:
                    if(the_height >= 500)
                        return "gb_big5";
                    else
                        return "gb_big5_wide";
                case the_width >= 900:
                    if(the_height >= 500)
                        return "gb_big4";
                    else
                        return "gb_big4_wide";
                case the_width >= 800:
                    if(the_height >= 400)
                        return "gb_big3";
                    else
                        return "gb_big3_wide";
                case the_width >= 700: //for Tablet
                    if(the_height >= 300)
                        return "gb_big2";
                    else
                        return "gb_big2_wide";
                case the_width >= 600:
                    if(the_height >= 300)
                        return "gb_big1";
                    else
                        return "gb_big1_wide";
                case the_width >= 500:
                    if(the_height >= 200)
                        return "gb_mid";
                    else
                        return "gb_mid_wide";
                case the_width >= 400:
                    if(the_height >= 200)
                        return "gb_small5";
                    else
                        return "gb_small5_wide";
                case the_width >= 300:
                    if(the_height >= 100)
                        return "gb_small4";
                    else
                        return "gb_small4_wide";
                case the_width >= 280: //for Mobiles
                    if(the_height >= 100)
                        return "gb_small4";
                    else
                        return "gb_small4_wide";
                case the_width >= 200:
                    if(the_height >= 100)
                        return "gb_small3";
                    else
                        return "gb_small3_wide";
                case the_width >= 100:
                    if(the_height >= 50)
                        return "gb_small2";
                    else
                        return "gb_small2_wide";
                default:
                    return "gb_small1";
            }
        }
    }
}(jQuery));