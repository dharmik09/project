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
        <link href="{{ asset('/frontend/css/bootstrap-material-design.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/ripples.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.structure.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery-ui.theme.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/jquery.mCustomScrollbar.min.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/owl.carousel.css')}}" rel="stylesheet" >
        <link href="{{ asset('/frontend/css/font.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/style.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('/frontend/css/custom.css')}}" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        @yield('header')

    </head>

    <body>
        <nav>
            <div class="container">
                <div class="logo pull-left"><a href="@if(Auth::guard('sponsor')->check()) {{ url('sponsor/home') }} @else {{ url('/') }} @endif"><img src="{{ Storage::url('img/logo.png') }}" alt=""></a></div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <li class="n-user submenu-container">
                            <?php
                                $photo = (isset(Auth::guard('sponsor')->user()->sp_photo)) ? Auth::guard('sponsor')->user()->sp_photo : '';
                                if (isset($photo) && $photo != '' && Storage::size(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . $photo) > 0) {
                                    $profilePicUrl = Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . $photo);
                                } else {
                                    $profilePicUrl = Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                                }                                                                                       
                            ?>
                            <a href="javascript:void(0);"><i class="i-user"><img src="{{ $profilePicUrl }}" alt=""></i></a>
                            <div class="submenu">
                                <div class="user-snapshot">
                                    <div class="user-avatar"><img src="{{ $profilePicUrl }}" alt=""></div>
                                    <div class="user-name">
                                        <h2>{{ Auth::guard('sponsor')->user()->sp_company_name }}</h2>
                                        <p>{{ Auth::guard('sponsor')->user()->sp_email }}</p>
                                    </div>
                                </div>
                                <div class="btns"><a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary btn-small text-uppercase">Sign out</a>
                                    <form id="logout-form" action="{{ url('/sponsor/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
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
                        <li><a href="{{ url('sponsor/update-profile') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'sponsor/update-profile') ? 'active' : '' }}" title="My Profile">My Profile</a></li>
                        <li><a href="{{ url('sponsor/home') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'sponsor/home') ? 'active' : '' }}" title="Progress">Progress</a></li>
                        <li><a href="{{ url('sponsor/my-coins') }}" class="{{ (Route::getFacadeRoot()->current()->uri() == 'sponsor/my-coins') ? 'active' : '' }}" title="My ProCoins">My ProCoins</a></li>
                    </ul>
                    <a href="#" class="menu-close"><i class="icon-close"></i></a>
                    
                </div>
            </div>
        </nav>
        @yield('content')
        <footer>
            <div class="container">
                <div class="left">
                    <ul class="links">
                        <li><a href="{{ url('contact-us') }}" title="Contact Us">Contact</a></li>
                        <li><a href="{{ url('privacy-policy') }}" title="Privacy Policy">Privacy</a></li>
                        <li><a href="{{ url('terms-condition') }}" title="Terms & Conditions">Terms & Conditions</a></li>
                    </ul>
                    <ul class="links">
                        <li><span>&copy; 2018 ProTeen</span></li>
                        <li><span>A <strong>UNIDEL</strong> COMPANY</span></li>
                    </ul>                      
                </div>
                <div class="right">
                    <ul class="social">
                        <li><a href="#"><i class="icon-search"><img src="{{ Storage::url('img/search-icon.png') }}" alt="search" class="icon-img"><img src="{{ Storage::url('img/search-icon-hover.png') }}" alt="search" class="icon-hover"></i></a></li>
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
            <div id="loading-text"><span>Loading...</span></div>
            <div id="loading-content"></div>
        </div> 
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('frontend/js/chosen.js')}}"></script>
        <script src="{{ asset('frontend/js/comman.js')}}"></script>
        <script src="{{ asset('js/general.js') }}"></script>
        <script src="{{ asset('frontend/js/owl.carousel.min.js')}}"></script>
        <!-- SlimScroll -->
        <script src="{{ asset('frontend/js/jquery.mCustomScrollbar.min.js') }}"></script>
        <script src="{{ asset('frontend/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{ asset('frontend/plugins/fastclick/fastclick.min.js')}}"></script>
        <script src="{{ asset('frontend/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery.autocomplete.min.js') }}"></script>
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

                if(search_keyword.length > 2){
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

        </script>

        @yield('script')

    </body>

</html>
