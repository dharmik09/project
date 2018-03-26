@extends('layouts.parent-master')

@section('content')
<div id="dashboard_modal" class="modal fade info_modal skill_pop_up close_modal" role="dialog">
    <div class="loader" id="page_loader" style="display:none;">
        <div class="cont_loader">
            <div class="img1"></div>
            <div class="img2"></div>
        </div>
    </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close close_next model_close" data-dismiss="modal"><i class="icon-close"></i></button>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
            <div class="modal-body">
                <div class="default_content left_align">
                    <!-- dashboard_inner_box End -->
                    <div class="dashboard_inner_box dash_progress my_intrest" style="transition: all 0.5s !important">
                        <div class="centerlize">
                            <div class="container">
                                <div class="parent_assessment">
                                    <div id="errorGoneMsg"></div>
                                    <div class="section" id="section1">
                                        <h1><span class="title_border"><span class="parent_assessment_header_text">How well do you know your Teen ?</span><br/>VOTE your
                                            <span class="high_label">H - High,</span>
                                            <span class="mid_label"> M - Moderate,</span>
                                            <span class="low_label">L - Low </span> on 24 aspects</span></h1>
                                        <div id="displayAssessmentData1">
                                            @if(isset($response['teenagerMIData']) && !empty($response['teenagerMIData']))
                                            <form id="assesmentDataM" method="post">
                                                <ul class="parameters">
                                                @foreach($response['teenagerMIData'] as $key=>$val)
                                                    <li class="boxy">
                                                        <h3>{{ $val['aptitude']}}</h3>
                                                        <img src="{{ $val['image']}}" alt="">
                                                        <div class="rating">
                                                            <span class="radio_cont">
                                                                <input type="radio" class="square_radio square_radio1" id="mi_data_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="H" <?php if($val['parentScale'] == 'H') {echo 'checked="checked"'; }?>>
                                                                <label for="mi_data_{{$key}}" class="high">
                                                                    <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                    <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                    <span class="rating_label">H</span>
                                                                </label>
                                                            </span>
                                                            <span class="radio_cont">
                                                                <input type="radio" class="square_radio square_radio1" id="mi_data1_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="M" <?php if($val['parentScale'] == 'M') {echo 'checked="checked"'; }?>>
                                                                <label for="mi_data1_{{$key}}" class="mid">
                                                                    <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                    <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                    <span class="rating_label">M</span>
                                                                </label>
                                                            </span>
                                                            <span class="radio_cont">
                                                                <input type="radio" class="square_radio square_radio1" id="mi_data2_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="L" <?php if($val['parentScale'] == 'L') {echo 'checked="checked"'; }?>>
                                                                <label for="mi_data2_{{$key}}" class="low">
                                                                    <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                    <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                    <span class="rating_label">L</span>
                                                                </label>
                                                            </span>
                                                        <?php $introText = $val['aptitude'] . (isset($val['info']) && ($val['info'] != '') ? ' - ' . $val['info'] : ''); ?>
                                                        <span class="video_card"><a href="javascript:void(0)" onclick="openIntroVideo('{{$val['video']}}', '{{$introText}}')"><i class="fa fa-play-circle" aria-hidden="true"></i></a></span>
                                                    </div>
                                                        <div class="system_assessment high">{{ $val['scale'] }}</div>
                                                    </li>
                                                @endforeach
                                                </ul>
                                            </form>
                                                <div class="clearfix button_container_parent_assessment">
                                                    <a href="javascript:void(0);" class="assessment_btn btn primary_btn pull-right" data-divrefrence="#section2" onclick="savePromiseRate(1,'section2');">Next Section</a>
                                                </div>
                                            @else
                                                <div class="no_data">No data found</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="section" id="section2" style="display: none;">
                                        <h1><span class="title_border">How much do you know your Teen ?<br/>v your
                                            <span class="high_label">H</span>
                                            <span class="mid_label">M</span>
                                            <span class="low_label">L</span> VOTE on these 24 aspects</span></h1>
                                        <div id="displayAssessmentData2">
                                            @if(isset($response['teenagerPersonalityData']) && !empty($response['teenagerPersonalityData']))
                                                <form id="assesmentDataP" method="post">
                                                    <ul class="parameters">
                                                    @foreach($response['teenagerPersonalityData'] as $key=>$val)
                                                        <li class="boxy">
                                                            <h3>{{ $val['aptitude']}}</h3>
                                                            <img src="{{ $val['image']}}" alt="">
                                                            <div class="rating">
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio2" id="per_data_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="H" <?php if($val['parentScale'] == 'H') {echo 'checked="checked"'; }?>>
                                                                    <label for="per_data_{{$key}}" class="high">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">H</span>
                                                                    </label>
                                                                </span>
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio2" id="per_data2_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="M" <?php if($val['parentScale'] == 'M') {echo 'checked="checked"'; }?>>
                                                                    <label for="per_data2_{{$key}}" class="mid">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">M</span>
                                                                    </label>
                                                                </span>
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio2" id="per_data3_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="L" <?php if($val['parentScale'] == 'L') {echo 'checked="checked"'; }?>>
                                                                    <label for="per_data3_{{$key}}" class="low">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">L</span>
                                                                    </label>
                                                                </span>
                                                            <?php $introText = $val['aptitude'] . (isset($val['info']) && ($val['info'] != '') ? ' - ' . $val['info'] : ''); ?>
                                                            <span class="video_card"><a href="javascript:void(0)" onclick="openIntroVideo('{{$val['video']}}', '{{$introText}}')"><i class="fa fa-play-circle" aria-hidden="true"></i></a></span>
                                                        </div>
                                                            <div class="system_assessment high">{{ $val['scale'] }}</div>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </form>
                                                <div class="clearfix button_container_parent_assessment">
                                                    <a href="javascript:void(0);" class="assessment_btn btn primary_btn pull-left assessment_btn_click" data-divrefrence="#section1">Previous Section</a>
                                                    <a href="javascript:void(0);" class="assessment_btn btn primary_btn pull-right" data-divrefrence="#section3" onclick="savePromiseRate(2,'section3');">Next Section</a>
                                                </div>
                                            @else
                                                <div class="no_data">No data found</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="section" id="section3" style="display: none;">
                                        <h1><span class="title_border">How much do you know your Teen ?<br/>v your
                                            <span class="high_label">H</span>
                                            <span class="mid_label">M</span>
                                            <span class="low_label">L</span> VOTE on these 24 aspects</span></h1>
                                        <div id="displayAssessmentData3">
                                            @if(isset($response['teenagerApptitudeData']) && !empty($response['teenagerApptitudeData']))
                                                <form id="assesmentDataA" method="post">
                                                    <ul class="parameters">
                                                    @foreach($response['teenagerApptitudeData'] as $key=>$val)
                                                        <li class="boxy">
                                                            <h3>{{ $val['aptitude']}}</h3>
                                                            <img src="{{ $val['image']}}" alt="">
                                                            <div class="rating">
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio3" id="ap_data_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="H" <?php if($val['parentScale'] == 'H') {echo 'checked="checked"'; }?>>
                                                                    <label for="ap_data_{{$key}}" class="high">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">H</span>
                                                                    </label>
                                                                </span>
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio3" id="ap_data2_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="M" <?php if($val['parentScale'] == 'M') {echo 'checked="checked"'; }?>>
                                                                    <label for="ap_data2_{{$key}}" class="mid">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">M</span>
                                                                    </label>
                                                                </span>
                                                                <span class="radio_cont">
                                                                    <input type="radio" class="square_radio square_radio3" id="ap_data3_{{$key}}" name="mi_data[{{$val['type']}}][{{$val['aptitude']}}]" value="L" <?php if($val['parentScale'] == 'L') {echo 'checked="checked"'; }?>>
                                                                    <label for="ap_data3_{{$key}}" class="low">
                                                                        <span class="uncheck"><i class="fa fa-square-o" aria-hidden="true"></i></span>
                                                                        <span class="check"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                        <span class="rating_label">L</span>
                                                                    </label>
                                                                </span>
                                                            <?php $introText = $val['aptitude'] . (isset($val['info']) && ($val['info'] != '') ? ' - ' . $val['info'] : ''); ?>
                                                            <span class="video_card"><a href="javascript:void(0)" onclick="openIntroVideo('{{$val['video']}}', '{{$introText}}')"><i class="fa fa-play-circle" aria-hidden="true"></i></a></span>
                                                        </div>
                                                            <div class="system_assessment high">{{ $val['scale'] }}</div>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </form>
                                                <div class="clearfix button_container_parent_assessment">
                                                    <a href="javascript:void(0);" class="assessment_btn btn primary_btn pull-left assessment_btn_click" data-divrefrence="#section2">Previous Section</a>
                                                    <a href="javascript:void(0);" class="assessment_btn btn primary_btn pull-right" data-divrefrence="#section3" onclick="savePromiseRate(3,'');">Submit</a>
                                                </div>
                                            @else
                                                <div class="no_data">No data found</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade default_popup HML_popup" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close close_next" data-dismiss="modal"><i class="icon-close"></i></button>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName"></h2></div>
            <div class="modal-body body_sticky_not">
                <div class="basket_iframe_video"><span id="operationVideoNot"></span></div>
            </div>
            <div class="modal-body body_sticky">
                <div class="basket_iframe_video"><iframe id="operationVideo" src="" frameborder="0" allowfullscreen=""></iframe></div>
            </div>
        </div>
    </div>
