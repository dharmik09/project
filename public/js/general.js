$(document).ready(function() {

    // Navigation dropdown script
    $('.submenu-container > a').click(function(event) {
        $('.submenu-container').removeClass('active');
        $(this).closest('.submenu-container').addClass('active');
    });
    $(document).mouseup(function(e) {
        var c = $(".nav-bar");
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            $('.submenu-container').removeClass('active');
        }
    });


    var rocketImage = $('.menu-rocket');
    var activatedLink = $('.main-menu li a.active');
    $('.main-menu li a').mouseover(function(event) {
        var x = $(this).offset();
        rocketImage.css({
            top: x.top,
            left: x.left - rocketImage.width() * 1.2
        });
    });

    $('.main-menu li a').mouseleave(function(event) {
        var activeLinkPostion = activatedLink.offset();
        rocketImage.css({
            top: activeLinkPostion.top,
            left: activeLinkPostion.left - rocketImage.width() * 1.2
        });
    });

    $('.menu-toggler').click(function(event) {
        $('.main-menu').addClass('active');
        setTimeout(function() {
            var activeLinkPostion = activatedLink.offset();

            rocketImage.css({
                top: activeLinkPostion.top,
                left: activeLinkPostion.left - rocketImage.width() * 1.2
            });
        }, 600);
    });
    $('.menu-close').click(function(event) {
        $('.main-menu').removeClass('active');
    });
    $(".member-info .detail").each(function(i) {
        len = $(this).text().length;
        if (len > 10) {
            $(this).text($(this).text().substr(0, 80) + '...');
        }
    });


    // Popover section
    $('.custompop').popover({
        html: true,
        content: function() {
            return $($(this).data('popover-content')).html();
        }
    });

    $('.custompop').click(function (e) {
        e.stopPropagation();
        $('.custompop').not(this).popover('hide');
    });

    $(document).click(function (e) {
        if (($('.popover').has(e.target).length == 0)) {
            $('.custompop').popover('hide');
        }
    });

    $('body').on('hidden.bs.popover', function (e) {
        $(e.target).data("bs.popover").inState.click = false;
    });
    $('body').on('click', '.popover-closer', function(event) {
        $('.custompop').popover('hide');
    });
    // $('.popover-shareId').popover({
    //     html: true,
    //     content: function() {
    //         return $('.shareContent').html();
    //     }
    // });
    // var isVisible = false;
    // var clickedAway = false;

    // $('.popoverThis').popover({
    //     html: true,
    //     trigger: 'manual'
    // }).click(function(e) {
    //     $(this).popover('show');
    //     $('.popover-content').append('<a class="close" style="position: absolute; top: 10px; right: 0px;"><i class="icon-close"></i></a>');
    //     clickedAway = false
    //     isVisible = true
    //     e.preventDefault()
    // });
    // //social share popover
    // $('.popoverShare').popover({
    //     html: true,
    //     trigger: 'manual'
    // }).click(function(e) {
    //     $(this).popover('show');
    //     $('.popover-content').addClass('socialmedia-icon');
    //     clickedAway = false
    //     isVisible = true
    //     e.preventDefault()
    // });

    // $(document).click(function(e) {
    //     if (isVisible & clickedAway) {
    //         $('.popoverThis, .popoverShare').popover('hide')
    //         isVisible = clickedAway = false
    //     } else {
    //         clickedAway = true
    //     }
    // });
    // $('.shareId').popover({
    //     html: true,
    //     content: function() {
    //         return $('.shareContent').html();
    //     }
    // });
    // $('.popoverThis').popover();
    // $('.popoverShare').popover();

    // $('.popoverThis').on('click', function(e) {
    //     $('.popoverThis').not(this).popover('hide');
    // });
    // $('.popoverShare').on('click', function(e) {
    //     $('.popoverShare').not(this).popover('hide');
    // });
    // var ProgressBar = require('progressbar.js')
    // var line = new ProgressBar.Line('#container');

    //progressbar
    $(function() {
        $(window).scroll(function() {
            $(".progress-bar:not(.animated)").each(function() {
                if ($(this).is(':visible')) {
                    var progressBar = $(this); // $(this) would work too
                    progressBar.animate({
                        width: progressBar.data('width') + '%'
                    }, 1500);
                    progressBar.addClass('animated');
                }
            });
        });
    });
});