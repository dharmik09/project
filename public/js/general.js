$(document).ready(function () {

    // Navigation dropdown script
    $('.submenu-container > a').click(function (event) {
        $('.submenu-container').removeClass('active');
        $(this).closest('.submenu-container').addClass('active');
    });
    $(document).mouseup(function (e) {
        var c = $(".nav-bar");
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            $('.submenu-container').removeClass('active');
        }
    });


    var rocketImage = $('.menu-rocket');
    var activatedLink = $('.main-menu li a.active');
    $('.main-menu li a').mouseover(function (event) {
        var x = $(this).offset();
        rocketImage.css({
            top: x.top,
            left: x.left - rocketImage.width() * 1.2
        });
    });

    $('.main-menu li a').mouseleave(function (event) {
        var activeLinkPostion = activatedLink.offset();
        rocketImage.css({
            top: activeLinkPostion.top,
            left: activeLinkPostion.left - rocketImage.width() * 1.2
        });
    });

    $('.menu-toggler').click(function (event) {
        $('.main-menu').addClass('active');
        setTimeout(function () {
            var activeLinkPostion = activatedLink.offset();

            rocketImage.css({
                top: activeLinkPostion.top,
                left: activeLinkPostion.left - rocketImage.width() * 1.2
            });
        }, 600);
    });
    $('.menu-close').click(function (event) {
        $('.main-menu').removeClass('active');
    });
    $('.menu-close').click(function (e) {
        e.preventDefault();
    });
    $('.vol-on').click(function (){
        $(this).addClass('hide');
        $('.vol-off').removeClass('hide');
    })
    $('.vol-off').click(function (){
        $(this).addClass('hide');
        $('.vol-on').removeClass('hide');
    })
    $(".member-info .detail").each(function (i) {
        len = $(this).text().length;
        if (len > 10) {
            $(this).text($(this).text().substr(0, 80) + '...');
        }
    });
    if ($('.main-menu').hasClass('.active')) {
        $('body').addClass('fixedPosition');
    } else {
        $('body').removeClass('fixedPosition');
    }

    $('.menu-toggler').click(function () {
        $('body').addClass('fixedPosition')
    })
    $('.menu-close').click(function () {
        $('body').removeClass('fixedPosition');
    })
    // Popover section
    $('.custompop').popover({
        html: true,
        content: function () {
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
    $('body').on('click', '.popover-closer', function (event) {
        $('.custompop').popover('hide');
    });
    /*// add iframe url for a map
		function loadMap(iframeObject)
		{
			// if the iframe has no src or a blank src, and it has a data-src attribute
			if ( !(iframeObject.attr("src") && iframeObject.attr("src").length) && iframeObject.attr("data-src") )
			{
				iframeObject.attr("src", iframeObject.attr("data-src"));
			}
		}
		// scroll to a map
		function scrollToDiv(divID)
		{
			$("html, body").animate({
				scrollTop: $(divID).offset().top - ( $(".fixed-navigation").height() || 0 ) - 20
			}, 300);
		}
		// if a location hash is on the url, add active to the div.
		if ( location.hash && $(location.hash + ".sec-map").length )
		{
			$(location.hash + ".sec-map").addClass("active");
		}
		else
		{
			// otherwise, just make the first map active.
			$(".sec-map:first").addClass("active");
		}
		loadMap($(".sec-map.active iframe"));
		// contact page maps on click
		$(".contact-map-link").click(function(e){
			var myLink = $(this).attr("href")
			var targetMap = $( myLink.substr(myLink.indexOf("#")) );
			if ( targetMap.length )
			{
				e.preventDefault();
				loadMap(targetMap.children("iframe"));
				scrollToDiv(targetMap);
				$(".sec-map").not(targetMap).removeClass("active");
				targetMap.addClass("active");
			}
		});
		// contact page stop scrolling until clicked.
		$(".map-overlay").click(function(){
			$(this).hide();
		});
*/
 //loading page css
    $('#loading-wrapper').remove();
    $('body').removeClass('overflw-hide');
    //expand
     $('.expand').click(function(){
         //$(this).closest('.row').find('.sec-wrap').slideToggle("slow");
        $('.sec-wrap-1').slideToggle("slow");
    })
    $('.expand-1').click(function(){
         //$(this).closest('.row').find('.sec-wrap').slideToggle("slow");
        $('.sec-wrap-2').slideToggle("slow");
    })
    $('.expand-2').click(function(){
         //$(this).closest('.row').find('.sec-wrap').slideToggle("slow");
        $('.sec-wrap-3').slideToggle("slow");
    })
    $('.expand-3').click(function(){
         //$(this).closest('.row').find('.sec-wrap').slideToggle("slow");
        $('.sec-wrap-4').slideToggle("slow");
    })
    $('.expand-4').click(function(){
         //$(this).closest('.row').find('.sec-wrap').slideToggle("slow");
        $('.sec-wrap-5').slideToggle("slow");
    })
    
    //progressbar
    $(function () {
        $(window).scroll(function () {
            $(".progress-bar:not(.animated)").each(function () {
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
