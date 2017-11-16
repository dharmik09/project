$(document).ready(function () {
    var rocketImage = $('.menu-rocket');
    var activatedLink = $('.main-menu li a.active');
    $('.main-menu li a').mouseover(function(event) {
        var x = $(this).offset();
        rocketImage.css({
            top: x.top,
            left: x.left-rocketImage.width()*1.2
        });
    });

    $('.main-menu li a').mouseleave(function(event) {
            var activeLinkPostion = activatedLink.offset();
            rocketImage.css({
                top: activeLinkPostion.top,
                left: activeLinkPostion.left-rocketImage.width()*1.2
            });
    });

    $('.menu-toggler').click(function(event) {
        $('.main-menu').addClass('active');
        setTimeout(function(){
            var activeLinkPostion = activatedLink.offset();

            rocketImage.css({
                top: activeLinkPostion.top,
                left: activeLinkPostion.left-rocketImage.width()*1.2
            });
        }, 600);
    });
    $('.menu-close').click(function(event) {
        $('.main-menu').removeClass('active');
    });
});
