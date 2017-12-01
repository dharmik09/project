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
    <body class="fixed-nav {{ (Route::getFacadeRoot()->current()->uri() == 'teenager/signup') ? 'sec-overflow' : '' }}">
        <nav class="fixed-navigation">
            <div class="container">
                <div class="logo pull-left">
                	<a href="{{ url('/') }}">
                		<img src="{{ Storage::url('img/logo.png') }}" alt="{{ trans('labels.appname') }}">
                	</a>
                </div>
                <div class="menu-toggle pull-right">
                    <ul class="nav-bar clearfix">
                        <li class="n-menu">
                        	<a href="javascript:void(0);" class="menu-toggler">
                        		<i class="icon-menu"></i>
                        	</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-container">
                    <ul>
                        <li><a href="{{ url('/teenager') }}" title="Teen" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['teenager', 'teenager/login', 'teenager/signup']) ? 'active' : ''}}">Teen</a></li>
	                    <li><a href="{{ url('/parent') }}" title="Parent" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['parent', 'parent/login', 'parent/signup']) ? 'active' : ''}}">Parent</a></li>
	                    <li><a href="{{ url('/counselor') }}"  title="Mentor" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['counselor', 'counselor/login', 'counselor/signup']) ? 'active' : ''}}">Mentor</a></li>
	                    <li><a href="{{ url('/school') }}" title="School" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['school', 'school/login', 'school/signup']) ? 'active' : ''}}">School</a></li>
	                    <li><a href="{{ url('/sponsor') }}" title="Enterprise" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['sponsor', 'sponsor/login', 'sponsor/signup']) ? 'active' : ''}}">Enterprise</a></li>
                        <li><a href="{{ url('/team') }}" title="Team" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['team']) ? 'active' : ''}}">Team</a></li>
	                    <li><a href="{{ url('/contact-us') }}" title="Contact" class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['contact-us']) ? 'active' : ''}}">Contact</a></li>
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
                        <li><a href="#"><i class="icon-facebook"></i></a></li>
                        <li><a href="#"><i class="icon-twitter"></i></a></li>
                        <li><a href="#"><i class="icon-google"></i></a></li>
                        <li><a href="#"><i class="icon-linkdin"></i></a></li>
                    </ul>
                    <div class="store">
                        <a href="" class="appstore">
                            <img class="i-app-store" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJwAAAA1AQMAAACOZRAoAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABJJREFUeNpjYBgFo2AUjAIqAwAEWQABDrhkeAAAAABJRU5ErkJggg==">
                        </a>
                        <a href="" class="playstore">
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
        <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('js/masonry.pkgd.js') }}"></script>
        <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('js/aos.js') }}"></script>
        <script src="{{ asset('js/general.js') }}"></script>
        
        @stack('script-footer')
        @yield('script')
    </body>
</html>