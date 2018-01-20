@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Profile</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sponsor-overflow" id="profile-info">
        <div class="container">
            <div class="col-xs-12">
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
                    <div class="col-md-8 col-md-offset-2 invalid_pass_error">
                        <div class="box-body">
                            <div class="alert alert-error alert-dismissable danger">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if (count($errors) > 0)
                <div class="alert alert-danger danger">
                    <strong>{{trans('validation.whoops')}}</strong>
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    {{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="sec-popup">
                <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                <div class="hide" id="pop1">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                    </div>
                </div>
            </div>
            <!--profile detail-->
            <div class="profile-detail">
                <form id="teenager_my_profile_form" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/teenager/save-profile') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="upload-img profile-img" id="img-preview">
                                <span style="background-image: url({{ $data['user_profile'] }})"></span>
                                <input type="file" name="pic" accept="image/*" onchange="readURL(this);" title="Edit Profile image">
                            </div>
                            <div class="photo-error"></div>
                            <span class="complete-detail">Profile 62% complete</span>
                        </div>
                        <?php
                            if($user->t_pincode != "")
                            {
                                $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$user->t_pincode.'&sensor=true');
                                $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                            } else {
                                $getCityArea = ( Auth::guard('teenager')->user()->getCountry->c_name != "" ) ? Auth::guard('teenager')->user()->getCountry->c_name : "Default";
                            }
                        ?>
                        <div class="col-sm-9">
                            <h1>{{ $user->t_name }} {{ $user->t_lastname }}</h1>
                            <ul class="area-detail">
                                <li>{{ $getCityArea }} Area</li>
                                <li>{{ $myConnectionsCount }} {{ ($myConnectionsCount == 1) ? "Connection" : "Connections" }} </li>
                            </ul>
                            <ul class="social-media">
                                <li><a href="#" title="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                                <li><a href="#" title="google plus" target="_blank"><i class="icon-google"></i></a></li>
                            </ul>
                            <div class="chat-icon">
                                <a href="#" title="Chat"><i class="icon-chat"></i>
                                    <span>3</span></a>
                            </div>
                            <p id="display-about-info">{{ $user->t_about_info }}</p>
                            <input type="text" class="form-control about-info" id="t_about_info" name="t_about_info" placeholder="describe yourself" value="{{ $user->t_about_info }}" >
                            <a id="editInfo" href="javascript:void(0);" title="Edit Info">Edit</a>
                        </div>
                    </div>
                    <!--profile form-->
                    <div class="profile-form">
                        <div class="clearfix row flex-container">
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="name" name="name" placeholder="first name *" tabindex="1" value="{{ $user->t_name }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="lastname" name="lastname" placeholder="last name *" tabindex="2" value="{{ $user->t_lastname }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email *" tabindex="3" value="{{ $user->t_email }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control onlyNumber" id="phone" name="phone" placeholder="phone" minlength="7" maxlength="10" tabindex="5" value="{{ $user->t_phone_new }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group input-group">
                                    <div class="clearfix">
                                        <span id="countrycode" class="input-group-addon">+91</span>
                                        <input type="text" class="form-control onlyNumber" id="mobile" name="mobile" maxlength="10" placeholder="mobile phone *" tabindex="6" value="{{ $user->t_phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group custom-select">
                                    <select tabindex="7" class="form-control" id="country" name="country" onchange="getPhoneCodeByCountry(this.value);" required>
                                        <option value="">country</option>
                                        @forelse($countries as $val)
                                            <option value="{{$val->id}}" <?php echo ($user->t_country == $val->id ) ? "selected='selected'" : ''; ?> > {{$val->c_name}} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <input type="hidden" name="country_phone_code" id="country_phone_code" readonly="readonly" id="country_phone_code" class="cst_input_primary" maxlength="10" placeholder="Phone Code" value="{{old('country_phone_code')}}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="zip code *" tabindex="8" value="{{ $user->t_pincode }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="password" class="form-control pass-visi" id="password" name="password" placeholder="password" tabindex="11" value="" maxlength="16">
                                    <span class="visibility-pwd">
                                        <img src="{{ Storage::url('img/view.png') }}" alt="view" class="view img">
                                        <img src="{{ Storage::url('img/hide.png') }}" alt="view" class="img-hide hide img">
                                    </span>
                                    <span class="password-info">Type password to change your current password</span>
                                    <em id="pass_validation">  </em>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group custom-select">
                                    <select tabindex="9" class="form-control" id="gender" name="gender" required >
                                        <option value="1" <?php echo (old('gender') && old('gender') == 1) ? "selected='selected'" : ''; ?> >Male</option>
                                        <option value="2" <?php echo (old('gender') && old('gender') == 2) ? "selected='selected'" : ''; ?> >Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control nospace" id="proteen_code" name="proteen_code" placeholder="proteen code" tabindex="10" value="{{ $user->t_nickname }}">
                                </div>
                            </div>
                            <?php 
                                $birthYear = $birthMonth = $birthDay = "";
                                if(isset($user->t_birthdate) && $user->t_birthdate != "") {
                                    list($birthYear, $birthMonth, $birthDay) = explode("-", $user->t_birthdate);
                                }
                            ?>
                            <div class="col-sm-9 col-sm-offset-3 text-right">
                                <div class="form-group date-sec">
                                    <label>Date of Birth:</label>
                                    <div class="date-feild">
                                        <select name="month" class="form-control date-block" id="month" tabindex="13">
                                            <option value="">mm</option>
                                            @for($month = 01; $month <= 12; $month++)
                                                <option value="{{date('m', mktime(0,0,0,$month, 1, date('Y')))}}" <?php echo ($month == $birthMonth) ? "selected='selected'" : ''; ?> >{{ date('M', mktime(0,0,0,$month, 1, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="day" class="form-control date-block" id="day" tabindex="14" >
                                            <option value="">dd</option>
                                            @for($day = 1; $day <= 31; $day++)
                                                <option value="{{date('d', mktime(0,0,0,0, $day, date('Y')))}}" <?php echo ($day == $birthDay) ? "selected='selected'" : ''; ?> >{{ date('d', mktime(0,0,0,0, $day, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="year" class="form-control date-block" id="year" tabindex="15">
                                            <option value="">yyyy</option>
                                            @foreach(range(\Carbon\Carbon::now()->year, 1950) as $year)
                                                <option value="{{$year}}" <?php echo ($year == $birthYear) ? "selected='selected'" : ''; ?> >{{$year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="birth-error" style="text-align: left;padding-left:35%;"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="sponsor-sec">
                                    <div class="sponsor-content">
                                        <div class="form-register sponsor-list owl-carousel">
                                            @forelse($sponsorDetail as $key => $value)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="selected_sponsor[]" id="sponsor_{{$key}}" value="{{$value->sponsor_id}}" <?php echo (!empty($teenSponsorIds) && in_array($value->sponsor_id, $teenSponsorIds) ) ? "checked" : ""; ?> />
                                                        <span class="checker"></span>
                                                        <span class="logo-icon">
                                                            <?php
                                                                $sponsor_logo = ($value->sp_logo != "") ? Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH').$value->sp_logo) : asset(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                                            ?>
                                                            <img src="{{ $sponsor_logo }}" alt="{{ $value->sp_company_name }}" style="height:74px; width:127px;">
                                                        </span>
                                                        <span class="sponsor-name">{{ str_limit($value->sp_company_name, $limit = 100, $end = '..') }}</span>
                                                    </label>
                                                </div>
                                            @empty

                                            @endforelse
                                        </div>
                                        <div class="sponsor-error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7 text-right">
                                <ul class="toggle-switch">
                                    <li>Public profile
                                        <label class="switch">
                                            <input type="checkbox" id="public_profile" name="public_profile" <?php echo (isset($user->is_search_on) && $user->is_search_on == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with other members
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_members" name="share_with_members" <?php echo (isset($user->is_share_with_other_members) && $user->is_share_with_other_members == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with parents
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_parents" name="share_with_parents" <?php echo (isset($user->is_share_with_parents) && $user->is_share_with_parents == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Share info with teachers
                                        <label class="switch">
                                            <input type="checkbox" id="share_with_teachers" name="share_with_teachers" <?php echo (isset($user->is_share_with_teachers) && $user->is_share_with_teachers == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>Notifications
                                        <label class="switch">
                                            <input type="checkbox" id="notifications" name="notifications" <?php echo (isset($user->is_notify) && $user->is_notify == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                                <span class="on">On</span>
                                                <span class="off">Off</span>
                                            </span>
                                        </label>
                                    </li>
                                    <li>View information for
                                        <label class="switch">
                                        <input type="checkbox" id="t_view_information" name="t_view_information" <?php echo (isset($user->t_view_information) && $user->t_view_information == '1') ? "checked='checked'": '' ?> value="1">
                                            <span class="slider round">
                                              <span class="on">USA</span>
                                              <span class="off">India</span>
                                            </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center col-sm-12">
                                <button id="saveProfile" class="btn btn-submit btn-default" type="submit" title="Submit">Submit</button>
                                <span class="hand-icon"><i class="icon-hand-simple"></i></span>
                            </div>
                        </div>
                    </div>
                    <!--profile form end-->
                </form>
            </div>
            <!--profile detail end-->
        </div>
    </section>
    <!-- profile section-->
    <!--sec parents & mentors-->
    <section class="sec-parents" id="sec-parents">
        <div class="container">
            <div class="sec-popup">
                <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop2" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                <div class="hide" id="pop2">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                    </div>
                </div>
            </div>
            <h2>Parents & Mentors</h2>
            <div class="parent-form">
                <form id="teenager_parent_pair_form" role="form" method="POST" action="{{ url('/teenager/save-pair') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="email" id="parent_email" name="parent_email" placeholder="Email" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group custom-select">
                                <select class="form-control" id="p_user_type" name="p_user_type">
                                    <option value="1">Parent</option>
                                    <option value="2">Mentor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-submit" type="submit" title="Add">Add</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="mentor-list">
                <ul class="row owl-carousel">
                    @forelse($teenagerParents as $teenagerParent)
                        <?php 
                            if (isset($teenagerParent->p_photo) && $teenagerParent->p_photo != '') {
                                $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $teenagerParent->p_photo;
                            } else {
                                $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                            }
                        ?>
                        <li class="col-sm-3 col-xs-6">
                            <figure>
                                <div class="mentor-img" style="background-image: url({{ Storage::url($parentPhoto) }})"></div>
                                <figcaption>{{ $teenagerParent->p_first_name }}</figcaption>
                            </figure>
                        </li>
                    @empty
                        No parents or mentors found.
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
    <!--sec parents & mentors end-->
    <!-- sec personal survey-->
    <div class="sec-survey" id="sec-survey">
        <div class="container">
            <h2>Personal Survey Part - 1</h2>
            <div id="traitsData"></div>
            
            <div class="sec-popup">
                <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop3" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                <div class="hide" id="pop3">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                    </div>
                </div>
            </div>
            <h2>Personal Survey</h2>
            <div id="errorGoneMsg"></div>
            <div class="survey-list">
                <div id="loading-wrapper-sub" class="loading-screen bg-offwhite">
                    <div id="loading-text">
                        <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
                    </div>
                    <div id="loading-content"></div>
                </div>
                <div class="opinion-sec" id="opinionSection" style="display:none;">
                </div>
                <br/>
                <div id="firstLevelWorldSection">
                    
                </div>
            </div>
        </div>
    </div>
    <!-- sec personal survey end-->
    <!-- icon voted sec start-->
    <div class="icon-voted bg-offwhite" id="icon-voted">
        <div class="container">
            <h2>Icon Voted in L1</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nemo pariatur id, explicabo vitae delectus eveniet rem doloremque perspiciatis, soluta, officiis mollitia reprehenderit assumenda libero molestias quae et. Tenetur, a, atque.</p>
            <div class="voted-list">
                @if (isset($teenagerMyIcons) && !empty($teenagerMyIcons))
                <ul class="row owl-carousel">
                    @forelse($teenagerMyIcons as $teenagerMyIcon)
                    <li class="col-sm-3 col-xs-6">
                        <figure>
                            <div class="icon-img"><img src="{{ $teenagerMyIcon }}"></div>
                        </figure>
                    </li>
                    @empty
                    No Icons Found
                    @endforelse
                </ul>
                @else
                    <h3>No Icons Found</h3>
                @endif
            </div>
        </div>
    </div>
    <!-- icon voted sec end-->
    <!--sec progress-->
    <section class="sec-progress" id="sec-progress">
        <div class="container">
            <h2>My Progress</h2>
            <div class="bg-white my-progress profile-tab">
                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                    <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Achievements <span class="count">(10)</span></span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">My Careers <span class="count">({{$myCareersCount}})</span></span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">My Connections <span class="count">({{$myConnectionsCount}})</span></span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
                        <ul class="badge-list clearfix">
                            <li class="point-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>Points <br>achieved</p>
                            </li>
                            <li class="point-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>Points <br>achieved</p>
                            </li>
                            <li class="point-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>Points <br>achieved</p>
                            </li>
                            <li class="point-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>Points <br>achieved</p>
                            </li>
                            <li>
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>Points <br>achieved</p>
                            </li>
                        </ul>
                        <ul class="badge-list clearfix">
                            <li class="career-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>careers<br> completed</p>
                            </li>
                            <li class="career-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>careers<br> completed</p>
                            </li>
                            <li class="career-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>careers<br> completed</p>
                            </li>
                            <li>
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>careers<br> completed</p>
                            </li>
                            <li>
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>careers<br> completed</p>
                            </li>
                        </ul>
                        <ul class="badge-list clearfix">
                            <li class="connection-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>connections<br> made</p>
                            </li>
                            <li class="connection-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>connections<br> made</p>
                            </li>
                            <li class="connection-cl">
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>connections<br> made</p>
                            </li>
                            <li>
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>connections<br> made</p>
                            </li>
                            <li>
                                <div class="point-tab">
                                    <i class="icon-badge"></i>
                                    <span class="point">100</span>
                                </div>
                                <p>connections<br> made</p>
                            </li>
                        </ul>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="careers-tab my-career">
                            @forelse ($myCareers as $myCareer)
                            <div class="careers-block">
                                <div class="careers-img">
                                    <!-- <i class="icon-image"></i> -->
                                    <?php
                                        if ($myCareer->pf_logo != "" && Storage::size(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$myCareer->pf_logo) > 0) {
                                            $pfLogo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$myCareer->pf_logo);
                                        } else {
                                            $pfLogo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
                                        } ?>
                                <span class="i-image"><img src="{{ $pfLogo }}" alt="career image"></span>
                                </div>
                                <div class="careers-content">
                                    <h4>{{ $myCareer->pf_name }}</h4>
                                </div>
                            </div>
                            @empty
                            <center>
                                <h3>No Records found.</h3>
                            </center>
                            @endforelse
                            @if (!empty($myCareers) && $myCareersCount > 10)
                                <p class="text-center remove-my-careers-row">
                                    <a id="load-more-career" href="javascript:void(0)" title="load more" class="load-more" data-id="{{ $myCareer->attemptedId }}">load more</a>
                                </p>
                            @endif
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade my-connection">
                        @forelse($myConnections as $myConnection)
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <?php
                                            if(isset($myConnection->t_photo) && $myConnection->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo)) {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo;
                                            } else {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                            }
                                        ?>
                                        <img src="{{ Storage::url($teenImage) }}" alt="team">
                                    </div>
                                    <a href="{{ url('teenager/network-member') }}/{{$myConnection->t_uniqueid }}" title="{{ $myConnection->t_name }}"> {{ $myConnection->t_name }}</a>
                                </div>
                            </div>
                            <div class="flex-item">
                                <div class="team-point">
                                    {{ $myConnection->t_coins }} points
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        @empty
                            <center>
                                <h3>No Connections found.</h3>
                            </center>
                        @endforelse
                        @if (!empty($myConnections->toArray()) && $myConnectionsCount > 10)
                            <p class="text-center remove-my-connection-row"><a id="load-more-connection" href="javascript:void(0)" title="load more" class="load-more" data-id="{{ $myConnection->id }}">load more</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--sec progress end-->
    <!--sec learning guidance-->
    <section class="sec-guidance" id="sec-guidance">
        <div class="container">
            <h2>Learning Guidance</h2>
            {!! (isset($learningGuidance->cms_body)) ? $learningGuidance->cms_body : 'Learning Guidance will be updated!' !!}
            <p class="text-center"><a href="{{ url('/teenager/learning-guidance') }}" title="learn more" class="btn btn-primary">learn more</a></p>
        </div>
    </section>
    <div class="sec-record" id="sec-record">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <div class="achievement">
                            Achievement Record 
                            <span>
                            @if (isset($teenagerMeta['achievement'][0]['meta_value']))
                                <?php
                                    $achievementStr = preg_replace("/&nbsp;/",'', $teenagerMeta['achievement'][0]['meta_value']);
                                ?> 
                                {!! $achievementStr !!}
                            @endif
                            </span>
                            <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="collapsed">Edit</a>
                        </div>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="accordion1">
                    <div class="panel-body">
                        <div class="list clearfix">
                            <form id="teenager_achievement" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/teenager/save-teenager-achievement-info') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                            {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <textarea name="meta_value" id="achievement">{{ isset($teenagerMeta['achievement'][0]['meta_value']) ? $teenagerMeta['achievement'][0]['meta_value'] : "" }}</textarea>
                                    <span class="achievement_error"></span>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <div class="academic">
                            Academic Record 
                            <span>
                            @if(isset($teenagerMeta['education'][0]['meta_value']))
                                <?php
                                    $academicStr = preg_replace("/&nbsp;/",'', $teenagerMeta['education'][0]['meta_value']);
                                ?> 
                                {!! $academicStr !!}
                            @endif
                            </span>
                            <a data-parent="#accordion" data-toggle="collapse" href="#accordion2" class="collapsed">Edit</a>
                        </div>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="accordion2">
                    <div class="panel-body">
                        <div class="list clearfix">
                            <form id="teenager_academic" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/teenager/save-teenager-academic-info') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                            {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <textarea name="meta_value" id="academic">{{ isset($teenagerMeta['education'][0]['meta_value']) ? $teenagerMeta['education'][0]['meta_value'] : "" }}</textarea>
                                    <span class="academic_error"></span>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>

    $('.onlyNumber').on('keyup', function() {
            this.value = this.value.replace(/[^0-9]/gi, '');
        });
    $('.alphaonly').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/[^a-zA-Z_' ]/g, ''));
    });
    $('.nospace').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/\s/g, ''));
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                //var a = document.querySelector("#img-preview");
                var a = document.querySelector(".profile-img");
                if (input.files[0].type == 'image/jpeg' || input.files[0].type == 'image/jpg' || input.files[0].type == 'image/png' || input.files[0].type == 'image/bmp') {
                    if (input.files[0].size > 3000000) {
                        $(".photo-error").text("File size is too large. Maximum 3MB allowed");
                        $(this).val('');
                    } else {
                        a.style.backgroundImage = "url('" + e.target.result + "')";
                        // document.getElementById("#").className = "activated";
                        a.className = "upload-img activated";
                    }
                } else {
                    $(".photo-error").text("File type not allowed");
                    $(this).val('');
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function readIconURL(input, setId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var a = document.querySelector(setId);
                if (input.files[0].type == 'image/jpeg' || input.files[0].type == 'image/jpg' || input.files[0].type == 'image/png' || input.files[0].type == 'image/bmp') {
                    if (input.files[0].size > 3000000) {
                        $(".errorGoneMsgPopup").text("File size is too large. Maximum 3MB allowed");
                        $(this).val('');
                    } else {
                        a.style.backgroundImage = "url('" + e.target.result + "')";
                        a.className = "upload-img activated";
                    }
                } else {
                    $(".errorGoneMsgPopup").text("File type not allowed");
                    $(this).val('');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        getFirstLevelData();
        $('#icon-slider').owlCarousel({
            loop: true,
            margin: 0,
            items: 1,
            autoplay: true,
            autoplayTimeout: 3000,
            smartSpeed: 1000,
            nav: false,
            dots: false
            //singleItem: true
        });

        $('.mentor-list ul').owlCarousel({
            loop: false,
            margin: 0,
            items: 4,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 1000,
            nav: false,
            dots: true,
            responsive: {
                0: {
                    items: 2
                },
                768: {
                    items: 4
                },
            }
        });
        $('.sponsor-list').owlCarousel({
            loop: false,
            margin: 20,
            items: 2,
            autoplay: false,
            autoplayTimeout: 3000,
            smartSpeed: 1000,
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
            }
        });
        $('.voted-list ul').owlCarousel({
                loop: true,
                margin: 0,
                items: 4,
                autoplay: false,
                autoplayTimeout: 3000,
                smartSpeed: 1000,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    480:{
                      items:2  
                    },
                    768: {
                        items: 4
                    },
                }
        });
        
        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z_'\s]+$/i.test(value);
        }, "Letters only please");
        jQuery.validator.addMethod("mobilelength", function(value, element) {
            return this.optional(element) || /^\d{10}$/i.test(value);
        }, "Please enter valid mobile number");
        var updateProfileRules = {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                lettersonly: true
            },
            lastname: {
                required: true,
                minlength: 3,
                maxlength: 50,
                lettersonly: true
            },
            email: {
                required: true,
                email: true,
                maxlength : 100
            },
            country: {
                required: true
            },
            pincode: {
                required: true,
                minlength: 5,
                maxlength: 6,
                number: true
            },
            gender: {
                required: true
            },
            year: {
                required: true,
            },
            month: {
                required: true,
            },
            day: {
                required: true,
            },
            'selected_sponsor[]': {
                required: true
            },
            mobile: {
                required: true,
                mobilelength: true,
                number: true
            },
            phone: {
                minlength: 7,
                maxlength: 10,
                number: true
            }
        };
        
        $("#teenager_my_profile_form").validate({
            rules: updateProfileRules,
            messages: {
                name: {
                    required: "First name is required",
                },
                lastname: {
                    required: "Last name is required",
                },
                email: {
                    required: "Email is required",
                },
                country: {
                    required: "Country is required"
                },
                pincode: {
                    required: "Zipcode is required",
                },
                gender: {
                    required: "Gender is required",
                },
                year: {
                    required: "Year is required",
                },
                month: {
                    required: "Month is required",
                },
                day: {
                    required: "Day is required",
                },
                'selected_sponsor[]': {
                    required: "Please select atleast one sponsor",
                },
                mobile: {
                    required: "Mobile is required",
                },
            },
            errorPlacement: function(error, element) {
                if(element.attr("name") == "selected_sponsor[]") {
                    error.appendTo(".sponsor-error");
                }else if(element.attr("name") == "day" || element.attr("name") == "year" || element.attr("name") == "month"){
                    error.appendTo(".birth-error");
                } else {
                    error.insertAfter(element)
                }
            },
        });
        var parentInviteRules = {
            parent_email: {
                required: true,
                email: true,
                maxlength: 100
            },
            p_user_type: {
                required: true
            }
        };
        $("#teenager_parent_pair_form").validate({
            rules: parentInviteRules,
            messages: {
                parent_email: {
                    required: "Email is required"
                },
                p_user_type: {
                    required: "Please select user type"
                }
            }
        });

        CKEDITOR.replace('achievement');
        CKEDITOR.replace('academic');

        CKEDITOR.config.toolbar = [
            ['Bold', 'Italic', 'BulletedList']
        ] ;

        // Cache the toggle button
        var $toggle = $(".visibility-pwd");
        var $field = $(".pass-visi");
        var i = $(this).find('.img');
        // Toggle the field type
        $toggle.on("click", function(e) {
            e && e.preventDefault();
            if ($field.attr("type") == "password") {
                $field.attr("type", "text");
                i.toggleClass("hide");
            } else {
               i.toggleClass("hide");
                $field.attr("type", "password");
            }
        });
        var countryCode = $("#country").val();
        if (countryCode) {
            getPhoneCodeByCountry(countryCode);
        }
        $('#email').attr('readonly', true);
        $("#t_about_info").hide();
        fetchLevel1TraitQuestion();

        
        // $("#fictionSave").on('click', (function(e) {
        //     console.log(); return false;
        //     e.preventDefault();
        //     $(".errorCode").text('');
        //     var cat1Value = Number($("#categoryName1").val());
        //     var cat1NameValue = $("#characterName1").val().trim();
        //     var cat2Value = Number($("#categoryName2").val());
        //     var cat2NameValue = $("#characterName2").val().trim();
        //     var submitIconData = false;
        //     var messageD = "Please, fillup all required data";
        //     if(cat1Value > 0 && cat1NameValue != '' && cat1NameValue.length > 0){
        //         submitIconData = true;
        //     }else if(cat2Value > 0 && cat2NameValue != '' && cat2NameValue.length > 0){
        //         submitIconData = true;
        //     }else{
        //         submitIconData = false;
        //     }
        //     if(submitIconData){
        //         $.ajax({
        //             url: "{{ url('/teenager/addIconCategory')}}",
        //             type: "POST",
        //             data: new FormData(this),
        //             dataType: 'json',
        //             contentType: false,
        //             cache: false,
        //             processData: false,
        //             success: function(data) {
        //                 if (data.status == 1) {
        //                     if (data.categoryType == 1) {
        //                         $("#icon_category").val(data.categoryid);
        //                         $('#icon_category').trigger("change");
        //                         $("#myModal1").modal('hide');
        //                     } else if (data.categoryType == 2) {
        //                         $("#icon_category").val(data.categoryid);
        //                         $('#icon_category').trigger("change");
        //                         $("#myModal2").modal('hide');
        //                     } else {

        //                     }
        //                 } else {
        //                     if($("#useForClassPopup").hasClass('r_after_click_popup')){
        //                         $(".errorGoneMsgPopup").html('');
        //                     }
        //                     $(".errorGoneMsgPopup").append('<div class="col-md-8 col-md-offset-2 r_after_click_popup" id="useForClassPopup"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>Something went wrong, Please try it again</div></div></div>');
        //                 }
        //             },
        //             error: function() {
        //                 if($("#useForClassPopup").hasClass('r_after_click_popup')){
        //                     $(".errorGoneMsgPopup").html('');
        //                 }
        //                 $(".errorGoneMsgPopup").append('<div class="col-md-8 col-md-offset-2 r_after_click_popup" id="useForClassPopup"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>Something went wrong, Please try it again</div></div></div>');

        //             }
        //         });
        //     }else{
        //         if($("#useForClassPopup").hasClass('r_after_click_popup')){
        //             $(".errorGoneMsgPopup").html('');
        //         }
        //         $(".errorGoneMsgPopup").append('<div class="col-md-8 col-md-offset-2 r_after_click_popup" id="useForClassPopup"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>'+messageD+'</div></div></div>');
        //     }
        // }));
    });
    

    function getFirstLevelData() {
        $('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        $('#loading-wrapper-sub').show();
        $.ajax({
            url: "{{url('teenager/play-first-level-activity')}}",
            type : 'POST',
            headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
            success: function(data){
                //$('#opinionSection').fadeIn(3000);
                $('#opinionSection').show();
                $('#opinionSection').html(data);
                $('#loading-wrapper-sub').hide();
                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                $('#icon-slider').owlCarousel({
                    loop: true,
                    margin: 0,
                    items: 1,
                    autoplay: true,
                    autoplayTimeout: 3000,
                    smartSpeed: 1000,
                    nav: false,
                    dots: false
                });
                $('.loaderSection .loading-wrapper-sub').hide();
                $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            },
            error: function(){
                $('#loading-wrapper-sub').hide();
                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            }
            // error: function (xhr, ajaxOptions, thrownError) {
            //     var errorMsg = 'Ajax request failed: ' + xhr.responseText;
            //     $('#content').html(errorMsg);
            // }
        });
    }

    function saveAnswer(answer, question) {
        answer = $.trim(answer);
        question = $.trim(question);
        if(typeof question !== "undefined" && typeof answer !== "undefined" && !isNaN(answer)) {
            $('.opinion-ans-functional').fadeOut();
            $('.opinion-result').fadeIn(3000);
            
            //Save one by one records
            var form_data = 'answerId=' + answer + '&questionId=' + question;
            $.ajax({
                type: 'POST',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/teenager/save-first-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                cache: false,
                success: function(data) {
                    setTimeout(function() {
                        $('#opinionSection').hide();
                        getFirstLevelData();
                    }, 5000);
                }
            });


        } else {
            location.reload(true);
        }
    }

    $("#teenager_achievement").submit(function(event) {
        var myContent = CKEDITOR.instances.achievement.getData();
        if(myContent == "")
        {
            $(".achievement_error").text("Please add achievement!").show().fadeOut(5000);
            return false;
        }
        return true;
    });
    $("#teenager_academic").submit(function(event) {
        var myContent = CKEDITOR.instances.academic.getData();
        if(myContent == "")
        {
            $(".academic_error").text("Please add academic detail!").show().fadeOut(5000);
            return false;
        }
        return true;
    });
    
    $("input[type=password]").keyup(function() {
        var password = $(this).val();
        if (password == '') {
            $("#pass_validation").text('');
            $("#pass_validation").removeClass('invalid');
            $("#saveProfile").removeAttr("disabled");
            return true;
        } else if (password.length < 6) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least 6 characters');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.length > 20) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Password maximum range is 20');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/\d/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one number');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/[a-zA-Z]/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one character');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else if (password.search(/[!\@\#\$\%\^\&\*\(\)\_\+]/) == -1) {
            $("#pass_validation").addClass('invalid');
            $("#pass_validation").text('Use at least one special character');
            $("#saveProfile").attr("disabled", "disabled");
            return true;
        } else {
            $("#pass_validation").removeClass('invalid');
            $("#pass_validation").text('');
            $("#saveProfile").removeAttr("disabled");
            return false;
        }
    });
    function getPhoneCodeByCountry(country_id) {
        $.ajax({
            url: "{{ url('teenager/get-phone-code-by-country-for-profile') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "country_id": country_id
            },
            success: function(response) {
                $('#countrycode').text(response);
            }
        });
    }
    $("#teenager_my_profile_form").submit(function() {
        $("#saveProfile").toggleClass('sending').blur();
        var form = $("#teenager_my_profile_form");
        form.validate();
        if (form.valid()) {
            return true;
            setTimeout(function () {
                $("#saveProfile").removeClass('sending').blur();
            }, 2500);
        } else {
            $("#saveProfile").removeClass('sending').blur();
        }
    });
    $(document).on('click','#editInfo',function() {
        if ($("#t_about_info").is(':visible')) {
            $("#t_about_info").hide();
            $("#display-about-info").show();
        } else {
            $("#display-about-info").hide();
            $("#t_about_info").show();
        }
    });
    
    function playFirstLevelWorldType(type) {
        $('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        $('#loading-wrapper-sub').show();
        $("#errorGoneMsg").html('');
        $.ajax({
            url: "{{url('teenager/play-first-level-world-type')}}",
            type : 'POST',
            data : {'type' : type},
            headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
            success: function(data){
                $("#opinionSection").html(data);
                //$('#firstLevelWorldSection').show();
                //$('#firstLevelWorldSection').html(data);
                $('#loading-wrapper-sub').hide();
                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                $('.loaderSection .loading-wrapper-sub').hide();
                $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                $('#icon-slider').owlCarousel({
                    loop: true,
                    margin: 0,
                    items: 1,
                    autoplay: true,
                    autoplayTimeout: 3000,
                    smartSpeed: 1000,
                    nav: false,
                    dots: false
                });
            },
            error: function(){
                $('#loading-wrapper-sub').hide();
                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            }
        });
    }

    $(document).on('click','#load-more-connection',function(){
        var lastTeenId = $(this).data('id');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'lastTeenId=' + lastTeenId;
        $.ajax({
            url : '{{ url("teenager/load-more-my-connections") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            dataType : "text",
            success : function (data) {
                if(data != '') {
                    //$('#remove-row').remove();
                    $('.remove-my-connection-row').remove();
                    $('.my-connection').append(data);
                } else {
                    //$('#btn-more').html("No Data");
                }
            }
        });
    });
    
    $(document).on('click','#load-more-career',function(){
        var lastAttemptedId = $(this).data('id');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'lastAttemptedId=' + lastAttemptedId;
        $.ajax({
            url : '{{ url("teenager/load-more-my-careers") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            dataType : "text",
            success : function (data) {
                if(data != '') {
                    $('.remove-my-careers-row').remove();
                    $('.my-career').append(data);
                } else {
                }
            }
        });
    });

    function fetchLevel1TraitQuestion() {
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var toUserId = '';
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-level1-trait')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'toUserId':toUserId},
            success: function (response) {
                $("#traitsData").html(response);
            }
        });
    }

    function saveLevel1TraitQuestion() {

        var answerId = [];
        $.each($("input[name='traitAns']:checked"), function(){            
            answerId.push($(this).val());
        });
        var queId = $('#traitQue').val();
        var toUserId = '';
        $("#traitsData").fadeOut('slow', function() {
            $("#traitsData").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen bg-offwhite"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
            $("#traitsData").fadeIn('slow');
        });
        $("#traitsData").addClass('loading-screen-parent');
        
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/save-level1-trait')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'answerID':answerId,'questionID':queId,'toUserId':toUserId},
            success: function (response) {
                $("#traitsData").removeClass('loading-screen-parent');
                $("#traitsData").html(response).fadeIn('slow');
            }
        });
    }

    function checkAnswerChecked() {
        var answerId = [];
        $.each($("input[name='traitAns']:checked"), function(){            
            answerId.push($(this).val());
        });
        if(answerId.length != 0){
            $("#btnSaveTrait").attr("disabled", false);
            console.log(answerId);
        }else{
            $("#btnSaveTrait").attr("disabled", true);
        }
    }

    function getIconName(categoryId, categoryType, page, searchText) {
        $('.loaderSection .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        $('.loaderSection .loading-wrapper-sub').show();
        $("#errorGoneMsg").html('');
        var categoryId = categoryId;
        var dataString = 'categoryId=' + categoryId + '&categoryType=' + categoryType + '&searchText=' + searchText;
        if (categoryId != 'pop_up' && categoryId != '') {
            $('.no_selected_category').hide();
            $(".searchOnIcon").show();
            if(searchText == "") { $('#searchForIcon').val(''); }
            $("#searchForIcon").attr('onkeyup', "getIconName('"+categoryId+"', '"+categoryType+"', 1, this.value)");
            $.ajax({
                type: 'POST',
                data: dataString,
                dataType: 'html',
                url: "{{ url('/teenager/get-icon-name-new?page=') }}" + page,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                cache: false,
                success: function(data) {
                    if (data == '') {
                        $('.selected_category').hide();
                        $('.no_selected_category').val(' ');
                    } else {
                        $(".selected_category").show();
                        $('.no_selected_category').hide();
                        $(".selected_category").html(data);
                    }
                    $('.loaderSection .loading-wrapper-sub').hide();
                    $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                }
            });
        } else {
            $('.loaderSection .loading-wrapper-sub').hide();
            $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            $(".searchOnIcon").hide();
            $("#dataHtml").html('<div class="no_data_page"><span class="nodata_outer"><span class="nodata_middle">Please select one category</span></span</div>');
            $('#searchForIcon').val('');
            $('#searchForIcon').attr('onkeyup', "getIconName('', '1', 1, this.value)");
            $('.no_selected_category').show();
        }
    }

    $(function() {
        $('body').on('click', '.selected_category .pagination a', function(e) {
            e.preventDefault();
            $("#errorGoneMsg").html('');
            $('.loaderSection .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
            $('.loaderSection .loading-wrapper-sub').show();
            var categoryId = '2';
            var categoryType = '1';
            var searchText = $("#searchForIcon").val();
            var url = $(this).attr('href');
            getArticles(url);
            window.history.pushState("", "", url);
        });

        $('body').on('click', '.fictional-world #nextSubmit', function(e) {
            e.preventDefault();
            $("#errorGoneMsg").html('');
            var categoryId = $('input[name=category_id]:checked', '#level1ActivityWorldForm').val();
            var categoryType = $("#categoryIdValue").attr('data-category-type');
            var mainCategory = $("#categoryIdValue").val();
            if(mainCategory == "" || typeof mainCategory == 'undefined') {
                if($("#useForClass").hasClass('r_after_click')){
                    $("#errorGoneMsg").html('');
                }
                $("html, body").animate({
                    scrollTop: $('#errorGoneMsg').offset().top 
                }, 300);
                $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select any category!</span></div></div></div>');
                return false;
            }
            if(categoryId == "" || typeof categoryId == 'undefined') {
                if($("#useForClass").hasClass('r_after_click')){
                    $("#errorGoneMsg").html('');
                }
                $("html, body").animate({
                    scrollTop: $('#errorGoneMsg').offset().top 
                }, 300);
                $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select icon for category!</span></div></div></div>');
                return false;
            }
            var dataString = 'categoryId=' + categoryId + '&categoryType=' + categoryType;
            $('.loaderSection .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
            $('.loaderSection .loading-wrapper-sub').show();
            $.ajax({
                type: 'POST',
                data: dataString,
                dataType: 'html',
                url: "{{ url('/teenager/save-first-level-icon-category') }}",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                cache: false,
                success: function(data) {
                    try {
                        var valueOf = $.parseJSON(data); 
                    } catch (e) {
                        // not json
                    }
                    if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                        $("html, body").animate({
                            scrollTop: $('#errorGoneMsg').offset().top 
                        }, 300);
                        $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+valueOf.message+'</span></div></div></div>');
                    } else {
                        $('#errorGoneMsg').html("");
                        //$("#firstLevelWorldSection").html(data);
                        $("#opinionSection").html(data);
                    }
                    $('.loaderSection .loading-wrapper-sub').hide();
                    $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                }
            });
        });

        $('body').on('click', '.myWorldNext', function(e) {
            e.preventDefault();
            $("#errorGoneMsg").html('');
            var worldSelectionType = $('.icon_selection_select').val();
            var iconCategory3 = $("#icon_category_3").val();
            var relationsName = $("#relations_name").val();
            var firstname = $("#teen_firstname").val();
            var lastname = $("#teen_lastname").val();
            if(worldSelectionType != "" && typeof worldSelectionType !== 'undefined' && worldSelectionType == 2) {
                if(iconCategory3 == "" || relationsName == "") {
                    $("html, body").animate({
                        scrollTop: $('#errorGoneMsg').offset().top 
                    }, 300);
                    $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, fillup all required fields!</span></div></div></div>');
                    return false;
                }
                $('.loaderSection .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
                $('.loaderSection .loading-wrapper-sub').show();
            
                var form = $('#relationWorld')[0];
                var formData = new FormData(form);
                $.ajax({
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    //dataType: 'json',
                    url: "{{ url('/teenager/save-first-level-icon-category') }}",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(data) {
                        if (typeof data !== "undefined" && typeof data.status !== "undefined" && data.status == 0) {
                            $("html, body").animate({
                                scrollTop: $('#errorGoneMsg').offset().top 
                            }, 300);
                            $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+data.message+'</span></div></div></div>');
                        } else {
                            $('#errorGoneMsg').html("");
                            $("#opinionSection").html(data);
                        }
                        $('.loaderSection .loading-wrapper-sub').hide();
                        $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                    },
                    error: function() {
                        $("html, body").animate({
                            scrollTop: $('#errorGoneMsg').offset().top 
                        }, 300);
                        $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Something went wrong, Please try it again!</span></div></div></div>');
                        $('.loaderSection .loading-wrapper-sub').hide();
                        $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                    }
                });
            }
            if(worldSelectionType != "" && typeof worldSelectionType !== 'undefined' && worldSelectionType == 1) {
                if(firstname == "" || lastname == "") {
                    $("html, body").animate({
                        scrollTop: $('#errorGoneMsg').offset().top 
                    }, 300);
                    $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, fillup all required fields!</span></div></div></div>');
                    return false;
                }
                $('.loaderSection .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
                $('.loaderSection .loading-wrapper-sub').show();
            
                var form = $('#myOwnWorld')[0];
                var formData = new FormData(form);
                $.ajax({
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    //dataType: 'json',
                    url: "{{ url('/teenager/save-first-level-icon-category') }}",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(data) {
                        if (typeof data !== "undefined" && typeof data.status !== "undefined" && data.status == 0) {
                            $("html, body").animate({
                                scrollTop: $('#errorGoneMsg').offset().top 
                            }, 300);
                            $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+data.message+'</span></div></div></div>');
                        } else {
                            $('#errorGoneMsg').html("");
                            $("#opinionSection").html(data);
                        }
                        $('.loaderSection .loading-wrapper-sub').hide();
                        $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                    },
                    error: function() {
                        $("html, body").animate({
                            scrollTop: $('#errorGoneMsg').offset().top 
                        }, 300);
                        $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Something went wrong, Please try it again!</span></div></div></div>');
                        $('.loaderSection .loading-wrapper-sub').hide();
                        $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                    }
                });
            }
            //return false;
        });

        function getArticles(url) {
            //var dataString = 'categoryId=' + categoryId + '&categoryType=' + categoryType + '&searchText=' + searchText;
            $.ajax({
                type: 'POST',
            //    data: dataString,
                dataType: 'html',
                url: url,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                cache: false,
            }).done(function (data) {
                $('.loaderSection .loading-wrapper-sub').hide();
                $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                $(".selected_category").show();
                $('.no_selected_category').hide();
                $(".selected_category").html(data);
            }).fail(function () {
                $('.loaderSection .loading-wrapper-sub').hide();
                $('.loaderSection .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                $("html, body").animate({
                    scrollTop: $('#errorGoneMsg').offset().top 
                }, 300);
                $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Page data not found!</span></div></div></div>');
            });
        }
    });

    function checkQualityData() {
        var icon = $('input.iconCheck:checked').length;
        $("#errorGoneMsg").html('');
        var result = 1;
        if (icon < 5 ) {
            if($("#useForClass").hasClass('r_after_click')){
                $("#errorGoneMsg").html('');
            }
            $("html, body").animate({
                scrollTop: $('#errorGoneMsg').offset().top 
            }, 300);
            $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select atleast five Qualities</span></div></div></div>');
            return false;
        }
        return true;
    }

    function checkIconUploadData() {
        $(".errorGoneMsgPopup").text("");
        var cat1Value = $("#categoryName1").val();
        var cat1NameValue = $("#characterName1").val();
        var submitIconData = false;
        if ($("#categoryName1").val() === "" && cat1NameValue == '') {
            submitIconData = false;
            $(".errorGoneMsgPopup").text("Please, fillup all required data");
            return false;
        } else {
            submitIconData = true;
        }
        if($("#categoryName1").val() === ""){
            submitIconData = false;
            $(".errorGoneMsgPopup").text("");
            $(".errorGoneMsgPopup").text("Please, select atleast one category");
            return false;
        } else {
            submitIconData = true;
        }
        if (cat1NameValue != '') {
            if (cat1NameValue.length >= 100) {
                submitIconData = false;
                $(".errorGoneMsgPopup").text("");
                $(".errorGoneMsgPopup").text("Name field not allowed more than 100 charaters");
                return false;
            } else {
                submitIconData = true;
            }
        } else {
            submitIconData = false;
            $(".errorGoneMsgPopup").text("");
            $(".errorGoneMsgPopup").text("Please, fillup name field");
            return false;
        }
        
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        //Serialize the form data.
        //var formData = $("#fictionForm").serialize();
        var form = $('#fictionForm')[0];
        var formData = new FormData(form);
        if(submitIconData == true){
            $.ajax({
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',
                url: "{{ url('/teenager/add-icon-category')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function(response) {
                    if (response.status == 1) {
                        $("#categoryIdValue").val(response.categoryid);
                        $('#categoryIdValue').trigger("change");
                        $("#fiction_modal_icon").modal('hide');
                    } else {
                        $(".errorGoneMsgPopup").text("Something went wrong, Please try it again!");
                    }
                },
                error: function() {
                    $(".errorGoneMsgPopup").text("Something went wrong, Please try it again!");
                }
            });
        } else {
            $(".errorGoneMsgPopup").text("Please, fillup all required data");
        }
    }

    function getWorldData(categoryType) {
        if (categoryType == 2){
            $('#relation_data').show();
            $('#self_data').hide();
        } else if (categoryType == 1) {
            $('#self_data').show();
            $('#relation_data').hide();
        } else {
            $('#self_data').hide();
            $('#relation_data').hide();
        }
    }

    function checkLevel1Questions(questionAttempted) {
        if (questionAttempted == 1) {
            $("#errorGoneMsg").html('');
            $("html, body").animate({
                scrollTop: $('#errorGoneMsg').offset().top 
            }, 300);
            $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">All options voted!</span></div></div></div>');
        }
    }
</script>
@stop