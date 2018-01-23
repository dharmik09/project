<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <!-- Bootstrap -->
        <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('css/owl.css')}}" rel="stylesheet">
        <link href="{{asset('css/magnific-popup.css')}}" rel="stylesheet">
        <link href="{{asset('css/aos.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        
        @stack('script-header')
        @yield('header')
    </head>
    <body>
        <nav>
            <div class="container">
                <div class="logo pull-left">
                	<a href="@if(Auth::guard('teenager')->check()) {{ url('teenager/home') }} @else {{ url('/') }} @endif">
                		<img src="{{ Storage::url('img/logo.png') }}" alt="{{ trans('labels.appname') }}">
                	</a>
                </div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <li class="n-user submenu-container">
                            <?php $user_profile_thumb_image = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url('uploads/teenager/thumb/'.Auth::guard('teenager')->user()->t_photo) : Storage::url('uploads/teenager/thumb/proteen-logo.png'); ?>
                            <a href="javascript:void(0);"><i class="i-user"><img src="{{ $user_profile_thumb_image }}" alt="user icon"></i><span class="badge">12</span></a>
                            <div class="submenu">
                                <div class="user-snapshot">
                                    <div class="user-avatar">
                                        <a href="{{ url('teenager/home') }}" title="My Dashboard">
                                            <img src="{{ $user_profile_thumb_image }}">
                                        </a>
                                    </div>
                                    <div class="user-name">
                                        <a href="{{ url('teenager/home') }}" title="My Dashboard">
                                            <h2>{{ Auth::guard('teenager')->user()->t_name }} {{ Auth::guard('teenager')->user()->t_lastname }}</h2>
                                            <p>{{ Auth::guard('teenager')->user()->t_email }}</p>
                                        </a>
                                    </div>
                                    <div class="vol-btn">
                                        <span class="vol-on"><img src="{{ Storage::url('img/icon-vol.png') }}" ></span>
                                        <span class="vol-off hide"><img src="{{ Storage::url('img/vol-mute.png') }}" ></span>
                                    </div>
                                </div>
                                <div class="btns">
                                    <a href="{{ url('/teenager/my-profile') }}" title="Profile" class="btn btn-primary btn-small text-uppercase">My Profile</a>
                                    <a href="{{ url('/teenager/chat') }}" class="btn btn-primary btn-small text-uppercase">Messages</a>
                                    <a href="{{ url('/teenager/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary btn-small text-uppercase">Sign out</a>
                                    <form id="logout-form" action="{{ url('/teenager/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="n-coin submenu-container">
                            <a href="javascript:void(0);"><span class="coins"></span>{{ (Auth::guard('teenager')->user()->t_coins > 0) ? number_format(Auth::guard('teenager')->user()->t_coins) : 'No Coins' }}</a>
                            <div class="submenu">
                                <h2>My ProCoins</h2>
                                <div class="btns">
                                    <a href="{{ url('/teenager/gift-coins') }}" class="btn btn-success btn-small text-uppercase">Gift</a>
                                    <a href="{{ url('/teenager/buy-procoins') }}" class="btn btn-success btn-small text-uppercase">Buy</a>
                                    <a href="{{ url('/teenager/get-pro-coins-history') }}" class="btn btn-success btn-small text-uppercase">History</a>
                                </div>
                            </div>
                        </li>
                        <li class="n-window"><a href="{{ url('/teenager/list-career') }}"><i class="icon-window"></i></a></li>
                        <li class="n-menu"><a href="javascript:void(0);" class="menu-toggler"><i class="icon-menu"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-container">
                    <ul>
                        <li><a href="{{ url('/teenager/home') }}" title="Dashboard" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/home']) ? 'active' : ''}}">Dashboard</a></li>
                        <li><a href="{{ url('/teenager/list-career') }}" title="Careers" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/list-career']) ? 'active' : ''}}">Careers</a></li>
                        <li><a href="{{ url('/teenager/community') }}" title="Community" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/community']) ? 'active' : ''}}">Community</a></li>
                        <li><a href="{{ url('/teenager/my-profile') }}" title="Profile" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/my-profile', 'teenager/edit-profile']) ? 'active' : ''}}">Profile</a></li>
                        <li><a href="{{ url('/teenager/coupons') }}" title="Coupons" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/coupons']) ? 'active' : ''}}">Coupons</a></li>
                        <li><a href="{{ url('/teenager/chat') }}" title="Chat" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/chat']) ? 'active' : ''}}">Chat</a></li>
                        <li><a href="{{ url('/teenager/help') }}" title="Help" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager/help']) ? 'active' : ''}}">Help</a></li>
                    </ul>
                    <img class="i-menu-rocket menu-rocket" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMkAAABZAQMAAACubpIFAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABlJREFUeNrtwTEBAAAAwqD1T20JT6AAADgbCWMAAa20KzgAAAAASUVORK5CYII=">
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
                        <li><a href="#">Careers</a></li>
                        <li><a href="{{ url('privacy-policy') }}" title="Privacy Policy">Privacy</a></li>
                        <li><a href="{{ url('terms-condition') }}" title="Terms & Conditions">Terms & Conditions</a></li>
                    </ul>
                    <span>&copy; 2018 ProTeen</span>
                </div>
                <div class="right">
                    <ul class="social">
                        <li><a href="https://www.facebook.com/proteenlife/" target="_blank"><i class="icon-facebook"></i></a></li>
                        <li><a href="https://twitter.com/ProTeenLife" target="_blank"><i class="icon-twitter"></i></a></li>
                        <li><a href="https://plus.google.com/109414106711493074923" target="_blank"><i class="icon-google"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/proteen-life" target="_blank"><i class="icon-linkdin"></i></a></li>
                    </ul>
                    <div class="store">
                        <a href="https://itunes.apple.com/us/app/proteen/id1247894187?mt=8" target="_blank" class="appstore">
                        <img class="i-app-store" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJwAAAA1AQMAAACOZRAoAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABJJREFUeNpjYBgFo2AUjAIqAwAEWQABDrhkeAAAAABJRU5ErkJggg==">
                        </a>
                        <a href="https://play.google.com/store/apps/details?id=com.proteenlife" target="_blank" class="playstore">
                        <img class="i-play-store" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJsAAAA1AQMAAABsuQtRAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABJJREFUeNpjYBgFo2AUjAIqAwAEWQABDrhkeAAAAABJRU5ErkJggg==">
                        </a>
                    </div>
                </div>
            </div>
        </footer>
        <div id="loading-wrapper">
            <div id="loading-text"><span>Loading...</span></div>
            <div id="loading-content"></div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
        <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('js/masonry.pkgd.js') }}"></script>
        <script src="{{ asset('js/aos.js') }}"></script>
        <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('js/general.js') }}"></script>
        
        @stack('script-footer')
        @yield('script')
    </body>
</html>