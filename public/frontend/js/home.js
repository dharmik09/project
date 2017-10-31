jQuery(document).ready(function($) {
	// function centralize_container(){
 //        var contents_height = $('.centerlize').height();
 //        var window_height = $(window).height();
 //        var top_pos = (window_height-contents_height)/2;
 //        if(window_height > (contents_height+170)){
 //            $('.centerlize').css({"position":"absolute","top":top_pos+"px","width":"100%"});
 //        }
 //    }
 //    $(window).resize(function() {
 //        centralize_container();
 //    });
 //    centralize_container();

    /*This script for teen age menu */
    var win_width = $(window).width();
    $('.drop_down_menu').click(function() {
        $('ul.menu_dropdown').slideToggle(300);
        $('ul.menu_dropdown').toggleClass('open');
        $(this).toggleClass('active');
    });
    $(document).mouseup(function (e){
        if($('html').width()>767){
            if($('.menu_dropdown').hasClass('open')){
                var container = $(".menu_dropdown").parents('li');
                if (!container.is(e.target) && container.has(e.target).length === 0){
                    $('ul.menu_dropdown').slideToggle();
                    $('.drop_down_menu').toggleClass('active');
                    $('ul.menu_dropdown').toggleClass('open');
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
    });
});
/*Animation*/
//var isWebkit = /Webkit/i.test(navigator.userAgent),
//    isChrome = /Chrome/i.test(navigator.userAgent),
//    isMobile = !!("ontouchstart" in window),
//    isAndroid = /Android/i.test(navigator.userAgent),
//    isIE = document.documentMode;
//
//function r(min, max) {
//    return Math.floor(Math.random() * (max - min + 1)) + min;
//}
//
//$.Velocity.defaults.easing = "easeInOutsine";
//
//var dotsCount,
//    dotsHtml = "",
//    $count = $("#count"),
//    $dots;
//
//if (window.location.hash) {
//    dotsCount = window.location.hash.slice(1);
//} else {
//    dotsCount = isMobile ? (isAndroid ? 40 : 60) : (isChrome ? 150 : 125);
//}
//
//function getRandomInt() {
//    return Math.floor(Math.random() * (24 - 1 + 1)) + 1;
//}
//
//for (var i = 0; i < dotsCount; i++) {
//        //dotsHtml += '<div class="dot"><img src="frontend/images/home/' + getRandomInt() + '.png" alt=""></div>';    
//        dotsHtml += '<div class="dot"> <div class="inner_animation img-' + getRandomInt() + '"></div></div>';
//}
//
//$dots = $(dotsHtml);
//
//var $container = $("#container"),
//    $browserWidthNotice = $("#browserWidthNotice"),
//    $welcome = $("#welcome");
//
//var screenWidth = window.screen.availWidth,
//    screenHeight = window.screen.availHeight,
//    chromeHeight = screenHeight - (document.documentElement.clientHeight || screenHeight);
//
//var translateZMin = -200,
//    translateZMax = 60;
//
//var containerAnimationMap = {
//    perspective: [50, 50]
//};
//
//if ((document.documentElement.clientWidth / screenWidth) < 0.80) {
//    $browserWidthNotice.show();
//}
//
//$container.css("perspective-origin", screenWidth / 2 + "px " + ((screenHeight * 0.5) - chromeHeight) + "px");
//
//$dots
//    .velocity({
//        translateX: [
//            function() {
//                return "+=" + r(-screenWidth / 1.5, screenWidth / 1.5) },
//            function() {
//                return r(0, screenWidth) }
//        ],
//        translateY: [
//            function() {
//                return "+=" + r(-screenHeight / 2.75, screenHeight / 2.75) },
//            function() {
//                return r(0, screenHeight) }
//        ],
//        translateZ: [
//            function() {
//                return "+=" + r(translateZMin, translateZMax) },
//            function() {
//                return r(translateZMin, translateZMax) }
//        ]
//    }, {
//        duration: 20000,
//        loop: true,
//        complete: function() {}
//    })
//    .appendTo($container);
