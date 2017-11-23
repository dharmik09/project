<!DOCTYPE html>
<html lang="en">
    <head>
        <META charset="utf-8">
        <META http-equiv="X-UA-Compatible" content="IE=edge">
        <META content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <META Http-Equiv="Cache-Control" Content="no-cache"/>
        <META Http-Equiv="Pragma" Content="no-cache"/>
        <META Http-Equiv="Expires" Content="0"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{trans('labels.appname')}}</title>
        <link href="{{ asset('/frontend/css/bootstrap.min.css')}}" rel="stylesheet" >
        <link href="{{ asset('/frontend/css/owl.carousel.css')}}" rel="stylesheet" >        
        <link href="{{ asset('/frontend/css/bootstrap-material-design.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/ripples.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.structure.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.theme.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery.mCustomScrollbar.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/font.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/custom.css')}}" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        @yield('header')
        <style>
            .chat_initial_hide{display: none !important;}
            .loader,
            .overlay_menu_dropdown{position:fixed;top:0;bottom:0;left:0;right:0;z-index:8;}
            .loader{z-index:4000;}
            .loader.init-loader{z-index:10000;background:rgba(15,8,26,1);transition:all 2s;}
            .fadeLoader{opacity:0 !important;}
            .container_padd{opacity:0;transition:all 2s;display:none;}
            .container_padd.active{opacity:1 !important;}
            .loader .cont_loader_padd,
            .loader .cont_loader{position:relative;width:100%;height:100%;}
            .loader .cont_loader .img1,
            .loader .cont_loader .img2{background-size:150px;background-repeat: no-repeat;background-attachment: fixed;background-position: center;height:100%;width:100%;position:absolute;top:0;left:0;}
            .loader .cont_loader .img1{background-image: url('/frontend/images/load_bar.png');-webkit-animation:loader_spin 0.8s linear infinite;-moz-animation:loader_spin 0.8s linear infinite;-o-animation:loader_spin 0.8s linear infinite;animation:loader_spin 0.8s linear infinite; }
            .loader .cont_loader .img2{background-image: url('/frontend/images/load_hero.png');}
            @-webkit-keyframes loader_spin { 100% { -webkit-transform: rotate(360deg); } }
            @-moz-keyframes loader_spin { 100% { -webkit-transform: rotate(360deg); -moz-transform:rotate(360deg); transform:rotate(360deg); } }
            @-o-keyframes loader_spin { 100% { -webkit-transform: rotate(360deg); -o-transform:rotate(360deg); transform:rotate(360deg); } }
            @keyframes loader_spin { 100% { -webkit-transform: rotate(360deg); -moz-transform:rotate(360deg); -o-transform:rotate(360deg); transform:rotate(360deg); } }
            @media(min-width:992px){
                .noScroll{overflow: hidden !important;padding-right:17px;}
            }
        </style>
    </head>

    <body class="noScroll">
        @if(Auth::guard('parent')->check())
                <div class="loader init-loader">
            <div class="cont_loader">
                <div class="img1"></div>
                <div class="img2"></div>
            </div>
        </div>
        <div class="navbar navbar-cst">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                </button>
                <a class="navbar-brand" href="{{url('parent/dashboard')}}"><img src="{{ asset('frontend/images/proteen_logo.png')}}" alt=""></a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav nav_basic navbar-nav navbar-right home_page_navigation non_teen">
                    <?php $inArrayRoute = ['parent/myChallengers', 'parent/myChallengersResearch/{professionId}', 'parent/myChallengersAccept/{professionId}/{teenId}', 'parent/level4Activity/{professionId}/{teenId}', 'parent/level4PlayMore/{professionId}/{teenId}','parent/level4IntermediateActivity/{professionId}/{templateId}/{teenId}', 'parent/level4Advance/{professionId}/{teenId}', 'parent/level4AdvanceStep2/{professionId}/{typeid}/{teenId}']; ?>
                    <li class="{{(in_array(Route::getFacadeRoot()->current()->uri(), $inArrayRoute)) ? 'active':''}}"><a href="{{url('parent/myChallengers')}}" >My Challengers</a></li>
                    <li class="{{(Route::getFacadeRoot()->current()->uri() == 'parent/update-profile')? 'active':''}}"><a href="{{url('/parent/update-profile')}}" >My Profile</a></li>
                    <li class="{{(Route::getFacadeRoot()->current()->uri() == 'parent/dashboard')? 'active':''}}"><a href="{{url('/parent/dashboard/')}}">My Teen</a></li>
                    <li class="{{(Route::getFacadeRoot()->current()->uri() == 'parent/progress')? 'active':''}}"><a href="{{url('/parent/progress/')}}">Progress</a></li>
                    <li class="{{(Route::getFacadeRoot()->current()->uri() == 'parent/pricing')? 'active':''}}"><a href="{{url('/parent/mycoins/')}}">My ProCoins</a></li>
                    <li class="user_avatar">
                        <a href="#" class="drop_down_menu">
                           <span class="user_detail_name">{{Auth::guard('parent')->user()->p_first_name}}</span>
                            <?php
                             $photo = Auth::guard('parent')->user()->p_photo;
                             
                             
                             if ($photo != '' && file_exists(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo)) {
                                $profilePicUrl = asset(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo);
                               } else {
                                $profilePicUrl = asset(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                               }                                                                                       
                            ?>
                            <img class="user_detail_image" src="{{ asset($profilePicUrl)}}" alt="">

                        </a>
                        <ul class="navigation_prime menu_dropdown" style="display: none;">
                            <form id="logout-form" action="{{ url('/parent/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <li>
                                <a href="{{url('/parent/logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
        @else
       <div class="navbar navbar-cst home_header_navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                    <a class="navbar-brand" href="/"><img src="{{asset('frontend/images/proteen_logo.png')}}" alt=""></a>
                    <span class="brand_tag sticky_tag">Explore<i class="fa fa-circle" aria-hidden="true"></i>Experience<i class="fa fa-circle" aria-hidden="true"></i>Enjoy</span>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                    <ul class="nav nav_basic navbar-nav navbar-right home_page_navigation">
                        <li><a href="{{url('about')}}">About</a></li>
                        <li><a href="{{url('watchVideo')}}" class="intro_video">Watch</a></li>
                        <li>
                            <a href="#" class="drop_down_menu">
                                <span class="user_detail_name play_menu">Play <i class="fa fa-caret-down" aria-hidden="true"></i></span>
                            </a>
                            <ul class="navigation_prime menu_dropdown product_drop" style="display: none;">
                                <li><a href="{{url('teenager')}}">Teen</a></li>
                                <li><a href="{{url('parent')}}">Parent</a></li>
                                <li><a href="{{url('counselor')}}">Mentor</a></li>
                                <li><a href="{{url('school')}}">School</a></li>
                                <li><a href="{{url('sponsor')}}">Enterprise</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @endif
        @yield('content')
        @if(Auth::guard('parent')->check())
        <footer class="primary_footer tnc">
            <div class="container">
                <div class="copyright tandc">

                </div>
                <div class="social_footer">

                </div>
            </div>
        </footer>
        @else
        <footer class="primary_footer tnc">
            <div class="container">
                <div class="copyright tandc">
                    <span><a href="{{url('contact')}}"  class="terms">Contact</a></span>
                    <span><a href="{{url('term')}}" class="terms">Terms &amp; Conditions</a></span>
                    <span><a href="{{url('privacy')}}" class="terms">Privacy Policy</a></span>
                    <span>(c) 2017 All Rights Reserved</span>
                </div>
                <div class="social_footer">
                    <a href="https://www.linkedin.com/company/proteen-life" target="_blank" class="linkedin_footer"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
                    <a href="https://www.facebook.com/proteenlife/" target="_blank" class="facebook_footer"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
                    <a href="https://plus.google.com/109414106711493074923" target="_blank" class="google_plus_footer"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
                    <a href="https://twitter.com/ProTeenLife" target="_blank" class="twitter_footer"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
                </div>
            </div>
        </footer>
        @endif
        @yield('footer')
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js')}}"></script>

        <script src="{{ asset('frontend/js/comman.js')}}"></script>

        <!-- SlimScroll -->
        <script src="{{ asset('frontend/js/jquery.mCustomScrollbar.min.js') }}"></script>
        <script src="{{ asset('frontend/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{ asset('frontend/plugins/fastclick/fastclick.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery.validate.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert-dismissable').fadeOut();
                }, 5000);

                $('[data-toggle="tooltip"]').tooltip();
            });
            $('.onlyNumber').on('keyup', function() {
                this.value = this.value.replace(/[^0-9]/gi, '');
            });

            $('#search_box').on('keyup', function() {
                var search_keyword = $('#search_box').val();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'search_keyword=' + search_keyword;

                if (search_keyword.length > 2) {
                    $.ajax({
                        type: 'POST',
                        data: form_data,
                        dataType: 'html',
                        url: "{{ url('/teenager/globalSearch')}}",
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        cache: false,
                        success: function(data) {
                            $("#searchData").html(data);
                        }
                    });
                }
            });
            var FACEBOOK_CLIENT_ID = '<?php echo Config::get('constant.FACEBOOK_CLIENT_ID') ?>';
        </script>
<script>
$(window).bind("load", function() {
    $('.init-loader').addClass("fadeLoader");
    $('.container_padd').show();
    $('.container_padd').addClass("active");
    setTimeout(function(){
        $('.init-loader').remove();
    },2500);
    setTimeout(function(){
        $('body').removeClass("noScroll");
    },2000);

});
</script>

         @yield('script')
    </body>

</html>
