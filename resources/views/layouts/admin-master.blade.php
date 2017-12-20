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
            $userMenuItems = array('admin/home','admin/sponsors','admin/add-sponsor','admin/edit-sponsor','admin/coupons','admin/addcoupon','admin/editcoupon','admin/teenagers',
                'admin/add-teenager', 'admin/notification', 'admin/edit-teenager/{id}/{sid}','admin/parents/{type}','admin/counselors/{type}','admin/sponsor-activity/{id}','admin/schools','admin/edit-parent/{id}','admin/edit-school/{id}','admin/edit-sponsor/{id}');           
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
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/teenagers','admin/add-teenager','admin/edit-teenager']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/teenagers') }}">
                                            <i class="fa fa-circle-o"></i> <span>{{trans('labels.teenagers')}}</span>
                                        </a>
                                    </li>
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/parents','admin/add-parent','admin/edit-parent']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/parents/1') }}">
                                            <i class="fa fa-circle-o"></i> <span>{{trans('labels.parents')}}</span>
                                        </a>
                                    </li>
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/counselors']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/counselors/2') }}">
                                            <i class="fa fa-circle-o"></i> <span>Mentors</span>
                                        </a>
                                    </li>
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/schools', 'admin/add-school', 'admin/edit-school']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/schools') }}">
                                            <i class="fa fa-circle-o"></i> <span>{{trans('labels.schools')}}</span>
                                        </a>
                                    </li>
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/sponsors', 'admin/add-sponsor', 'admin/edit-sponsor']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/sponsors') }}">
                                            <i class="fa fa-circle-o"></i> <span>Enterprise</span>
                                        </a>
                                    </li>
                                    <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/coupons', 'admin/add-coupon', 'admin/edit-coupon']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/coupons') }}">
                                            <i class="fa fa-circle-o"></i> <span>Enterprise's {{trans('labels.coupons')}}</span>
                                        </a>
                                    </li>
                                    <li class=" treeview">
                                        <a href="{{ url('admin/notification') }}">
                                            <i class="fa fa-circle-o"></i> <span>{{trans('labels.notification')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ (Request::is('admin/level1Activity') || Request::is('admin/editHumanIcon/*') || Request::is('admin/editHumanIconCategory/*') || Request::is('admin/editCartoon/*') || Request::is('admin/editCartoonIconCategory/*') || Request::is('admin/editLevel1Activity/*') || Request::is('admin/addLevel1Activity') || Request::is('admin/editLevel1Activity') || Request::is('admin/cartoons') || Request::is('admin/addCartoon') || Request::is('admin/editCartoon') || Request::is('admin/humanIcons') || Request::is('admin/addHumanIcon') || Request::is('admin/editHumanIcon') || Request::is('admin/humanIconsCategory') || Request::is('admin/addHumanIconsCategory') || Request::is('admin/editHumanIconsCategory') || Request::is('admin/cartoonIconsCategory') || Request::is('admin/addCartoonIconsCategory') || Request::is('admin/editCartoonIconsCategory') || Request::is('admin/viewUserImage') || Request::is('admin/uploadCartoons') || Request::is('admin/viewHumanUserImage')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/level1Activity') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.level1')}}</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ (Request::is('admin/level1Activity') || Request::is('admin/addLevel1Activity') || Request::is('admin/editLevel1Activity/*') || Request::is('admin/saveLevel1Activity') || Request::is('admin/deleteLevel1Activity/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/level1Activity') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.activity')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/cartoonIconsCategory') || Request::is('admin/addCartoonIconsCategory') || Request::is('admin/editCartoonIconsCategory/*') || Request::is('admin/saveCartoonIconsCategory') || Request::is('admin/deleteCartoonIconsCategory/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/cartoonIconsCategory') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level1cartooniconcategory')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/cartoons') || Request::is('admin/addCartoons') || Request::is('admin/editCartoons/*') || Request::is('admin/saveCartoons') || Request::is('admin/deleteCartoons/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/cartoons') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level1cartoonicon')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/humanIconsCategory') || Request::is('admin/addHumanIconsCategory') || Request::is('admin/editHumanIconsCategory/*') || Request::is('admin/saveHumanIconsCategory') || Request::is('admin/deleteHumanIconsCategory/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/humanIconsCategory') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level1humaniconcategory')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/humanIcons') || Request::is('admin/addHumanIcons') || Request::is('admin/editHumanIcons/*') || Request::is('admin/saveHumanIcons') || Request::is('admin/deleteHumanIcons/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/humanIcons') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level1humanicon')}}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ (Request::is('admin/level2Activity') || Request::is('admin/addLevel2Activity') || Request::is('admin/editLevel2Activity/*') || Request::is('admin/saveLevel2Activity') || Request::is('admin/deleteLevel2Activity/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/level2Activity') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.level2')}}</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ (Request::is('admin/level2Activity') || Request::is('admin/addLevel2Activity') || Request::is('admin/editLevel2Activity/*') || Request::is('admin/saveLevel2Activity') || Request::is('admin/deleteLevel2Activity/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/level2Activity') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.activity')}}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ (Request::is('admin/baskets') || Request::is('admin/addBasket') || Request::is('admin/editBasket/*') || Request::is('admin/saveBaskets') ||Request::is('admin/deleteBaskets') || Request::is('admin/professions') || Request::is('admin/addProfessions') || Request::is('admin/editProfessions/*') || Request::is('admin/saveProfessions') || Request::is('admin/deleteProfessions/*') || Request::is('admin/addProfessionBulk') || Request::is('admin/saveProfessionBulk') || Request::is('admin/exportProfessoin') || Request::is('admin/exportCompetitors') || Request::is('admin/headers') || Request::is('admin/addHeader') || Request::is('admin/editHeader/*') || Request::is('admin/deleteHeader') || Request::is('admin/deleteHeader/*') || Request::is('admin/careerMapping') || Request::is('admin/addCareerMapping') || Request::is('admin/editCareerMapping/*') || Request::is('admin/saveCareerMapping') || Request::is('admin/deleteCareerMapping/*') || Request::is('admin/importExcel') || Request::is('admin/addImportExcel') ) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/baskets') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.level3')}}</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ (Request::is('admin/baskets') || Request::is('admin/addBaskets') || Request::is('admin/editBaskets/*') || Request::is('admin/saveBaskets') || Request::is('admin/deleteBaskets')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/baskets') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.baskets')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/professions') || Request::is('admin/addProfessions') || Request::is('admin/editProfessions/*') || Request::is('admin/saveProfessions') || Request::is('admin/deleteProfessions/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/professions') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.professions')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/headers') || Request::is('admin/addHeaders') || Request::is('admin/editHeaders/*') || Request::is('admin/saveHeaders') || Request::is('admin/deleteHeaders/*')) ? 'active' : '' }}">
                                        <a href="{{ url('admin/headers') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.headers')}}
                                        </a>
                                    </li>
                                    <li class="{{ (Request::is('admin/careerMapping') || Request::is('admin/addCareerMapping') || Request::is('admin/editCareerMapping/*') || Request::is('admin/saveCareerMapping') || Request::is('admin/deleteCareerMapping/*') || Request::is('admin/importExcel') || Request::is('admin/addImportExcel')) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/careerMapping') }}">
                                            <i class="fa fa-circle-o"></i> <span>Career HML Mapping</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/level4Activity', 'admin/viewUserAllAdvanceActivities/{teenager}/{profession}', 'admin/level4Activity', 'admin/level4AdvanceActivityUserTask', 'admin/editlevel4advanceactivity/{id}', 'admin/listlevel4advanceactivity', 'admin/editlevel4IntermediateActivity/{id}', 'admin/listLevel4IntermediateActivity', 'admin/editGamificationTemplate/{id}', 'admin/listGamificationTemplate', 'admin/editLeve4Activity/{id}', 'admin/addLevel4Activity', 'admin/editLevel4Activity', 'admin/level4LearningStyle', 'admin/level4AdvanceActivityParentTask']) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/level4Activity') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.level4')}}</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @if(Auth::guard('admin')->user()->email != trans('labels.adminemailid'))
                                        <li class=" {{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/level4Activity', 'admin/addLevel4Activity', 'admin/editLevel4Activity']) ? 'active' : ''}} treeview">
                                            <a href="{{ url('admin/level4Activity') }}"><i class="fa fa-circle-o"></i>Basic Activities</a>
                                        </li>
                                    @endif
                                    <li class=" {{ in_array(Route::getFacadeRoot()->current()->uri(), ['admin/listGamificationTemplate']) ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/listGamificationTemplate') }}">
                                            <i class="fa fa-circle-o"></i> <span>Questions Concepts</span>
                                        </a>
                                    </li>
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/addIntermediateActivity') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/listLevel4IntermediateActivity') }}">
                                            <i class="fa fa-circle-o"></i><span>InterMediate Activities</span>
                                        </a>
                                    </li>
                                    @if(Auth::guard('admin')->user()->email != trans('labels.adminemailid'))
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/listlevel4advanceactivity') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/listlevel4advanceactivity') }}">
                                            <i class="fa fa-circle-o"></i><span>Advance Activities</span>
                                        </a>
                                    </li>
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/level4AdvanceActivityUserTask') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/level4AdvanceActivityUserTask') }}">
                                            <i class="fa fa-circle-o"></i><span>Advance User Tasks</span>
                                        </a>
                                    </li>
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/level4AdvanceActivityParentTask') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/level4AdvanceActivityParentTask') }}">
                                            <i class="fa fa-circle-o"></i><span>Advance Parent Tasks</span>
                                        </a>
                                    </li>
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/level4LearningStyle') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/level4LearningStyle') }}">
                                            <i class="fa fa-circle-o"></i><span>Learning Guidance</span>
                                        </a>
                                    </li>
                                    <li class="{{ (Route::getFacadeRoot()->current()->uri() == 'admin/professionLearningStyle') ? 'active' : '' }} treeview">
                                        <a href="{{ url('admin/professionLearningStyle') }}">
                                            <i class="fa fa-circle-o"></i><span>Profession Learning Guidance</span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li> 
                            <li class="{{ (Request::is('admin/listHint') ||  Request::is('admin/genericAds') || Request::is('admin/editConfiguration/*') || Request::is('admin/configurations') || Request::is('admin/editTemplate/*') || Request::is('admin/templates') || Request::is('admin/editCms/*') || Request::is('admin/cms') || Request::is('admin/editHintLogic/*')) ? 'active': '' }} treeview">
                                <a href="{{ url('admin/listHint') }}">
                                    <i class="fa fa-dashboard"></i> <span>Settings</span><i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="treeview">
                                        <a href="{{ url('admin/listHint') }}">
                                            <i class="fa fa-circle-o"></i> <span>Hint Management</span>
                                        </a>
                                    </li>
                                    <li class="treeview">
                                        <a href="{{ url('admin/cms') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.cms')}}
                                        </a>
                                    </li>
                                    <li class="treeview">
                                        <a href="{{ url('admin/templates') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.emailtemplates')}}
                                        </a>
                                    </li>
                                    <li class="treeview">
                                        <a href="{{ url('admin/configurations') }}">
                                            <i class="fa fa-circle-o"></i> <span>Configuration</span>
                                        </a>
                                    </li>
                                    <li class="treeview">
                                        <a href="{{ url('admin/genericAds') }}">
                                            <i class="fa fa-circle-o"></i> <span>Generic Ads</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ (Request::is('admin/userReport') || Request::is('admin/schoolReport') || Request::is('admin/level1Chart') || Request::is('admin/iconReport') || Request::is('admin/iconQualityReport') || Request::is('admin/level2Chart') || Request::is('admin/userApi') || Request::is('admin/level3Report') || Request::is('admin/level4BasicReport') || Request::is('admin/level4IntermediateReport') || Request::is('admin/level4AdvanceReport')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/level1Chart') }}">
                                    <i class="fa fa-dashboard"></i> 
                                    <span>{{trans('labels.report')}}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="{{ url('admin/userReport') }}">
                                            <i class="fa fa-circle-o"></i>Teens
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/schoolReport') }}">
                                            <i class="fa fa-circle-o"></i>School Report
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/level1Chart') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level1')}} Survey
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/iconReport') }}">
                                            <i class="fa fa-circle-o"></i>Level 1 Icon
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/iconQualityReport') }}">
                                            <i class="fa fa-circle-o"></i>Level 1 Quality
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ url('admin/level2Chart') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level2')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/userApi') }}">
                                            <i class="fa fa-circle-o"></i>Teen PROMISE
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/level3Report') }}">
                                            <i class="fa fa-circle-o"></i>{{trans('labels.level3')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/level4BasicReport') }}">
                                            <i class="fa fa-circle-o"></i>Level 4 Basic
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/level4IntermediateReport') }}">
                                            <i class="fa fa-circle-o"></i>Level 4 Intermediate
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('admin/level4AdvanceReport') }}">
                                            <i class="fa fa-circle-o"></i>Level 4 Advance
                                        </a>
                                    </li>
                                </ul>
                            </li>   
                            <li class="{{ (Request::is('admin/coins') ||  Request::is('admin/addCoins') || Request::is('admin/editCoins/*') || Request::is('admin/saveCoins') || Request::is('admin/deleteCoins/*')) ? 'active': '' }}">
                                <a href="{{ url('admin/coins') }}">
                                    <i class="fa fa-dashboard"></i>ProCoins Packages
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/paidComponents') || Request::is('admin/addPaidComponents') || Request::is('admin/editPaidComponents/*') || Request::is('admin/savePaidComponents') || Request::is('admin/deletePaidComponents/*') || Request::is('admin/invoice') || Request::is('admin/addInvoice') || Request::is('admin/editInvoice/*') || Request::is('admin/saveInvoice') || Request::is('admin/deleteInvoice/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/paidComponents') }}">
                                    <i class="fa fa-dashboard"></i>
                                        <span>Paid Components</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="treeview">
                                        <a href="{{ url('admin/paidComponents') }}">
                                            <i class="fa fa-circle-o"></i><span>Paid Components</span>
                                        </a>
                                    </li>
                                    <li class="treeview">
                                        <a href="{{ url('admin/invoice') }}">
                                            <i class="fa fa-circle-o"></i><span>Invoice</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="{{ (Request::is('admin/video') || Request::is('admin/addVideo') || Request::is('admin/editVideo/*') || Request::is('admin/saveVideo') || Request::is('admin/deleteVideo/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/video') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.video')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/faq') || Request::is('admin/addFaq') || Request::is('admin/editFaq/*') || Request::is('admin/saveFaq') || Request::is('admin/deleteFaq/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/faq') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.faq')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/testimonials') || Request::is('admin/addTestimonial') || Request::is('admin/editTestimonial/*') || Request::is('admin/saveTestimonial') || Request::is('admin/deleteTestimonial/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/testimonials') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.testimonial')}} / Team</span>
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/helpText') || Request::is('admin/addHelpText') || Request::is('admin/editHelpText/*') || Request::is('admin/saveHelpText') || Request::is('admin/deleteHelpText/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/helpText') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.helptext')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/professionCertifications') || Request::is('admin/addProfessionCertification') || Request::is('admin/editProfessionCertification/*') || Request::is('admin/saveProfessionCertification') || Request::is('admin/deleteProfessionCertification/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/professionCertifications') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.professioncertification')}}</span>
                                </a>
                            </li>
                            <li class="{{ (Request::is('admin/professionSubjects') || Request::is('admin/addProfessionSubject') || Request::is('admin/editProfessionSubject/*') || Request::is('admin/saveProfessionSubject') || Request::is('admin/deleteProfessionSubject/*')) ? 'active' : '' }} treeview">
                                <a href="{{ url('admin/professionSubjects') }}">
                                    <i class="fa fa-dashboard"></i> <span>{{trans('labels.professionsubject')}}</span>
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
        <script src="{{ asset('backend/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js')}}"></script>
        <script>
            $('div.alert').not('.alert-important').delay(5000).fadeOut(350);
            $('div.alert.alert-important').delay(5000).fadeOut(350);
        </script>
        @yield('script')

    </body>
</html>