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
    $(".submenu-container > a").hover(function () {
        $('.submenu-container').removeClass('active');
        $(this).closest('.submenu-container').addClass('active');
        $('.submenu').removeClass('inactive');
    }, function () {
        $('.submenu-container').removeClass('inactive');
    });
    $(".submenu-container").mouseleave(function () {
        $('.submenu-container').removeClass('active');
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
    $('.vol-on').click(function () {
        $(this).addClass('hide');
        $('.vol-off').removeClass('hide');
    })
    $('.vol-off').click(function () {
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
    //$('#loading-wrapper').remove();
    setTimeout(function () {
        $('#loading-wrapper').hide();
    }, 2500);
    /*$('#loading-wrapper-sub').hide();
    }, 2500);*/
    $('body').removeClass('overflw-hide');
    //expand
    $('.expand').click(function () {
        $('.sec-wrap-1').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
    })
    $('.expand-1').click(function () {
        $('.sec-wrap-2').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
    })
    $('.expand-2').click(function () {
        $('.sec-wrap-3').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
    })
    $('.expand-3').click(function () {
        $('.sec-wrap-4').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
    })
    $('.expand-4').click(function () {
        $('.sec-wrap-5').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
    })
    $('.text-overflow').each(function(index, el) {
                var parent = $(el).closest('.full-text');
                var btn = parent.find('.read-more');
                var elementHt = parent.find('.text-full').outerHeight();
                if (elementHt > 70) {
                    btn.addClass('less');
                    btn.css('display', 'block');
                }
                btn.click(function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if ($(this).hasClass('less')) {
                        $(this).removeClass('less');
                        $(this).addClass('more');
                        $(this).text('Read Less');
                        $(this).attr('title', 'Read Less');
                        var ht = $(this).closest('.full-text').find('.text-full').outerHeight();
                        $(this).closest('.full-text').find('.text-overflow').animate({
                            'height': ht
                        });
                    } else {
                        $(this).addClass('less');
                        $(this).removeClass('more');
                        $(this).text('Read More');
                        $(this).attr('title', 'Read More');
                        $(this).closest('.full-text').find('.text-overflow').animate({
                            'height': '70px'
                        });
                    }
                });
            });
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
    $('.btnLoad').click(function () {
        $(".loader_con").show();
        var offset = $(this).val();
        //alert(offset);
        $('.loader_con').removeClass('gif-none');
        $('.btnLoad').removeClass('btn-none');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'loadmore_posts',
                offset1: offset
            },
            success: function (response) {
                var response = $.parseJSON(response);
                console.log(response);
                $('.loader_con').addClass('gif-none');
                $('.btnLoad').addClass('btn-none');
                $('#loadmore').append(response.html);
                $('.btnLoad').val(response.key);
                //$("#btnLoad").removeClass('loading');
                if (response.flag == 'set') {
                    // alert('hi');
                    $("#btnLoad").hide();
                }
                // alert(response.html);
            }
        });
    });
    //refresh loader
    $('.refresh-text').click(function () {
        $(".refresh-img").attr('src', "img/Reload.gif");
    });
    //accordian to top
    $(function () {
    $('#accordion').on('shown.bs.collapse', function (e) {
        var offset = $(this).find('.collapse.in').prev('.panel-heading');
        if(offset) {
            $('html,body').animate({
                scrollTop: $(offset).offset().top -0
            }, 500); 
        }
    }); 
});
});
/*$(document).ready(function () {
    // Handler for .ready() called.
    $('html, body').animate({
       $('#' + divToScrollTo).offset().top
    }, 'slow');
});*/
/*$(function(){
  // get hash value
  var hash = window.location.hash;
  // now scroll to element with that id
  $('html, body').animate({ scrollTop: $(hash).offset().top },2000);
});*/
if(window.location.hash) {
  // Fragment exists
    var hash = window.location.hash;
  // now scroll to element with that id
  $('html, body').animate({ scrollTop: $(hash).offset().top},2000);
} else {
  // Fragment doesn't exist
}
//menu close
$(document).keydown(function (e) {
    // ESCAPE key pressed
    if (e.keyCode == 27) {
        $('.main-menu').removeClass('active');
        $('body').removeClass('fixedPosition');
    }
});
