<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{trans('labels.appname')}}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ asset('/backend/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/css/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/css/ionicons.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/plugins/datepicker/bootstrap-datetimepicker.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/css/AdminLTE.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/square/blue.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/css/custom.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/css/chosen.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/css/jquery.dataTables.min.css') }}"/>
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        @yield('header')
    </head>
    @if (Auth::guard('admin')->check())
    <body class="hold-transition skin-blue sidebar-mini">
        @else
    <body class="hold-transition login-page">
        @endif

        <div class="wrapper">
            @if (Auth::guard('admin')->check())
            <?php             
            $userMenuItems = array('admin/home','admin/sponsors','admin/addsponsor','admin/editsponsor','admin/coupons','admin/addcoupon','admin/editcoupon','admin/teenagers',
                'admin/parents/{type}','admin/counselors/{type}','admin/sponsoractivity/{id}','admin/schools','admin/editparent/{id}','admin/editschool/{id}','admin/editsponsor/{id}');           
            ?>
            <header class="main-header">
                <a href="{{ url('/admin')}}" class="logo">
                    <span class="logo-mini"><img src="{{ asset('backend/images/proteen_logo.png')}}" /></span>
                    <span class="logo-lg"><img src="{{ asset('backend/images/proteen_logo.png')}}" /></span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">{{trans('labels.togglenav')}}</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('/backend/images/avatar5.png')}}" class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{Auth::guard('admin')->user()->name}}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="{{ asset('/backend/images/avatar5.png')}}" class="img-circle" alt="User Image">
                                        <p>
                                            {{Auth::guard('admin')->user()->name}}
                                        </p>
                                    </li>
                                    <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                    <li class="user-footer">
                                        <div style="text-align: center;">
                                            <a href="{{ url('/admin/logout') }}"
                                                onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="{{ asset('/backend/images/avatar5.png')}}" class="img-circle" alt="User Image">
                        </div>
                        <div style="padding-top: 8px;">
                            <a href="{{ url('admin/dashboard') }}">
                                <span style="margin-left: 30px; font-size: 18px;">{{trans('labels.dashboard')}}</span>
                            </a>
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                                              
                        @if(Auth::guard('admin')->user()->email != trans('labels.adminemailid'))
                            <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), $userMenuItems) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/teenagers') }}">
                                    <i class="fa fa-dashboard"></i> <span>All Users</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/teenagers','admin/addteenager','admin/editteenager']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/teenagers') }}">
                                            <i class="fa fa-circle-o"></i> <span>{{trans('labels.teenagers')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ (Request::is('admin/video') || Request::is('admin/addVideo') || Request::is('admin/editVideo/*') || Request::is('admin/saveVideo') || Request::is('admin/deleteVideo/*')) ? 'active' : '' }} treeview">
                            <a href="{{ url('admin/video') }}">
                                <i class="fa fa-circle-o"></i> <span>{{trans('labels.video')}}</span>
                            </a>
                        </li>  
                        @endif                        
                    </ul>
                </section>
            </aside>
            @endif

            @if (Auth::guard('admin')->check())
            <div class="content-wrapper">

                @if ($message = Session::get('success'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body">
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body">
                            <div class="alert alert-error alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @yield('content')
            </div>
            @else
            @yield('content')
            @endif

            @if (Auth::guard('admin')->check())

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    {!! trans('labels.version') !!}
                </div>
                {!! trans('labels.copyrightstr') !!}
            </footer>
            @endif
            @yield('footer')
        </div>
        <script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <script src="{{ asset('backend/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('backend/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <script src="{{ asset('backend/plugins/fastclick/fastclick.min.js')}}"></script>
        <script src="{{ asset('backend/js/app.min.js')}}"></script>
        <script src="{{ asset('backend/js/demo.js')}}"></script>
        <script src="{{ asset('backend/plugins/iCheck/icheck.min.js')}}"></script>
        <script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('backend/js/chosen.jquery.js')}}"></script>
        <script src="{{ asset('backend/js/common_admin.js')}}"></script>
        <script>
            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            $('div.alert.alert-important').delay(5000).fadeOut(350);
        </script>
        @yield('script')

    </body>
</html>