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
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <!-- <link href="{{ asset('/frontend/css/style-1.css')}}" rel="stylesheet"> -->
        <?php
        $newPageRoutes = array('parent/career-detail/{slug}/{teenId}', 'parent/learning-guidance/{teenUniqueId}');
        if (!in_array(Route::getFacadeRoot()->current()->uri(), $newPageRoutes)) { ?>
        <link href="{{ asset('/frontend/css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/custom.css')}}" rel="stylesheet">
        <?php } ?>
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
        <!-- <div class="loader init-loader">
            <div class="cont_loader">
                <div class="img1"></div>
                <div class="img2"></div>
            </div>
        </div> -->
        <nav>
            <div class="container">
                <div class="logo pull-left">
                    <a href="@if(Auth::guard('parent')->check()) {{ url('parent/home') }} @else {{ url('/') }} @endif" title="Dashboard">
                        <img src="{{ Storage::url('img/logo.png') }}" alt="{{ trans('labels.appname') }}">
                    </a></div>
                    <div class="menu-toggle pull-right">
                        <ul class="nav-bar clearfix">
                            <li class="n-user submenu-container">
                                <?php
                                    $photo = (isset(Auth::guard('parent')->user()->p_photo)) ? Auth::guard('parent')->user()->p_photo : '';
                                    if (isset($photo) && $photo != '' && Storage::size(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo) > 0) {
                                        $profilePicUrl = Storage::url(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo);
                                    } else {
                                        $profilePicUrl = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                                    }                                                                                       
                                ?>
                                <a href="javascript:void(0);"><i class="i-user"><img src="{{ $profilePicUrl }}" alt=""></i></a>
                                <div class="submenu">
                                    <div class="user-snapshot">
                                        <div class="user-avatar"><img src="{{ $profilePicUrl }}" alt=""></div>
                                        <div class="user-name">
                                            <h2>{{ Auth::guard('parent')->user()->p_first_name }} {{ Auth::guard('parent')->user()->p_last_name }}</h2>
                                            <p>{{ Auth::guard('parent')->user()->p_email }}</p>
                                        </div>
                                    </div>
                                    <div class="btns">
                                       <a href="javascript:void(0);" title="Profile" class="btn btn-primary btn-small text-uppercase">My Profile</a>
                                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary btn-small text-uppercase">Sign out</a>
                                        <form id="logout-form" action="{{ url('/parent/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </div>
                            </li>
                            <li class="n-coin submenu-container">
                                <a href="javascript:void(0);">
                                    <span class="coins"></span>
                                    <span id="user_procoins">{{ (Auth::guard('parent')->user()->p_coins > 0) ? number_format(Auth::guard('parent')->user()->p_coins) : 'No Coins' }}</span>
                                </a>
                                <div class="submenu">
                                    <h2>My ProCoins</h2>
                                    <div class="btns">
                                        <a href="{{ url('/parent/get-gift-coins') }}" class="btn btn-success btn-small text-uppercase btn-primary">Gift</a>
                                        <a href="{{ url('/parent/my-coins') }}" class="btn btn-success btn-small text-uppercase btn-primary">Buy</a>
                                        <a href="{{ url('/parent/get-consumption') }}" class="btn btn-success btn-small text-uppercase btn-primary">History</a>
                                    </div>
                                </div>
                            </li>
                            <li class="n-menu"><a href="javascript:void(0);" class="menu-toggler"><i class="icon-menu"></i></a></li>
                        </ul>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-container">
                    <ul>
                        <?php $inArrayRoute = ['parent/my-challengers', 'parent/my-challengers-research/{professionId}', 'parent/my-challengers-accept/{professionId}/{teenId}', 'parent/level4-activity/{professionId}/{teenId}', 'parent/level4-play-more/{professionId}/{teenId}','parent/level4-intermediate-activity/{professionId}/{templateId}/{teenId}', 'parent/level4-advance/{professionId}/{teenId}', 'parent/level4-advance-step2/{professionId}/{typeid}/{teenId}']; ?>
                        <li><a href="{{ url('parent/my-challengers') }}" title="My Challengers" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), $inArrayRoute) ? 'active' : ''}}" >Challengers</a></li>
                        <li><a href="{{ url('/parent/update-profile') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'parent/update-profile') ? 'active' : '' }}" title="Profile">Profile</a></li>
                        <li><a href="{{ url('/parent/home') }}" title="My Connects" class="{{ (Route::getFacadeRoot()->current()->uri() == 'parent/home') ? 'active' : '' }}" >Connects</a></li>
                        <li><a href="{{ url('/parent/progress') }}" title="Progress" class="{{ (Route::getFacadeRoot()->current()->uri() == 'parent/progress') ? 'active' : '' }}" >Progress</a></li>
                        <li><a href="{{ url('/parent/my-coins') }}" title="My Coins" class="{{ (Route::getFacadeRoot()->current()->uri() == 'parent/my-coins') ? 'active' : '' }}" >ProCoins</a></li>
                    </ul>
                    <a href="#" class="menu-close"><i class="icon-close"></i></a>
                </div>
            </div>
        </nav>
    </div>
    @yield('content')
    <a class="back-to-top bounce" href="#" title="Back to top"><img src="{{ Storage::url('img/arrow-up.png') }}" alt="back to top"></a>
        <footer>
            <div class="container">
                <div class="left">
                    <ul class="links">
                        <li><a href="{{ url('about-us') }}" title="About Us">About</a></li>
                        <li><a href="{{ url('contact-us') }}" title="Contact Us">Contact</a></li>
                        <li><a href="{{ url('privacy-policy') }}" title="Privacy Policy">Privacy</a></li>
                        <li><a href="{{ url('terms-condition') }}" title="Terms & Conditions">Terms & Conditions</a></li>
                    </ul>
                    <ul class="links">
                        <li><span>&copy; 2018 ProTeen</span></li>
                        <!-- <li><span>A <strong>UNIDEL</strong> COMPANY</span></li> -->
                        <li><span>A <strong><a href="https://www.unidel-group.com/" target="_blank" title="Unidel Company">UNIDEL</a></strong> Company</span></li>
                    </ul>                      
                </div>
                <div class="right">
                    <ul class="social">
                        <li><a href="https://www.facebook.com/proteenlife/" target="_blank"><i class="icon-facebook"></i></a></li>
                        <li><a href="https://twitter.com/ProTeenLife" target="_blank"><i class="icon-twitter"></i></a></li>
                        <li><a href="https://plus.google.com/109414106711493074923" target="_blank"><i class="icon-google"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/proteen-life" target="_blank"><i class="icon-linkdin"></i></a></li>
                    </ul>
                </div>
            </div>
        </footer>
        @yield('footer')
        <div id="loading-wrapper">
            <!-- <div id="loading-text"><span>Loading...</span></div> -->
            <div id="loading-content"><img src="{{ Storage::url('img/Bars.gif') }}"></div>
        </div> 
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js')}}"></script>

        <script src="{{ asset('frontend/js/comman.js')}}"></script>
        <script src="{{ asset('js/general.js') }}"></script>

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
