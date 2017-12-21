@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Profile</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sponsor-overflow">
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
                                <li>87 Connections </li>
                            </ul>
                            <ul class="social-media">
                                <li><a href="#" title="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                                <li><a href="#" title="google plus" target="_blank"><i class="icon-google"></i></a></li>
                            </ul>
                            <div class="chat-icon">
                                <a href="#" title="Chat"><i class="icon-chat"></i>
                                    <span>3</span></a>
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse suscipit eget massa ac consectetur. Nunc fringilla mattis mi, sit amet hendrerit nibh euismod in. Praesent ut vulputate sem. Vestibulum odio quam, sagittis vitae pellentesque sit amet, rhoncus sit amet ipsum. Ut eros risus, molestie sed sapien at, euismod dignissim velit.</p>
                        </div>
                    </div>
                    <!--profile form-->
                    <div class="profile-form">
                        <div class="clearfix row flex-container">
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="name" name="name" placeholder="First Name *" tabindex="1" value="{{ $user->t_name }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control alphaonly" id="lastname" name="lastname" placeholder="Last Name *" tabindex="2" value="{{ $user->t_lastname }}" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email *" tabindex="3" value="{{ $user->t_email }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="text" class="form-control onlyNumber" id="phone" name="phone" placeholder="Phone" minlength="7" maxlength="10" tabindex="5" value="{{ $user->t_phone_new }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group input-group">
                                    <div class="clearfix">
                                        <span id="countrycode" class="input-group-addon">+91</span>
                                        <input type="text" class="form-control onlyNumber" id="mobile" name="mobile" maxlength="10" placeholder="Mobile Phone *" tabindex="6" value="{{ $user->t_phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group custom-select">
                                    <select tabindex="7" class="form-control" id="country"name="country" onchange="getPhoneCodeByCountry(this.value);" required>
                                        <option value="">Country</option>
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
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Postal (Zip) Code*" tabindex="8" value="{{ $user->t_pincode }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 flex-items">
                                <div class="form-group">
                                    <input type="password" class="form-control pass-visi" id="password" name="password" placeholder="Password" tabindex="11" value="" maxlength="16">
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
                                    <input type="text" class="form-control alphaonly" id="proteen_code" name="proteen_code" placeholder="ProTeen code" tabindex="10" value="{{ $user->t_nickname }}">
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
                                    <li>Public Profile
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
                                    <li>View Information For
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
                                <button id="saveProfile" class="btn btn-submit" type="submit" title="Submit">Submit</button>
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
    <section class="sec-parents">
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
                            <button class="btn btn-submit" type="submit" title="a=Add">Add</button>
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
    <div class="sec-survey">
        <div class="container">

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
            <p>Choose three traits that you feel describe you:</p>
            <div class="survey-list">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Technologist</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Adventurer</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Geek</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Entrepreneur</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Writer</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Artist</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Explorer</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Thinker</span></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="ck-button">
                            <label><input type="checkbox" value="1"><span>Tree Hugger</span></label>
                        </div>
                    </div>
                </div>
                <div class="form-btn">
                    <span class="icon"><i class="icon-arrow-spring"></i></span>
                    <a href="#" title="Next">Next</a>
                </div>
            </div>
        </div>
    </div>
    <!-- sec personal survey end-->
    <!--sec progress-->
    <section class="sec-progress">
        <div class="container">
            <h2>My Progress</h2>
            <div class="bg-white my-progress profile-tab">
                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                    <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Achievements <span class="count">(10)</span></span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">My Careers <span class="count">(18)</span></span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">My Connections <span class="count">(56)</span></span></span></a></li>
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
                        <div class="careers-tab">
                            <div class="careers-block">
                                <div class="careers-img">
                                    <i class="icon-image"></i>
                                </div>
                                <div class="careers-content">
                                    <h4>lorem ipsum</h4>
                                </div>
                            </div>
                            <div class="careers-block">
                                <div class="careers-img">
                                    <i class="icon-image"></i>
                                </div>
                                <div class="careers-content">
                                    <h4>lorem ipsum</h4>
                                </div>
                            </div>
                            <div class="careers-block">
                                <div class="careers-img">
                                    <i class="icon-image"></i>
                                </div>
                                <div class="careers-content">
                                    <h4>lorem ipsum</h4>
                                </div>
                            </div>
                            <div class="careers-block">
                                <div class="careers-img">
                                    <i class="icon-image"></i>
                                </div>
                                <div class="careers-content">
                                    <h4>lorem ipsum</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade">
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <img src="{{Storage::url('img/ellen.jpg')}}" alt="team">
                                    </div>
                                    <a href="#" title="Ellen Ripley"> Ellen Ripley</a>
                                </div>
                            </div>
                            <div class="flex-item">
                                <div class="team-point">
                                    520,000 points
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <img src="{{Storage::url('img/alex.jpg')}}" alt="team">
                                    </div>
                                    <a href="#" title="Alex Murphy">Alex Murphy</a>
                                </div>
                            </div>
                            <div class="flex-item">
                                <div class="team-point">
                                    515,000 points
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--sec progress end-->
    <!--sec learning guidance-->
    <section class="sec-guidance">
        <div class="container">
            <h2>Learning Guidance</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin volutpat eros libero, et sagittis metus posuere id. Mauris mattis velit risus, nec tristique erat mattis sit amet. Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet mattis ac sit amet turpis. Proin a neque hendrerit, molestie lacus non, elementum velit. Nunc mattis justo magna, tempor faucibus diam commodo sit amet. Vestibulum id lectus eget dui rutrum tristique quis eget nulla. Vivamus mattis aliquet est. Mauris dapibus, magna sagittis pharetra suscipit, felis tortor mollis metus, non commodo erat arcu finibus risus. Mauris id ante eget lectus iaculis pellentesque eu efficitur nisl. Proin sagittis nec orci ut tincidunt. Aliquam sed turpis mauris. Cras nisl quam, vulputate ac sapien ut, hendrerit faucibus ligula.</p>
            <p>Maecenas fringilla eros vitae eros volutpat, quis mattis metus dictum. Etiam ac rhoncus elit, ac consequat urna. Morbi nec dignissim urna. Phasellus non laoreet dui. Nullam id auctor nibh, eu porta lorem. In eleifend elit quis ante interdum, mollis interdum erat condimentum. Aliquam porta turpis justo.</p>
            <p class="text-center"><a href="#" title="learn more" class="btn btn-primary">learn more</a></p>
        </div>
    </section>
    <div class="sec-record">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="collapsed achievement">Achievement Record<span>Edit</span></a>
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
                        <a data-parent="#accordion" data-toggle="collapse" href="#accordion2" class="collapsed academic">Academic Record<span>Edit</span></a>
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
<script src="//cdn.ckeditor.com/4.5.8/standard/ckeditor.js"></script>
<script>
    $('.onlyNumber').on('keyup', function() {
            this.value = this.value.replace(/[^0-9]/gi, '');
        });
    $('.alphaonly').bind('keyup blur', function() {
            var node = $(this);
            node.val(node.val().replace(/[^a-zA-Z_' ]/g, ''));
        });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var a = document.querySelector("#img-preview");
                if (input.files[0].size > 3000000) {
                    alert("File size is too large. Maximum 3MB allowed");
                    $(this).val('');
                } else {
                    a.style.backgroundImage = "url('" + e.target.result + "')";
                    // document.getElementById("#").className = "activated";
                    a.className = "upload-img activated";
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
        
    $(document).ready(function() {
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
                    required: "Pincode is required",
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
                    required: "Select at least one sponsor",
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
    });
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
</script>
@stop