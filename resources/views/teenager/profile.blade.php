@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Profile</title>
@endpush

@section('content')

    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile">
        <div class="container">
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
                <div class="row">
                    <div class="col-sm-3">
                        <div class="profile-img" style="background-image: url({{ $data['user_profile'] }})">

                        </div>
                        <span class="complete-detail">Profile 62% complete</span>
                    </div>
                    <div class="col-sm-9">
                        <h1>{{ $user->t_name }}</h1>
                        <ul class="area-detail">
                            <li>{{ ( Auth::guard('teenager')->user()->getCountry->c_name != "" ) ? Auth::guard('teenager')->user()->getCountry->c_name : "Miami" }} Area</li>
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
                    <form>
                        <div class="clearfix row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="name" placeholder="First Name *" tabindex="1" value="{{ $user->t_name }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="name" placeholder="Email *" tabindex="3" value="{{ $user->t_email }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="number" class="form-control" id="name" placeholder="Phone" tabindex="5" value="{{ $user->t_phone }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group input-group">
                                    <span class="input-group-addon">+91</span>
                                    <input type="number" class="form-control" id="name" placeholder="Mobile Phone *" tabindex="6" value="{{ $user->t_phone }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group custom-select">
                                    <select tabindex="7" class="form-control" name="country" onchange="getPhoneCodeByCountry(this.value);" required>
                                        <option value="">Country</option>
                                        @forelse($countries as $val)
                                            <option value="{{$val->id}}" <?php echo (old('country') && old('country') == $val->id ) ? "selected='selected'" : ''; ?> > {{$val->c_name}} </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <input type="hidden" name="country_phone_code" id="country_phone_code" readonly="readonly" id="country_phone_code" class="cst_input_primary" maxlength="10" placeholder="Phone Code" value="{{old('country_phone_code')}}">
                                </div>
                            </div>
                            <input type="hidden" name="country_phone_code" id="country_phone_code" readonly="readonly" id="country_phone_code" class="cst_input_primary" maxlength="10" placeholder="Phone Code" value="{{old('country_phone_code')}}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="number" class="form-control" id="name" placeholder="Postal (Zip) Code*" tabindex="8" value="{{ $user->t_pincode }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="password-info">Password info</span>
                                    <input type="password" class="form-control" id="name" placeholder=" password *" tabindex="11" value="{{ $user->password }}" maxlength="16" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group custom-select">
                                    <select tabindex="9" class="form-control" name="gender" required >
                                        <option value="1" <?php echo (old('gender') && old('gender') == 1) ? "selected='selected'" : ''; ?> >Male</option>
                                        <option value="2" <?php echo (old('gender') && old('gender') == 2) ? "selected='selected'" : ''; ?> >Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="proteen_code" id="proteen_code" placeholder="ProTeen code" tabindex="10" value="{{old('proteen_code')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="number" class="form-control" id="name" placeholder="Postal (Zip) Code*" tabindex="8" value="{{ $user->t_pincode }}">
                                </div>
                            </div>
                            <div class="col-sm-9 col-sm-offset-3 text-right">
                                <div class="form-group date-sec">
                                    <label>Date of Birth:</label>
                                    <div class="date-feild">
                                        <select name="month" class="form-control date-block" id="month" tabindex="13">
                                            <option value="">mm</option>
                                            @for($month = 01; $month <= 12; $month++)
                                                <option value="{{date('m', mktime(0,0,0,$month, 1, date('Y')))}}">{{ date('F', mktime(0,0,0,$month, 1, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="day" class="form-control date-block" id="day" tabindex="14" >
                                            <option value=""></option>
                                            @for($day = 1; $day <= 31; $day++)
                                                <option value="{{date('d', mktime(0,0,0,0, $day, date('Y')))}}">{{ date('d', mktime(0,0,0,0, $day, date('Y'))) }}</option>
                                            @endfor
                                        </select>
                                        <select name="year" class="form-control date-block" id="year" tabindex="15">
                                            <option value="">yyyy</option>
                                            @foreach(range(\Carbon\Carbon::now()->year, 1950) as $year)
                                                <option value="{{$year}}">{{$year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="sponsor-sec">
                                    <div class="sponsor-content">
                                        <div class="form-register sponsor-list">
                                            @forelse($sponsorDetail as $key => $value)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="selected_sponsor[]" id="sponsor_{{$key}}" value="{{$value->sponsor_id}}" <?php (old('selected_sponsor') && in_array($value->sponsor_id, old('selected_sponsor')) ) ? "checked" : ""; ?> />
                                                        <span class="checker"></span>
                                                        <span class="logo-icon">
                                                            <?php
                                                                $sponsor_logo = ($value->sp_logo != "") ? Storage::url(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH').$value->sp_logo) : asset(Config::get('constant.SPONSOR_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                                            ?>
                                                            <img src="{{ $sponsor_logo }}" alt="{{ $value->sp_company_name }}" height="74px" width="127px">
                                                        </span>{{ str_limit($value->sp_company_name, $limit = 20, $end = '..') }}
                                                    </label>
                                                </div>
                                            @empty

                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7 text-right">
                                <ul class="toggle-switch">
                                    <li>Public Profile
                                        <label class="switch">
                                      <input type="checkbox">
                                          <span class="slider round"></span>
                                    </label>
                                    </li>
                                    <li>Share info with other members
                                        <label class="switch">
                                      <input type="checkbox">
                                          <span class="slider round"></span>
                                    </label>
                                    </li>
                                    <li>Share info with parents
                                        <label class="switch">
                                      <input type="checkbox">
                                          <span class="slider round"></span>
                                    </label>
                                    </li>
                                    <li>Share info with teachers
                                        <label class="switch">
                                      <input type="checkbox">
                                          <span class="slider round"></span>
                                    </label>
                                    </li>
                                    <li>Notifications
                                        <label class="switch">
                                      <input type="checkbox">
                                          <span class="slider round"></span>
                                    </label>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center col-sm-12">
                                <button class="btn btn-submit" type="Submit" title="Submit">Submit</button>
                                <span class="hand-icon"><i class="icon-hand-simple"></i></span>
                            </div>
                        </div>
                    </form>
                </div>
                <!--profile form end-->
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
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="email" placeholder="Email" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group custom-select">
                                <select class="form-control">
                              <option value="Parent">Parent</option>
                              <option value="Mentor 1">Mentor 1</option>
                              <option value="Mentor 2">Mentor 2</option>
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
                <div class="row">
                    <div class="col-sm-3 col-xs-6">
                        <figure>
                            <div class="mentor-img" style="background-image: url({{Storage::url('img/parent-1.jpg')}})"></div>
                            <figcaption>Parent 1</figcaption>
                        </figure>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <figure>
                            <div class="mentor-img" style="background-image: url({{Storage::url('img/parent-2.jpg')}})"></div>
                            <figcaption>Parent 2</figcaption>
                        </figure>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <figure>
                            <div class="mentor-img" style="background-image: url({{Storage::url('img/mentor-1.jpg')}})"></div>
                            <figcaption>mentor 1</figcaption>
                        </figure>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <figure>
                            <div class="mentor-img" style="background-image: url({{Storage::url('img/mentor-2.jpg')}})"></div>
                            <figcaption>mentor 1</figcaption>
                        </figure>
                    </div>
                </div>
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
            <div class="bg-white my-progress">
                <!--<ul class="nav nav-tabs progress-tab">
                    <li class="acheivement active"><a data-toggle="tab" href="#menu1">Achievements <span class="count">(10)</span></a></li>
                    <li class="career"><a data-toggle="tab" href="#menu2">My Careers <span class="count">(18)</span></a></li>
                    <li class="connection"><a data-toggle="tab" href="#menu3">My Connections <span class="count">(56)</span></a></li>
                </ul>-->
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
    <!--sec learning guidance end-->
    <!--achievement record-->
    <section class="achievement-record sec-record">
        <div class="container">
            <h2>Achievement Record</h2>
            <p><a href="#" title="Edit Detail">Edit Detail</a></p>
        </div>
    </section>
    <!--achievement record end-->
    <!--academic record-->
    <section class="academic-record sec-record">
        <div class="container">
            <h2>Academic Record</h2>
            <p><a href="#" title="Edit Detail">Edit Detail</a></p>
        </div>
    </section>
    <!--academic record end-->
    <!--mid section end-->
    
@stop
