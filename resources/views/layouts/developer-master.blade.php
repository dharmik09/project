<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{trans('labels.appname')}}</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->

        <link rel="stylesheet" href="{{ asset('/backend/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('/backend/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('/backend/css/ionicons.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/plugins/datepicker/bootstrap-datetimepicker.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('/backend/css/AdminLTE.min.css')}}">
        <link rel="stylesheet" href="{{ asset('/backend/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/square/blue.css')}}">
        <link rel="stylesheet" href="{{ asset('backend/css/custom.css')}}">
        <link rel='stylesheet' src='https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css'/>
        <link rel="icon" type="image/png" href="{{ asset('/backend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/backend/images/favicon-16x16.png')}}" sizes="16x16" />
        
        @yield('header')
    </head>
    @if (Auth::guard('developer')->check())
    <body class="hold-transition skin-blue sidebar-mini">
        @else
    <body class="hold-transition login-page">
        @endif
        <div class="wrapper">
            @if (Auth::guard('developer')->check())
            <header class="main-header">
                <a href="{{ url('/developer')}}" class="logo">
                    <span class="logo-mini"><b>{{trans('labels.appshortname')}}</b></span>
                    <span class="logo-lg"><b>{{trans('labels.appname')}}</b></span>
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
                                    <span class="hidden-xs">{{Auth::guard('developer')->user()->name}}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{ asset('/backend/images/avatar5.png')}}" class="img-circle" alt="User Image">
                                        <p>
                                            {{Auth::guard('developer')->user()->name}}
                                        </p>
                                    </li>

                                    <li class="user-footer">
                                        <div style="text-align: center;">
                                            <form id="logout-form" action="{{ url('/developer/logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                            <a href="{{ url('/developer/logout') }}"
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
                        <div class="pull-left info">
                            <p>{{Auth::guard('developer')->user()->name}}</p>
                            <a href="javascript:void(0)"><i class="fa fa-circle text-success"></i>{{trans('labels.online')}}</a>
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="{{ url('developer/home') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.dashboard')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/systemLevel') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.systemlevels')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/apptitudeType') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.apptitudetypes')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/personalityType') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.personalitytypes')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/multipleintelligenceType') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.multipleintelligencetypes')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/interestType') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.interesttypes')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/multipleintelligenceTypeScale') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.multipleintelligencetypescale')}}</span>
                            </a>
                        </li>
                         <li>
                            <a href="{{ url('developer/personalityTypeScale') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.personalitytypescale')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/apptitudeTypeScale') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.apptitudetypescale')}}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('developer/level1Qualities') }}">
                                <i class="fa fa-dashboard"></i> <span>{{trans('labels.level1qualities')}}</span>
                            </a>
                        </li>
                    </ul>
                </section>  
                <!-- /.sidebar -->
            </aside>
            @endif

            @if (Auth::guard('developer')->check())
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
            </div><!-- /.content-wrapper -->
            @else
            @yield('content')
            @endif

            @if (Auth::guard('developer')->check())

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 1.0
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
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

        @yield('script')

    </body>
</html>