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
        <title>{{trans('labels.appname')}}</title>
        <!-- Bootstrap -->
        <link href="{{asset('css/home/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('css/home/owl.css')}}" rel="stylesheet">
        <link href="{{asset('css/home/style.css')}}" rel="stylesheet">
        @stack('script-header')
        @yield('header')

    </head>
    <body>
    @yield('content')
        <footer>
            <div class="container">
                <div class="left">
                    <ul class="links">
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                    <span>&copy; 2016 Proteen</span>
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
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ asset('js/home/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/home/owl.carousel.min.js') }}"></script>
        
        @stack('script-footer')
        @yield('script')
    </body>
</html>