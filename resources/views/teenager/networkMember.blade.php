@extends('layouts.teenager-master')

@push('script-header')
    <title>Members</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sec-member">
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
                        <a class="close popover-closer"><i class="icon-close"></i></a> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                    </div>
                </div>
            </div>
            <!--profile detail-->
            <div class="profile-detail member-detail">
                <div class="row">
                    <div class="col-sm-3">
                        <?php
                        if(isset($teenDetails->t_photo) && $teenDetails->t_photo != '') {
                            $teenPhoto = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH').$teenDetails->t_photo;
                        } else {
                            $teenPhoto = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                        }
                        ?>
                        <div class="profile-img" style="background-image: url('{{ Storage::url($teenPhoto) }}')">
                        </div>
                    </div>
                    <?php
                        if($teenDetails->t_pincode != "")
                        {
                            $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenDetails->t_pincode.'&sensor=true');
                            $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                        } else {
                            $getCityArea = ( $teenDetails->getCountry->c_name != "" ) ? $teenDetails->getCountry->c_name : "Default";
                        }
                        ?>
                    <div class="col-sm-9">
                        <h1>{{$teenDetails->t_name}}</h1>
                        <ul class="area-detail">
                            <li>{{ $getCityArea }} Area</li>
                            <li>{{ $myConnectionCount }} {{ ($myConnectionCount == 1) ? "Connection" : "Connections" }} </li>
                        </ul>
                        <ul class="social-media">
                            <li><a href="#" title="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                            <li><a href="#" title="google plus" target="_blank"><i class="icon-google"></i></a></li>
                        </ul>
                        @if (!empty($connectedTeen) && $connectedTeen == true)
                        <div class="chat-icon add-icon">
                            <a href="{{ url('teenager/send-request-to-teenager') }}/{{ $teenDetails->t_uniqueid }}" title="Add"><i class="icon-add-circle"></i></a>
                        </div>
                        @endif
                        <div class="chat-icon">
                            <a href="#" title="Chat"><i class="icon-chat"></i></a>
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse suscipit eget massa ac consectetur. Nunc fringilla mattis mi, sit amet hendrerit nibh euismod in. Praesent ut vulputate sem. Vestibulum odio quam, sagittis vitae pellentesque sit amet, rhoncus sit amet ipsum. Ut eros risus, molestie sed sapien at, euismod dignissim velit.</p>
                    </div>
                </div>
                <div class="text-center">
                    <ul class="sec-traits">
                        <li>
                            <div class="ck-button">
                                Technologist
                            </div>
                        </li>
                        <li>
                            <div class="ck-button">
                                Writer
                            </div>
                        </li>
                        <li>
                            <div class="ck-button">
                                Explorer
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!--profile detail end-->
        </div>
    </section>
    <!-- profile section-->
    <!-- sec personal survey-->
    <div class="sec-survey describe-traits">
        <div class="container">
            <p>Choose three traits you feel describe Alex:</p>
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
                    <a href="#" title="Submit">Submit</a>
                </div>
            </div>
        </div>
    </div>
    <!-- sec personal survey end-->
    <!--sec progress-->
    <section class="sec-progress sec-tab">
        <div class="container">
            <div class="bg-white my-progress border-tab">
                <ul class="nav nav-tabs custom-tab-container clearfix">
                    <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Interests</span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-2 tab-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Strengths</span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Connections</span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
                        <div class="sec-popup">
                            <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop2" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="pop2">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                </div>
                            </div>
                        </div>
                        <ul class="badge-list interest-list clearfix">
                            <?php 
                                $interestFlag = ''; 
                                if (!empty(array_filter($teenagerInterest))) {
                                    $interestFlag = true;
                                } else {
                                    $interestFlag = false;
                                }
                            ?>
                            @if ($interestFlag == true)
                                @forelse($teenagerInterest as $interestKey => $interestValue)
                                <?php if ($interestValue < 1) { continue; } ?> 
                                <li>
                                    <figure>
                                        <div class="progress-radial progress-90 progress-orange">
                                        </div>
                                        <figcaption><?php echo Helpers::getInterestBySlug($interestKey); ?></figcaption>
                                    </figure>
                                </li>
                                @empty
                                <center>
                                    <h3>No Records found.</h3>
                                </center>
                                @endforelse
                            @else
                            <center>
                                <h3>No Records found.</h3>
                            </center>
                            @endif
                        </ul>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="sec-popup">
                            <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop3" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="pop3">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                </div>
                            </div>
                        </div>
                        <div class="strength-list">
                            <ul class="badge-list interest-list clearfix">
                                @forelse($teenagerStrength as $strengthKey => $strengthValue)
                                <?php $imageChart = "img/My_chart-".$strengthValue['score'].".png"; ?>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart.png" alt="interest img">-->
                                        <div class="progress-radial progress-90">
                                        </div>
                                        <figcaption>{{ $strengthValue['name'] }}</figcaption>
                                    </figure>
                                </li>
                                @empty
                                <center>
                                    <h3>No Records found.</h3>
                                </center>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade">
                        <div class="sec-popup">
                            <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop4" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="pop4">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                </div>
                            </div>
                        </div>
                        @forelse($myConnections as $myConnection)
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <?php
                                            if(isset($myConnection->t_photo) && $myConnection->t_photo != '') {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$newConnection->t_photo;
                                            } else {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                            }
                                        ?>
                                        <img src="{{ Storage::url($teenImage) }}" alt="team">
                                    </div>
                                    <a href="#" title="{{ $myConnection->t_name }}"> {{ $myConnection->t_name }}</a>
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
                                <h3>No Records found.</h3>
                            </center>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--sec progress end-->
    <!--mid section end-->
@stop
