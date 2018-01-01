@extends('layouts.teenager-master')

@push('script-header')
    <title>Members</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sec-member">
        <div class="container">
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
                        <div class="profile-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }}')">

                        </div>
                    </div>
                    <div class="col-sm-9">
                        <h1>Alex Murphy</h1>
                        <ul class="area-detail">
                            <li>Miami Area</li>
                            <li>87 Connections </li>
                        </ul>
                        <ul class="social-media">
                            <li><a href="#" title="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                            <li><a href="#" title="google plus" target="_blank"><i class="icon-google"></i></a></li>
                        </ul>
                        <div class="chat-icon add-icon">
                            <a href="#" title="Add"><i class="icon-add-circle"></i></a>
                        </div>
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
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest.png" alt="interest img">-->
                                    <div class="progress-radial progress-90 progress-orange">
                                    </div>
                                    <figcaption>Interest 1</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest.png" alt="interest img">-->
                                    <div class="progress-radial progress-95 progress-orange">
                                    </div>
                                    <figcaption>Interest 2</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!-- <img src="img/my-interest-1.png" alt="interest img">-->
                                    <div class="progress-radial progress-75 progress-orange">
                                    </div>
                                    <figcaption>Interest 3</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-1.png" alt="interest img">-->
                                    <div class="progress-radial progress-95 progress-orange">
                                    </div>
                                    <figcaption>Interest 4</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-1.png" alt="interest img">-->
                                    <div class="progress-radial progress-60 progress-orange">
                                    </div>
                                    <figcaption>Interest 5</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-1.png" alt="interest img">-->
                                    <div class="progress-radial progress-65 progress-orange">
                                    </div>
                                    <figcaption>Interest 6</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-1.png" alt="interest img">-->
                                    <div class="progress-radial progress-30 progress-orange">
                                    </div>
                                    <figcaption>Interest 7</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-2.png" alt="interest img">-->
                                    <div class="progress-radial progress-80 progress-orange">
                                    </div>
                                    <figcaption>Interest 8</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-2.png" alt="interest img">-->
                                    <div class="progress-radial progress-75 progress-orange">
                                    </div>
                                    <figcaption>Interest 9</figcaption>
                                </figure>
                            </li>
                            <li>
                                <figure>
                                    <!--<img src="img/my-interest-2.png" alt="interest img">-->
                                    <div class="progress-radial progress-80 progress-orange">
                                    </div>
                                    <figcaption>Interest 10</figcaption>
                                </figure>
                            </li>
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
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart.png" alt="interest img">-->
                                        <div class="progress-radial progress-90">
                                        </div>
                                        <figcaption>Strength 1</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart.png" alt="interest img">--><div class="progress-radial progress-25">
                                        </div>
                                        <figcaption>Strength 2</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart2.png" alt="interest img">-->
                                        <div class="progress-radial progress-70">
                                        </div>
                                        <figcaption>Strength 3</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                       <!--<img src="img/My_chart2.png" alt="interest img">-->
                                       <div class="progress-radial progress-65">
                                        </div>
                                        <figcaption>Strength 4</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart2.png" alt="interest img">-->
                                        <div class="progress-radial progress-85">
                                        </div>
                                        <figcaption>Strength 5</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart2.png" alt="interest img">-->
                                        <div class="progress-radial progress-55">
                                        </div>
                                        <figcaption>Strength 6</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart2.png" alt="interest img">--><div class="progress-radial progress-90">
                                        </div>
                                        <figcaption>Strength 7</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart3.png" alt="interest img">--><div class="progress-radial progress-95">
                                        </div>
                                        <figcaption>Strength 8</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart3.png" alt="interest img">--><div class="progress-radial progress-85">
                                        </div>
                                        <figcaption>Strength 9</figcaption>
                                    </figure>
                                </li>
                                <li>
                                    <figure>
                                        <!--<img src="img/My_chart3.png" alt="interest img">--><div class="progress-radial progress-80">
                                        </div>
                                        <figcaption>Strength 10</figcaption>
                                    </figure>
                                </li>
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
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <img src="{{ Storage::url('img/ellen.jpg') }}" alt="team">
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
                                        <img src="{{ Storage::url('img/alex.jpg') }}" alt="team">
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
    <!--mid section end-->
@stop