</div>
<div class="centerlize">
    <div class="container">
        <div id="errorMsg"></div>
        <form name="displayTeen" method="post" action="" >
            <div class="container_padd school_progress">
                <div class="row">
                    <div class="col-md-6 col-sm-6 select_cst">
                        <div class="col-md-3 col-sm-5 teen_label"><span>Select Teen:</span></div>
                        <div class="col-md-6 col-sm-7 teen_drop">
                            <div class="select-style">
                                <select id="teenName" name="teen_Name">
                                    @if(isset($response['finalTeens']) && $response['finalTeens'])
                                    <?php $teenId = $teenDetail->id; ?>
                                    @foreach($response['finalTeens'] as $key=>$val)
                                    <option value="{{$val['unique_id']}}" <?php
                                    if ($val['unique_id'] == $teenDetail->t_uniqueid) {
                                        echo 'selected="selected"';
                                    }
                                    ?> >{{$val['name']}} {{$val['nickname']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 select_cst">
                        <div class="col-md-12 col-sm-12 teen_drop report_download">
                        @if ($response['remainingDays'] != 0)
                            <div class="promisebtn timer_btn">
                                <a href="javascript:void(0);" class="promise btn_golden_border reportbtn" title="" id="report" disabled>
                                    <span class="promiseplus">Report <i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                                    <span class="coinouter">
                                        <span class="coinsnum">{{$response['remainingDays']}} Days Left</span>
                                    </span>
                                </a>
                            </div>
                        @else
                            <div id="RdaysReport" >
                              <div class="promisebtn">
                                <a href="javascript:void(0)" style="margin-top: 0 ;" class="promise btn_golden_border reportbtn" id="report" disabled>
                                  <span class="promiseplus">Report <i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                                  <span class="coinouter">
                                      <span class="coinsnum">{{$response['coins']}}</span>
                                      <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}">
                                      </span>
                                  </span>
                                </a>
                              </div>
                          </div>
                        @endif
                    </div>
                </div><!-- Row End -->

                <div class="clearfix col-md-12">
                    <div class="row">
                        <div class="parent_h2_header col-xs-12">
                            <h2><span class="l-1"><span class="level_label">L-1</span></span> Results &amp; Trends</h2>
                        </div>
                    </div>
                    @if(isset($response['level1result']) && !empty($response['level1result']))
                        <div class="table_container fixed_box_type" style="height:300px;">
                            <table class="sponsor_table">
                                <tr>
                                    <th>Questions</th>
                                    <th>Teen Response</th>
                                    <th>Teen Trends</th>
                                </tr>

                                @foreach($response['level1result'] as $key=>$val)
                                <tr>
                                    <td>{{$val['question_text']}}</td>
                                    <td>{{$val['teen_anwer']}}</td>
                                    <td>{{$val['trend']}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        <div class="no_data col-xs-12" style="margin: 40px 0px;text-align:center;">Not Attempted Level 1 yet...</div>
                        <!--<div class="no_data_page  col-xs-12">
                            <span class="nodata_outer">
                                <span class="nodata_middle">
                                    Not Attempted Level 1 yet...
                                </span>
                            </span>
                        </div>-->
                    @endif

                </div><!-- parent_progress_question End -->
                <!-- Slider -->

                <!-- dashboard_inner_box End -->
                <div class="icon-voted profession_attempted">
                    <h2>Icon Voted in L1</h2>
                    <div class="voted-list">
                        @if(isset($response['teenagerMyIcons']) && count($response['teenagerMyIcons']) > 0)
                        <ul class="row owl-carousel">
                           @foreach($response['teenagerMyIcons'] as $teenagerMyIcon)
                            <li class="col-sm-3 col-xs-6">
                                <figure>
                                    <div class="icon-img"><a href="javascript:void(0);" data-placement="bottom" title="{{ str_limit($teenagerMyIcon['iconDescription'], $limit = 100, $end = '...') }}" data-toggle="tooltip"><img src="{{$teenagerMyIcon['iconImage']}}"></a></div>
                                </figure>
                            </li>
                            @endforeach
                        </ul>
                        @else
                            No records found
                        @endif
                    </div>
                </div>

                <div class="dash_progress my_intrest col-md-12 teen_interest">
                    <div class="parent_h2_header">
                        <h2>Interests from <span class="l-2"><span class="level_label margintb0">L-2</span></span> <span data-toggle="tooltip" title="ProTeen Multiple Intelligence Synthesis Engine"  data-placement="bottom"> PROMISE</span></h2>
                    </div>
                </div><!-- dashboard_inner_box End -->

                <div class="dash_progress my_intrest col-md-12 teenager_skill">
                    <div class="parent_h2_header">
                        <h2>Multiple Intelligences from <span class="l-2"><span class="level_label margintb0">L-2</span></span> <span data-toggle="tooltip" title="ProTeen Multiple Intelligence Synthesis Engine"  data-placement="bottom"> PROMISE</span></h2>
                    </div>
                    
                    <!--<h2 style="text-align:center;"><span class="parent_assessment_header_text">Your "How well do you know your Teen ?" response </span>
                        <span class="high_label" style="font-size:18px;">H - High,</span>
                        <span class="mid_label" style="font-size:18px;"> M - Moderate,</span>
                        <span class="low_label" style="font-size:18px;">L - Low </span>
                    </h2>-->
                    <h2 style="text-align:center;" class="multiple-heading">Your "How well do you know your Teen ?" response </h2>
                    <div class="data-explainations clearfix text-center data-dashboard">
                        <div class="content">
                            <div class="data"><span class="small-box career-data-color-1"></span><span>high</span></div>
                            <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
                            <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
                        </div>
                    </div>
                    <div class="parent_h2_header">
                        <h2 class="parent_h2_text">Multiple Intelligences</h2>
                    </div>

                    
                    
                </div>
                <br/>

                <div class="col-md-12">
                    <div class="dashboard_inner_box dash_progress no_bord mtb15">
                    <h2>Professions Explored in <span class="l-3"><span class="level_label margintb0">L-3</span></span></h2> </div>
                    @if(isset($response['attempted_profession']) && !empty($response['attempted_profession']))
                    <div class="owl-carousel2 owl-carousel nav_dis">
                        @foreach($response['attempted_profession'] as $key => $value)
                            <div class="item" id="edubox_{{$value['professionId']}}" data-cstid="{{$key}}">
                                <div class="flip-container">
                                    <div class="slider_card flipper">
                                        <div class="front_page">
                                            <div class="{{$value['professionId']}} loading-screen loading-wrapper-sub" style="display:none;">
                                                <div class="loading-text">
                                                    <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
                                                </div>
                                                <div class="loading-content"></div>
                                            </div>
                                            <div id="badges_rank_data_{{$value['professionId']}}">

                                            </div>
                                            <div class="dashboard_edu_text">
                                                <a href="javascript:void(0);" onclick="getProfessionEducationPath({{$value['professionId']}})" class="btn primary_btn">Education Path</a>
                                            </div>
                                            @if ($value['remainingDays'] == 0)
                                            <div id="days_{{$value['professionId']}}">
                                                <div class="promisebtn">
                                                    <a href="javascript:void(0);" class="promise btn_golden_border" title="" onclick="getPromisePlus({{$value['professionId']}}, {{$teenDetail->id}}, {{$value['remainingDays']}}, {{$value['required_coins']}});" data-ref="#{{$value['professionId']}}">
                                                        <span class="promiseplus">PROMISE Plus</span>
                                                        <span class="coinouter">
                                                            <span class="coinsnum">{{$value['required_coins']}}</span>
                                                            <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}">
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            @else
                                                <div class="promisebtn timer_btn">
                                                    <a href="javascript:void(0);" class="promise btn_golden_border" title="" onclick="getPromisePlus({{$value['professionId']}}, {{$teenDetail->id}}, {{$value['remainingDays']}},{{$value['required_coins']}});" data-ref="#{{$value['professionId']}}">
                                                        <span class="promiseplus">PROMISE Plus</span>
                                                        <span class="coinouter">
                                                            <span class="coinsnum">{{$value['remainingDays']}} Days Left</span>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="back_page first_page">
                                            <!-- <div class="loader card_loader cst_{{$value['professionId']}} init_loader">
                                                <div class="cont_loader">
                                                    <div class="img1"></div>
                                                    <div class="img2"></div>
                                                </div>
                                            </div> -->
                                            <div id="" class="cst_{{$value['professionId']}} loading-screen loading-wrapper-sub intermediate-first-question-loader" style="display:none;">
                                                <div class="loading-text">
                                                    <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
                                                </div>
                                                <div class="loading-content"></div>
                                            </div>
                                            <div class="full_path">
                                                <div class="inner_path" id="education_path_{{$value['professionId']}}">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="back_page second_page">
                                            <div class="full_path">
                                                <div class="inner_path">
                                                    <!--<div class="h2_container"><h2>Education Path</h2></div>-->
                                                    <span class="title dashboard_profession">
                                                        <span class="profession_text" style="font-size: 14px;" title="Actors">
                                                            <span>{{$value['profession_name']}}</span>
                                                        </span>
                                                    </span>
                                                    <a href="javascript:void(0);" class="close_next" id="close_{{$value['professionId']}}"><i class="icon-close" aria-hidden="true"></i></a>
                                                    <div class="userData" id="{{$value['professionId']}}">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="loader card_loader {{$value['professionId']}}" style="display:none;" >
                                                <div class="cont_loader">
                                                    <div class="img1"></div>
                                                    <div class="img2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="no_data">
                        <span class="nodata_outer">
                            <span class="nodata_middle">No professions attempted</span>
                        </span>
                    </div>
                    @endif
                </div>
                <div class="loader ajax-loader" style="display:none;">
                    <div class="cont_loader">
                        <div class="img1"></div>
                        <div class="img2"></div>
                    </div>
                </div>
            </div>
           </div>
            </form>
            <!--Learning Style start-->
                <div class="dashboard_inner_box no_bord sec-guidance">
                    <h2>Learning Guidance</h2>
                    {!! (isset($learningGuidance->cms_body)) ? $learningGuidance->cms_body : 'Learning Guidance will be updated!' !!}
                     @if ($response['remainingDaysForLS'] == 0)
                        <!--<a href="javascript:void(0);" class="learning_header learning_style_button" title="">
                             <span class="coinouter btn_golden_border" id="Rdays">
                                <span class="coinsnum">{{$response['required_coins']}}</span>
                                <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}"></span>
                            </span>
                        </a>-->
                        <div class="promisebtn">
                                                    <a href="javascript:void(0);" class="promise btn_golden_border" title="" >
                                                        <span class="promiseplus">PROMISE Plus</span>
                                                        <span class="coinouter">
                                                            <span class="coinsnum">2500</span>
                                                            <span class="coinsimg"><img src="https://proteenlive-old.s3.ap-south-1.amazonaws.com/frontend/images/coin-stack.png">
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                    @else
                        <!--<a href="javascript:void(0);" class="learning_header learning_style_button" title="">
                            <span class="coinouter btn_golden_border">
                                <span class="coinsnum">{{$response['remainingDaysForLS']}} Days Left</span>
                            </span>
                        </a>-->
                    @endif
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10">
                            <div class="btn_typ_lable labels lg_legend mtb15" id="lg_legend" style="margin:0px;display: none;">
                                <span class="easy">Easy</span>
                                <span class="medium">Medium</span>
                                <span class="tough">Tough</span>
                                <span class="not_assessed">NA</span>
                            </div>
                            <div id="learn" class="learn_scroll" style="display: none;">
                                <div class="loader card_loader ajax-loader">
                                    <div class="cont_loader">
                                        <div class="img1"></div>
                                        <div class="img2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="time_line_container dashboard_inner_box no_bord">
                <h2>Activity Timeline</h2>
                <div class="timeline">
                    <div class="timeline_inner">
                        <table>
                            <?php
                            $timeLine = Helpers::getTeenagerTimeLine($teenId);
                            $classArray = array('alpha', 'beta', 'gamma', 'delta');
                            ?>
                            @if(isset($timeLine) && !empty($timeLine))
                            <?php $flag = 0; ?>
                            @foreach($timeLine as $line=>$date)

                            <tr class="{{$classArray[$flag]}}">
                                <td class="timeline_icon">
                                    <span class="box"></span>
                                </td>
                                <td class="timeline_date">{{date('d, F Y',strtotime($date))}}</td>
                                <td class="timeline_detail">{{$line}}</td>
                            </tr>
                            <?php
                            $flag++;
                            if ($flag > 3) {
                                $flag = 0;
                            }
                            ?>
                            @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- <div class="modal fade default_popup HML_popup" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close close_next" data-dismiss="modal">Next</button>
                <div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName"></h2></div>
                <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
                <div class="modal-body body_sticky">
                    <div class="basket_iframe_video"><iframe id="operationVideo" src="" frameborder="0" allowfullscreen=""></iframe></div>
                </div>
            </div>
        </div>
    </div>-->
