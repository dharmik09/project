// Displaying image while uploading....
var input_file = '.upload_image input';

function readURL(input_file) {
    if (input_file.files && input_file.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var fileType = input_file.files[0];
            var alertMsg = '';
            if (fileType.type == 'image/jpeg' || fileType.type == 'image/jpg' || fileType.type == 'image/png' || fileType.type == 'image/bmp') {
                if (input_file.files[0].size > 3000000) {
                    alertMsg = "File size is too large. Maximum 3MB allowed";
                    //alert("File size is too large. Maximum 3MB allowed.");
                    $(this).val('');
                } else {
                    $(input_file).siblings('.placeholder_image').find('img').attr('src', e.target.result);
                }
            } else {
                alertMsg = "File type not allowed";
                //alert("File type not allowed");
                $(input_file).val('');
            }
            if (alertMsg !== '') {
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+alertMsg+'</span></div></div></div>');
            } else {
                $("#errorGoneMsg").html('');
            }
        };
        reader.readAsDataURL(input_file.files[0]);
    }
}

jQuery(document).ready(function($) {
    $('.upload_image .placeholder_image').click(function() {
        $(this).siblings('input').trigger('click');
        $(this).find('img').show().addClass('class_name');
        $(this).find('p').hide();
    });

    /*This script for teenager menu */
    var win_width = $(window).width();
    $('.drop_down_menu').click(function() {
        $('ul.menu_dropdown').slideToggle(300);
        $('ul.menu_dropdown').toggleClass('open');
        $(this).toggleClass('active');
        if(!$(this).parents('.navbar-nav').hasClass('home_page_navigation')){
            $('.navbar-collapse').collapse('hide');
        }
    });        

    $(document).mouseup(function (e){
        if(!$('.navbar-nav').hasClass('home_page_navigation')){
            if($('ul.menu_dropdown').hasClass('open')){
                var container = $("ul.menu_dropdown");
                if (!container.is(e.target) && container.has(e.target).length === 0){
                    $('ul.menu_dropdown').removeClass('open');
                    container.slideUp(300);
                    $('.drop_down_menu').removeClass('active');
                }
            }else if($('.navbar-collapse').hasClass('in')){
                var container = $(".navbar-collapse");
                if (!container.is(e.target) && container.has(e.target).length === 0){
                    $('.navbar-collapse').collapse('hide');
                }
            }
        }else{
            if($('html').width()>767){
                if($('.menu_dropdown').hasClass('open')){
                    var container = $(".menu_dropdown").parents('li');
                    if (!container.is(e.target) && container.has(e.target).length === 0){
                        $('ul.menu_dropdown').slideUp();
                        $('.drop_down_menu').removeClass('active');
                        $('ul.menu_dropdown').removeClass('open');
                    }
                }
            }else{
                if($('.navbar-collapse').hasClass('in')){
                    var container = $(".navbar-collapse");
                    if (!container.is(e.target) && container.has(e.target).length === 0){
                        $('ul.menu_dropdown').slideUp();
                        $('.drop_down_menu').removeClass('active');
                        $('ul.menu_dropdown').removeClass('open');
                        $('.navbar-collapse').collapse('hide');
                    }
                }
            }
        }
    });
    
    /*Search Box_trick*/
    $('#mobile_search_id').focusin(function() {
        $(this).parent('.mobile_search').addClass('full_width');
    });
    $('#mobile_search_id').focusout(function() {
        $(this).parent('.mobile_search').removeClass('full_width');
    });

    /*Centering the modal.....*/
    function setModalMaxHeight(element) {
        this.$element = $(element);
        this.$content = this.$element.find('.modal-content');
        var borderWidth = this.$content.outerHeight() - this.$content.innerHeight();
        var dialogMargin = $(window).width() < 768 ? 20 : 60;
        var contentHeight = $(window).height() - (dialogMargin + borderWidth);
        var headerHeight = this.$element.find('.modal-header').outerHeight() || 0;
        var footerHeight = this.$element.find('.modal-footer').outerHeight() || 0;
        var maxHeight = contentHeight - (headerHeight + footerHeight);
        this.$content.css({
            'overflow': 'hidden'
        });
        this.$element.find('.modal-body').css({
            'max-height': maxHeight,
            'overflow-y': 'auto'
        });
    }
    $('.modal').on('show.bs.modal', function() {
        $(this).show();
        setModalMaxHeight(this);
    });

    $(".cst_upload_simple_btn").on("click", function() {
        alert();
        $(".cst_upload_simple").trigger('click');
    });

    /*Search result showing*/
    $(".search_input").focus(function() {
        //$('.search_result_area').show();
        //$('.search_area').slideDown(400);
        //$('body').append('<div class="search_overlay"></div>')
    });
    $('.search_result_area').on('click', function(e) {
        if (e.target == this) {
            $('.search_result_area,.search_area').fadeOut(400);
        }
    });
    $('body').on('click', '.search_overlay', function() {
        $('.search_result_area,.search_area,.search_overlay').fadeOut(400);
    });
    $('.search_area').mCustomScrollbar({
        theme: 'minimal'
    });

    var audio1 = $("#audio_click_1")[0];

    var audio2 = $("#audio_click_2")[0];

    var audio3 = $("#audio_click_3")[0];

    $(".click_sound_1").click(function() {
        audio1.play();
    });
    $(".click_sound_2").click(function() {
        audio2.play();
    });
    $(".click_sound_3").click(function() {
        audio3.play();
    });

    $("body").on("click", ".file_type_button span.btn", function() {
        $(this).siblings('input').trigger('click');
    });

    $("body").on("click", ".cst_ans_radio label", function() {
        $(this).siblings('input').trigger('click');
    });
    /*Hint image pop-Up*/

    $('body').on('click', '.hero .hero_inner img , .pop_up_me, .user_fav_icon img', function(event) {
        var image_src = $(this).attr('src');
        $('#hint_image_modal').modal('show');
        $('#hint_image_modal img').attr('src', image_src);
    });

});
/*Image upload simple button*/
var span = document.getElementsByClassName('upload_path');
var uploader = document.getElementsByName('upload');
for (item in uploader) {
    uploader[item].onchange = function() {
        span[0].innerHTML = this.files[0].name;
    }
}