<div id="confirm" title="Congratulations!" style="display:none;">
    <div class="confirm_coins"></div><br/>
    <div class="confirm_detail"></div>
</div>
<div id="confirm_box" title="Alert" style="display:none;">
    <div class="confirm_rate"></div>
</div>

    @stop
    @section('script')
    <script>
        jQuery(document).ready(function($) {
            <?php if ($response['teenAssessment'] == "no") { ?>
                $('#dashboard_modal').modal('show');
            <?php }?>
            $("a[id=report]").each(
                function(){
                    if($(this).attr("disabled"))
                        $(this).attr("disabled", false); //enable button again
                }
            );
            $(".table_container").mCustomScrollbar({axis: "xy"});
            $('.parent_progsinner, .flip_scroll').mCustomScrollbar();
            var user_icon_number = <?php echo count($response['teenagerMyIcons']);?>;
            if (user_icon_number > 4) {
                $('.parent_inclination').owlCarousel({
                    nav: true,
                    loop: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        750: {
                            items: 2
                        },
                        990: {
                            items: 5,
                            mouseDrag: false
                        }
                    }
                });
            } else {
                $('.parent_inclination').addClass("no_slider");
            }
            $('.parent_apptitude,.parent_intrest').owlCarousel({
                nav: true,
                loop: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    750: {
                        items: 2
                    },
                    990: {
                        items: 5,
                        mouseDrag: false
                    }
                }
            });
        });
        if ($('.voted-list ul').children().length > 4) {
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
                        480: {
                            items: 2
                        },
                        768: {
                            items: 4
                        },
                    }
                });
            }
        $('#teenName').on('change', function() {
            $("a[id=report]").each(
                function(){
                    if($(this).attr("disabled"))
                        $(this).attr("disabled", true);                                                                                                    }
            );
            var uniqueid = $(this).val();
            var progressURL = '<?php echo url('/parent/progress') ?>';
            window.location.href = "/parent/progress/" + uniqueid;
            //document.forms['displayTeen'].submit();
        });

        function openIntroVideo(video, title)
        {
            if (video == '') {
                alert("No video found");
                return false;
            }
            var url = 'https://www.youtube.com/embed/' + video + '?rel=0&amp;showinfo=0';
            $("#operationVideo").attr('src', url);
            $("#basketName").text(title);
            $('.HML_popup').modal('show');
        }

        $(window).bind("load", function() {
            /*load less more for intrest*/
            var setAttemptedProfessionIds = '<?php echo json_encode($response['setAttemptedProfessionIds']);?>';

            $('.owl-carousel2').owlCarousel({
                nav: true,
                loop: false,
                dots: true,
                center: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    750: {
                        items: 2
                    },
                    990: {
                        items: 3,
                        stagePadding: 1,
                        mouseDrag: false
                    }
                }
            });

        });
        $('.dashboard_edu_text a').click(function(event) {
            $(this).parents('.flip-container').addClass('flip_now');
            $(this).parents('.flip-container').find('.second_page').removeClass('active');
            $(this).parents('.flip-container').find('.first_page').addClass('active');
        });
        /*$('.promisebtn a').click(function(event) {
                $(this).parents('.flip-container').addClass('flip_now');
                $(this).parents('.flip-container').find('.first_page').removeClass('active');
                $(this).parents('.flip-container').find('.second_page').addClass('active');
        });*/

        function getPromisePlus(professionId, teenager_id, days, r_coins)
        {
            if (days > 0) {
                getPromisePlusData(professionId, teenager_id);
            } else {
                $.ajax({
                    url: "{{ url('/parent/get-available-coins-for-parent') }}",
                    type: 'POST',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "teenId": teenager_id,
                        "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>
                    },
                    success: function(response) {
                        coins = response;
                        if (coins >= r_coins) {
                            $(".confirm_coins").text('<?php echo 'You have '; ?>' + format(coins) + '<?php echo ' ProCoins available.'; ?>');
                            $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(r_coins) + '<?php echo ' ProCoins and play on'; ?>');
                            $.ui.dialog.prototype._focusTabbable = function(){};
                            $( "#confirm" ).dialog({

                            resizable: false,
                            height: "auto",
                            width: 400,
                            draggable: false,
                            modal: true,
                            buttons: [
                            	{
                            		text: "Ok",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  getPromisePlusData(professionId, teenager_id);
                            		  $( this ).dialog( "close" );
                            		}
                            	},
                            	{
                            		text: "Cancel",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  $( this ).dialog( "close" );
                            		  $(".confirm_coins").text(' ');
                            		}
                            	}
                              ],
                              open: function(event, ui) {
                                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                }
                            });
                        } else {
                            $("#confirm").attr('title', 'Notification!');
                            $(".confirm_coins").text("You don't have enough ProCoins. Please Buy more.");
                            $.ui.dialog.prototype._focusTabbable = function(){};
                            $( "#confirm" ).dialog({

                            resizable: false,
                            height: "auto",
                            width: 400,
                            draggable: false,
                            modal: true,
                            buttons: [
                            	{
                            		text: "Buy",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  var path = '<?php echo url('/parent/my-coins/'); ?>';
                                      location.href = path;
                            		  $( this ).dialog( "close" );
                            		}
                            	},
                            	{
                            		text: "Cancel",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  $( this ).dialog( "close" );
                            		  $(".confirm_coins").text(' ');
                            		}
                            	}
                              ],
                              open: function(event, ui) {
                                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                }
                            });
                        }
                    }
                });
            }
        }

        function getPromisePlusData(professionId, teenager_id) {
            $('#'+professionId).parents('.flip-container').addClass('flip_now');
            $('#'+professionId).parents('.flip-container').find('.first_page').removeClass('active');
            $('#'+professionId).parents('.flip-container').find('.second_page').addClass('active');
            $('.'+professionId).show();
            $('.'+professionId).show();
            $.ajax({
                url: "{{ url('/parent/get-promise-plus') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "profession": professionId,
                    "teenId": teenager_id
                },
                success: function(response) {
                   $('#'+professionId).html(response);
                   $('.'+professionId).hide();
                   $('.flip_scroll').mCustomScrollbar();
                }
            });
        }

        function getLearningStyle(teenager_id,r_coins)
        {
            var days = <?php echo $response['remainingDaysForLS']; ?>;
            if (days > 0) {
                getLearningStyleData(teenager_id);
            } else {
                $.ajax({
                    url: "{{ url('/parent/get-available-coins-for-parent') }}",
                    type: 'POST',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "teenId": teenager_id,
                        "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>
                    },
                    success: function(response) {
                        coins = response;
                        if (coins >= r_coins) {
                            $(".confirm_coins").text('<?php echo 'You have '; ?>' + format(coins) + '<?php echo ' ProCoins available.'; ?>');
                            $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(r_coins) + '<?php echo ' ProCoins and play on'; ?>');
                            $.ui.dialog.prototype._focusTabbable = function(){};
                            $( "#confirm" ).dialog({

                            resizable: false,
                            height: "auto",
                            width: 400,
                            draggable: false,
                            modal: true,
                            buttons: [
                            	{
                            		text: "Ok",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  getLearningStyleData(teenager_id);
                            		  $( this ).dialog( "close" );
                            		}
                            	},
                            	{
                            		text: "Cancel",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  $( this ).dialog( "close" );
                            		  $(".confirm_coins").text(' ');
                            		}
                            	}
                              ],
                              open: function(event, ui) {
                                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                }
                            });
                        } else {
                            $("#confirm").attr('title', 'Notification!');
                            $(".confirm_coins").text("You don't have enough ProCoins. Please Buy more.");
                            $.ui.dialog.prototype._focusTabbable = function(){};
                            $( "#confirm" ).dialog({

                            resizable: false,
                            height: "auto",
                            width: 400,
                            draggable: false,
                            modal: true,
                            buttons: [
                            	{
                            		text: "Buy",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  var path = '<?php echo url('/parent/my-coins/'); ?>';
                                      location.href = path;
                            		  $( this ).dialog( "close" );
                            		}
                            	},
                            	{
                            		text: "Cancel",
                            		class : 'btn primary_btn',
                            		click: function() {
                            		  $( this ).dialog( "close" );
                            		  $(".confirm_coins").text(' ');
                            		}
                            	}
                              ],
                              open: function(event, ui) {
                                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                }
                            });
                        }
                    }
                });
            }
        }

        function getLearningStyleData(teenager_id) {
            $('.learn_scroll').slideDown();
            $('#lg_legend').slideDown();
            $.ajax({
                  url: "{{ url('/parent/get-learning-style') }}",
                  type: 'POST',
                  data: {
                      "_token": '{{ csrf_token() }}',
                      "teenId": teenager_id
                  },
                  success: function(response) {
                    $('.learn_scroll .ajax-loader').hide();
                    $('#learn').html(response);
                    $('#lg_legend').show();
                    $(".learn_scroll").mCustomScrollbar();
                    $('.learning_style_button').addClass('activet');
                    var parentId = <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>;
                    getRemaningDays(parentId);
                  }
            });
        }

        function getRemaningDays(parentId) {
            $.ajax({
                url: "{{ url('/parent/get-remaining-days') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "profession": 0,
                    "parentId": parentId
                },
                success: function(response) {
                   $('#Rdays').html(response);
                }
            });
        }

        $('body').on('click','.learning_style_button',function(){
          if($(this).hasClass('activet')){
              $('#learn').slideToggle();
              $('#lg_legend').slideToggle();
          }else{
                var result = getLearningStyle({{$teenId}}, {{$response['required_coins']}});
                if (result) {
                    $(this).addClass('activet');
                }
          }
        });

        $('body').on('click','#show_hide_btn_15',function(){
             setTimeout(function(){
                $("#learn").mCustomScrollbar("scrollTo","bottom");
             },600);
        });

        function getProfessionEducationPath(professionId)
        {
            if($("div#education_path_"+professionId+" .title").hasClass('title'))
            {
                $(".flip_scroll").mCustomScrollbar();
            }
            else
            {
                $('.cst_'+professionId).parent().addClass('loading-screen-parent');
                $('.cst_'+professionId).show();
                //$('.cst_'+professionId).show();
                $.ajax({
                    url: "{{ url('/parent/get-profession-education-path') }}",
                    type: 'POST',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "professionId": professionId
                    },
                    success: function(response) {
                        $('.cst_'+professionId).hide();
                        $('.cst_'+professionId).parent().removeClass('loading-screen-parent');
                        //$('.cst_'+professionId).hide();
                        $('#education_path_'+professionId).html(response);
                        $(".flip_scroll").mCustomScrollbar();
                    }
                });
            }
        }

        $('body').on('click','.micro_detail_button',function(){
            var bac = "."+$(this).data("idaa");
            $(bac).slideToggle();
            if ($(this).text() == "More")
               $(this).text("Less")
            else
               $(this).text("More");
        });

        $('body').on('click','.micro_detail_more',function(){
            $(this).siblings('.micro_detail').slideToggle();
            if ($(this).text() == "Less")
               $(this).text("Less")
            else
               $(this).text("More");
        });

        $('.timeline').mCustomScrollbar();

        $(document).on('click', '#report', function (e) {
            var days = <?php echo $response['remainingDays']; ?>;
            $.ajax({
                url: "{{ url('/parent/get-available-coins') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>
                },
                success: function(response) {
                    coins = response;
                    $.ajax({
                        url: "{{ url('/parent/get-coins-for-parent') }}",
                        type: 'POST',
                        data: {
                            "_token": '{{ csrf_token() }}',
                            "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>
                        },
                        success: function(response) {
                            if (response > 1) {
                                if (days == 0) {
                                    $(".confirm_coins").text('<?php echo 'You have '; ?>' + format(response) + '<?php echo ' ProCoins available.'; ?>');
                                    $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(coins) + '<?php echo ' ProCoins and play on'; ?>');
                                    $.ui.dialog.prototype._focusTabbable = function(){};
                                    $( "#confirm" ).dialog({

                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    draggable: false,
                                    modal: true,
                                    buttons: [
                                    	{
                                    		text: "Ok",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                    		  getReport(days);
                                    		  $( this ).dialog( "close" );
                                    		}
                                    	},
                                    	{
                                    		text: "Cancel",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                    		  $( this ).dialog( "close" );
                                    		  $(".confirm_coins").text(' ');
                                    		}
                                    	}
                                      ],
                                      open: function(event, ui) {
                                            $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                        }
                                    });
                                } else {
                                    getReport(days);
                                }
                            } else {
                                if (days != 0) {
                                    getReport(days);    
                                } else {
                                    $("#confirm").attr('title', 'Notification!');
                                    $(".confirm_coins").text("You don't have enough ProCoins. Please Buy more.");
                                    $.ui.dialog.prototype._focusTabbable = function(){};
                                    $( "#confirm" ).dialog({

                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    draggable: false,
                                    modal: true,
                                    buttons: [
                                    	{
                                    		text: "Buy",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                    		  var path = '<?php echo url('/parent/my-coins/'); ?>';
                                              location.href = path;
                                    		  $( this ).dialog( "close" );
                                    		}
                                    	},
                                    	{
                                    		text: "Cancel",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                    		  $( this ).dialog( "close" );
                                    		  $(".confirm_coins").text(' ');
                                    		}
                                    	}
                                      ],
                                       open: function(event, ui) {
                                            $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                        }
                                    });
                                }

                            }
                        }
                    });
                }
            });
        });
        $(document).on('click','.icon-close', function(){
            $( "#confirm" ).dialog( "close" );
        });
        $(document).on('click','.confirm_box_close', function(){
            $( "#confirm_box" ).dialog( "close" );
        });
        function getReport(days) {
            $.ajax({
                  url: "{{ url('/parent/purchased-coins-to-view-report') }}",
                  type: 'POST',
                  data: {
                      "_token": '{{ csrf_token() }}',
                      "parentId": <?php echo Auth::guard('parent')->user()->id;?>
                  },
                  success: function(response) {
                        var path = '<?php echo url('/parent/export-pdf/'.$teenDetail->t_uniqueid); ?>';
                        var win = window.open(path, '_blank');
                        win.focus();
                        if (days == 0) {
                            getRemaningDaysForReport(<?php echo Auth::guard('parent')->user()->id;?>);
                        }
                        //location.href = path;
                  }
              });
        }

        function getRemaningDaysForReport(parent_id) {
            $.ajax({
                url: "{{ url('/parent/get-remaining-days-for-report') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "parentId": <?php echo Auth::guard('parent')->user()->id; ?>
                },
                success: function(response) {
                   $('#RdaysReport').html(response);
                   $('#RdaysReport').show();
                }
            });
        }

        var professionone = $('[data-cstid="0"]').attr('id');
        var professiontwo = $('[data-cstid="1"]').attr('id');
        var professionthree = $('[data-cstid="2"]').attr('id');
        var professionfour = $('[data-cstid="3"]').attr('id');
        var finalIdOne = (professionone != "" && typeof professionone !== 'undefined')?professionone.split('_'):"";
        var finalIdTwo = (professiontwo != "" && typeof professiontwo !== 'undefined')?professiontwo.split('_'):"";
        var finalIdThree = (professionthree != "" && typeof professionthree !== 'undefined')?professionthree.split('_'):"";
        var finalIdFour = (professionfour != "" && typeof professionfour !== 'undefined')?professionfour.split('_'):"";

        (finalIdOne != "")?getBadgesAndRank(finalIdOne[1]):"";
        (finalIdTwo != "")?getBadgesAndRank(finalIdTwo[1]):"";
        (finalIdThree != "")?getBadgesAndRank(finalIdThree[1]):"";
        (finalIdFour != "")?getBadgesAndRank(finalIdFour[1]):"";

        function getBadgesAndRank(professionId)
        {
            $('.'+professionId).show();
            var teenagerId = "{{$teenDetail->id}}";

            $.ajax({
                url: "{{ url('/parent/get-profession-badges-and-rank') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "professionId": professionId,
                    "teenagerId" : teenagerId
                },
                success: function(response) {
                  $('.'+professionId).hide();
                  $('.front_page #badges_rank_data_'+professionId).html(response);
                }
            });
        }

        $( document ).ready(function() {
            var lastitemId = "<?php echo key( array_slice( $response['setAttemptedProfessionIds'], -1, 1, TRUE ) );?>";
            var default_pointer = 4;
            var default_key = 4;
            var setAttemptedProfessionIdsArray = '<?php echo json_encode($response['setAttemptedProfessionIds']);?>';
            var professionId = JSON.parse(setAttemptedProfessionIdsArray);

            $('body').on('click','.owl-carousel2 .owl-next',function(){
                var activeClassCheck = parseInt($(".owl-carousel2 .owl-stage .owl-item.active .item").attr('data-cstid')) + 2;
                var setDifferent = parseInt(default_pointer) - activeClassCheck ;
                if(lastitemId >= default_pointer && setDifferent === 1 && activeClassCheck >= 3){
                    $(this).addClass('temp_disable');
                    var teenagerId = "{{$teenDetail->id}}";
                    var d_key = default_key - 1;
                    $('.'+professionId[default_key]).show();
                    //$('.'+professionId[d_key]).show();
                    $('.'+professionId[d_key]).parent().addClass('loading-screen-parent');
                    $('.'+professionId[d_key]).show();
                    $.ajax({
                        url: "{{ url('/parent/get-profession-badges-and-rank-on-click') }}",
                        type: 'POST',
                        data: {
                            "_token": '{{ csrf_token() }}',
                            "professionId": professionId[default_key],
                            "teenagerId" : teenagerId
                        },
                        success: function(response) {
                            if(response !== "Error"){
                                $('.owl-carousel2')
                                .trigger('add.owl.carousel', ['<div class="item" id="edubox_'+professionId[default_key]+'" data-cstid="'+default_pointer+'"><div class="flip-container"><div class="slider_card flipper"></div></div></div>'])
                                .trigger('refresh.owl.carousel');
                               // $('.'+professionId[d_key]).hide();
                                $('.'+professionId[d_key]).hide();
                                $('.'+professionId[d_key]).parent().removeClass('loading-screen-parent');
                                $(".owl-item #edubox_"+professionId[default_key]+" .flip-container .slider_card").html(response);
                                $('.'+professionId[default_key]).hide();
                                $(".owl-next.temp_disable").removeClass('temp_disable');
                                default_key++;
                                default_pointer++;
                            }
                        }
                    });
                }else{
                    $('.owl-carousel2').removeClass('nav_dis');
                  //$(this).hide();
                }
            });
        });

        $('body').on('click','.dashboard_edu_text a',function(event) {
            $(this).parents('.flip-container').addClass('flip_now');
            $(this).parents('.flip-container').find('.second_page').removeClass('active');
            $(this).parents('.flip-container').find('.first_page').addClass('active');
        });

        $('body').on('click','.close_next', function(event) {
            $(this).parents('.flip-container').removeClass('flip_now');
        });

        function format(x) {
            return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        jQuery(document).ready(function($) {
            $(".assessment_btn_click").click(function(event) {
                $('.section').slideUp();
                $($(this).data("divrefrence")).slideDown();
            });
        });

    </script>

<script>

    function openIntroVideo(video, title)
    {
        if (video == ''){
            $("#basketName").text(title);
            $('.body_sticky_not').show();
            $("#operationVideoNot").text("No video found");
            $('.body_sticky').hide();
            $('.HML_popup').modal('show');
            return false;
        }
        var url = 'https://www.youtube.com/embed/' + video + '?rel=0&amp;showinfo=0';
        $("#operationVideo").attr('src', url);
        $("#operationVideoNot").text('');
        $('.body_sticky').show();
        $('.body_sticky_not').hide();
        $("#basketName").text(title);
        $('.HML_popup').modal('show');
    }

    $('#dashboard_modal').on('shown.bs.modal', function() {
            $("body").addClass('pop_opend');
    });
    $('#dashboard_modal').on('hidden.bs.modal', function () {
        $("body").removeClass('pop_opend');
    });

    $('#promise_popup').click(function(){
        $('#dashboard_modal').modal('show');
    });
    function savePromiseRate(key,section)
    {

        var form_data = '';
        if (key == 1) {
            var icon = $('input.square_radio1:checked').length;
            form_data = $('#assesmentDataM').serialize();
        } else if (key == 2) {
            var icon = $('input.square_radio2:checked').length;
            form_data = $('#assesmentDataP').serialize();
        } else if (key == 3) {
            var icon = $('input.square_radio3:checked').length;
            form_data = $('#assesmentDataA').serialize();
        }

        if (icon != 8 ) {
          window.scrollTo(0,0);
          $("#errorGoneMsg").html('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, give your VOTE to all aspects</span></div></div></div>');
              return false;
        }
        $("#errorGoneMsg").html('');
        $("#page_loader").show();
        $.ajax({
          url: "{{ url('/parent/save-teen-promise-rate') }}",
          type: 'POST',
          dataType: "json",
          data: {
              "_token": '{{ csrf_token() }}',
              "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>,
              "teenId": <?php echo $teenDetail->id; ?>,
              "form_data" : form_data,
              "key": key,
          },
          success: function(response) {
            if (section != '') {
                $("#page_loader").hide();
                $('.section').slideUp();
                $('#'+section).slideDown();
            } else {
                $.ajax({
                    url: "{{ url('/parent/get-teen-promise-rate-count') }}",
                    type: 'POST',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>,
                        "teenId": <?php echo $teenDetail->id; ?>,
                    },
                    success: function(rdata) {
                        $("#page_loader").hide();
                        if (rdata == 0) {
                            $('.close_modal').modal('hide');
                            $("#errorMsg").html('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-success success"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Your teen assessment has been saved successfully</span></div></div></div>');
                            location.reload(true);
                        }
                    }
                });
            }
          }
      });
    }

   $('body').on('click','.model_close',function(){
        $("#page_loader").show();
        $.ajax({
          url: "{{ url('/parent/get-teen-promise-rate-count') }}",
          type: 'POST',
          data: {
              "_token": '{{ csrf_token() }}',
              "parentId": <?php if (Auth::guard('parent')->check()) { echo Auth::guard('parent')->user()->id; } else { echo 0;}?>,
              "teenId": <?php echo $teenDetail->id; ?>,
          },
          success: function(response) {
            $("#page_loader").hide();
            if (response == 1) {
                $(".confirm_rate").text('Please attempt your teen PROMISE assessment.');
                $.ui.dialog.prototype._focusTabbable = function(){};
                $( "#confirm_box" ).dialog({
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close confirm_box_close"></i>' );
                },

                resizable: false,
                height: "auto",
                width: 400,
                draggable: false,
                modal: true,
                buttons: [
                	{
                		text: "Ok",
                		class : 'btn primary_btn',
                		click: function() {
                		  $( this ).dialog( "close" );
                          $('.close_modal').modal('show');
                		}
                	},
                	{
                		text: "Cancel",
                		class : 'btn primary_btn',
                		click: function() {
                		  $(".confirm_rate").text(' ');
                          var path = '<?php echo url('/parent/home/'); ?>';
                          location.href = path;
                		  $( this ).dialog( "close" );
                		}
                	}
                  ]
                });
            }
            $('.close_modal').modal('close');
          }
      });
   });
   $(window).on("load", function(e) {
        e.preventDefault();
        getTeenagerInterestData("{{$teenDetail->id}}");
        getTeenagerStrengthData("{{$teenDetail->id}}");
   });

   function getTeenagerInterestData(teenagerId) {
        $('.teen_interest .loading-screen-data').parent().addClass('loading-screen-parent');
        $('.teen_interest .loading-screen-data').show();
        $.ajax({
            type: 'POST',
            url: "{{url('parent/get-interest-detail')}}",
            dataType: 'html',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {'teenagerId':teenagerId},
            success: function (response) {
                try {
                    var valueOf = $.parseJSON(response); 
                } catch (e) {
                    $('.dashboard-interest-error-message').text(e);
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('.dashboard-interest-error-message').text(valueOf.message);
                } else {
                    $(".teen_interest").append(response).fadeIn('slow');
                }
                $('.teen_interest .loading-screen-data').hide();
                $('.teen_interest').removeClass('loading-screen-parent');
                /*load less more for skill*/
                // $('.content_prime').animate({height: 'auto'}, 1);
                // var newHeight = $('.content_prime').height();
                // $('.content_prime').animate({height: '230px'}, 1);
                // $('.intrest_load').click(function(event) {
                //     if ($(this).find('span').text() == "Show More")
                //         $(this).find('span').text("Show less")
                //     else
                //         $(this).find('span').text("Show More");
                //     $(this).toggleClass('rotation');
                //     $(this).siblings('.intrest_content').toggleClass('close_load');
                //     if ($(this).siblings('.intrest_content').hasClass('close_load')) {
                //         $(this).siblings('.intrest_content').animate({height: '220px'}, 1000);
                //     }
                //     else {
                //         $(this).siblings('.intrest_content').animate({
                //             height: newHeight,
                //         }, 1000);
                //     }
                // });

                /*load less more for skill*/
                $('.content_prime').animate({height: 'auto'}, 1);
                var newHeight = $('.content_prime').height();
                $('.content_prime').animate({height: '230px'}, 1);
                $('.intrest_load').click(function(event) {
                    if ($(this).find('span').text() == "Show More")
                        $(this).find('span').text("Show less")
                    else
                        $(this).find('span').text("Show More");
                    $(this).toggleClass('rotation');
                    $(this).siblings('.intrest_content').toggleClass('close_load');
                    if ($(this).siblings('.intrest_content').hasClass('close_load')) {
                        $(this).siblings('.intrest_content').animate({height: '220px'}, 1000);
                    }
                    else {
                        $(this).siblings('.intrest_content').animate({
                            height: newHeight,
                        }, 1000);
                    }
                });
            }
        });
    }

    function getTeenagerStrengthData(teenagerId) {
        $('.teenager_skill .loading-screen-data').parent().toggleClass('loading-screen-parent');
        $('.teenager_skill .loading-screen-data').show();
        $.ajax({
            type: 'POST',
            url: "{{url('parent/get-strength-detail')}}",
            dataType: 'html',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {'teenagerId':teenagerId},
            success: function (response) {
                try {
                    var valueOf = $.parseJSON(response);
                } catch (e) {
                    // not json
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('.dashboard-strength-error-message').text(valueOf.message);
                } else {
                    $(".teenager_skill").append(response).fadeIn('slow');
                }
                $('.teenager_skill .loading-screen-data').hide();
                $('.teenager_skill').removeClass('loading-screen-parent');
                /*load less more for skill*/
                $('.content_secondary').animate({height: 'auto'}, 1);
                var newHeight_secondary = $('.content_secondary').height();
                $('.content_secondary').animate({height: '230px'}, 1);
                $('.skill_load').click(function(event) {
                    if ($(this).find('span').text() == "Show More")
                        $(this).find('span').text("Show less")
                    else
                        $(this).find('span').text("Show More");
                    $(this).toggleClass('rotation');
                    $(this).siblings('.intrest_content').toggleClass('close_load');
                    if ($(this).siblings('.intrest_content').hasClass('close_load')) {
                        $(this).siblings('.intrest_content').animate({height: '220px'}, 1000);
                    }
                    else {
                        $(this).siblings('.intrest_content').animate({
                            height: newHeight_secondary,
                        }, 1000);
                    }
                });
            }
        });
    }
</script>
@stop