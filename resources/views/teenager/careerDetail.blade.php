@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')
    <div class="bg-offwhite">
    <!-- mid section starts-->
    <!-- mid section-->
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
        <section class="career-detail">
            <h1>{{$professionsData->pf_name}}</h1>
            <div class="banner-landing banner-detail" style="background-image:url({{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}})">
                <div>
                    <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video"><img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon"></a></div>
                </div>
                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{Helpers::youtube_id_from_url($professionsData->pf_video)}}" frameborder="0" allowfullscreen id="iframe-video"></iframe>
            </div>
            <div class="detail-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="career-stat">
                            <div class="row">
                                <div class="col-sm-6">
                                    <ul class="color-1">
                                        <li class="icon"><?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?></li>
                                        <?php
                                            $salary_range = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'salary_range';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($salary_range->pfic_content) && !empty($salary_range->pfic_content)) ? $salary_range->pfic_content : '' ?></h4>
                                            <p>average per year</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-2">
                                        <li class="icon"><i class="icon-clock"></i></li>
                                        <?php
                                            $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'work_hours_per_week';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?></h4>
                                            <p>hours per week</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-3">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_current';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?></h4>
                                            <p>US employment 2017</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-4">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_projected';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?></h4>
                                            <p>projected for 2027</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <div class="heading">
                                <h4>{{$professionsData->pf_name}}</h4>
                                <div class="list-icon"><span><a id="add-to-star" href="javascript:void(0)" title="Like" class="<?php echo (count($professionsData->starRatedProfession)>0) ? "favourite-career" : '' ?>"><i class="icon-star"></i></a></span><span><a href="#" title="print"><i class="icon-print"></i></a></span></div>
                            </div>
                            <p>{{$professionsData->pf_intro}}</p>
                        </div>
                        <div class="career-detail-tab bg-white">
                            <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Career Details</span></span></a></li>
                                <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Explore <span class="tab-complete">21% Complete</span></span></span></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="menu1" class="tab-pane fade in active">
                                    <?php
                                        $profession_outlook = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_outlook';
                                        })->first();
                                    ?>
                                    @if(isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content))
                                        <div class="block">
                                            <h4> Outlook</h4>
                                            <p>{!!$profession_outlook->pfic_content!!}</p>
                                        </div>
                                    @endif

                                    <?php
                                        $profession_education_path = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_education_path';
                                        })->first();
                                    ?>
                                    @if(isset($profession_education_path->pfic_content) && !empty($profession_education_path->pfic_content))
                                        <div class="block">
                                            <h4>Education</h4>
                                            <p>{!!$profession_education_path->pfic_content!!}</p>
                                            <div id="education_chart">Chart Loads here...</div>  
                                        </div>
                                    @endif


                                    <?php
                                        $profession_experience_and_growth_path = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_experience_and_growth_path';
                                        })->first();
                                    ?>
                                    @if(isset($profession_experience_and_growth_path->pfic_content) && !empty($profession_experience_and_growth_path->pfic_content))
                                        <div class="block">
                                            <h4>Experience</h4>
                                            {!!strip_tags($profession_experience_and_growth_path->pfic_content)!!}
                                        </div>
                                    @endif

                                    <?php
                                        $profession_certifications = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_certifications';
                                        })->first();
                                    ?>
                                    @if(isset($profession_certifications->pfic_content) && !empty($profession_certifications->pfic_content))
                                    <div class="block">
                                        <h4>Certifications</h4>
                                        <p>{!!$profession_certifications->pfic_content!!}</p>
                                        <div class="img-list">
                                            <ul>
                                                @forelse($professionsData->professionCertificates as $professionCertificate)
                                                <li><img src="{{ Storage::url($professionCertificationImagePath.$professionCertificate->certificate['pc_image']) }}" alt="compatia logo"></li>
                                                @empty
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                    <?php
                                        $profession_licensing = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_licensing';
                                        })->first();
                                    ?>
                                    @if(isset($profession_licensing->pfic_content) && !empty($profession_licensing->pfic_content))
                                    <div class="block">
                                        <h4>Licensing</h4>
                                        <p>{!!$profession_licensing->pfic_content!!}</p>
                                    </div>
                                    @endif

                                    <?php
                                        $profession_bridge = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_bridge';
                                        })->first();
                                    ?>
                                    @if(isset($profession_bridge->pfic_content) && !empty($profession_bridge->pfic_content))
                                    <div class="block">
                                        <h4>Apprenticeships</h4>
                                        <p>{!!$profession_bridge->pfic_content!!}</p>
                                    </div>
                                    @endif

                                    <?php
                                        $profession_job_activities = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_job_activities';
                                        })->first();
                                    ?>
                                    @if(isset($profession_job_activities->pfic_content) && !empty($profession_job_activities->pfic_content))
                                    <div class="block">
                                        <h4>Activities</h4>
                                        {!!$profession_job_activities->pfic_content!!}
                                    </div>
                                    @endif

                                    <?php
                                        $profession_subject_knowledge = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_subject_knowledge';
                                        })->first();
                                    ?>
                                    @if(isset($profession_subject_knowledge->pfic_content) && !empty($profession_subject_knowledge->pfic_content))
                                    <div class="block">
                                        <h4>Subjects</h4>
                                        <p>{!!$profession_subject_knowledge->pfic_content!!}</p>
                                        <div class="img-list">
                                            <ul>
                                                @forelse($professionsData->professionSubject as $professionSubject)
                                                <li><img src="{{ Storage::url($professionSubjectImagePath.$professionSubject->subject['ps_image']) }}" alt="compatia logo"></li>
                                                @empty
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <?php
                                        $profession_ability = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_ability';
                                        })->first();
                                    ?>
                                    @if(isset($profession_ability->pfic_content) && !empty($profession_ability->pfic_content))
                                    <div class="block">
                                        <h4>Abilities</h4>
                                        {!!$profession_ability->pfic_content!!}
                                    </div>
                                    @endif

                                    <?php
                                        $profession_subject_knowledge = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_subject_knowledge';
                                        })->first();
                                    ?>
                                    @if(isset($profession_subject_knowledge->pfic_content) && !empty($profession_subject_knowledge->pfic_content))
                                    <div class="block">
                                        <h4>Knowledge</h4>
                                        {!!$profession_subject_knowledge->pfic_content!!}
                                    </div>
                                    @endif

                                    <?php
                                        $profession_skills = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_skills';
                                        })->first();
                                    ?>
                                    @if(isset($profession_skills->pfic_content) && !empty($profession_skills->pfic_content))
                                    <div class="block">
                                        <h4>Skills</h4>
                                        {!!$profession_skills->pfic_content!!}
                                    </div>
                                    @endif
                                </div>
                                <div id="menu2" class="tab-pane fade in ">
                                    <div class="explore-table table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Competitors</th>
                                                    <th>Score</th>
                                                    <th>Rank</th>
                                                    <th>Points</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>106</td>
                                                    <td>60</td>
                                                    <td>4</td>
                                                    <td>60/1600</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="promise-plus-outer">
                                        <div class="promise-plus front_page">
                                            <div class="heading">
                                                <span><i class="icon-plus"></i></span>
                                                <h3>Promise Plus</h3>
                                            </div>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate.</p>
                                            <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                            <div class="modal fade" id="myModal" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content custom-modal">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                            <h4 class="modal-title">Congratulations!</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>You have 42,000 ProCoins available.</p>
                                                            <p>Click OK to consume your 250 ProCoins and play on</p>
                                                        </div>
                                                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-next" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="promise-plus-overlay">
                                            <div class="promise-plus">
                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                <div class="heading">
                                                    <span class="emojis-img"><img class="emojis-icon-2" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAAB1AQMAAAC7wWdyAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGwZAEAAfFAAFzojyWAAAAAElFTkSuQmCC"></span>
                                                    <h3>Promise Plus</h3>
                                                </div>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate.</p>
                                                <button class="btn btn-primary" title="Submit">Sumbit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="virtual-plus text-center">
                                        <h4><span>Virtual Role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                    </div>
                                    <div class="quiz-sec ">
                                        <div class="row flex-container">
                                            <div class="col-sm-12">
                                                <div class="quiz-box quiz-basic">
                                                    <div class="sec-show">
                                                        <h3>Quiz</h3>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                                        <span title="Play" class="btn-play btn btn-basic">Play</span>
                                                    </div>
                                                    <div class="basic-quiz-area sec-hide">
                                                        <div class="quiz_view">
                                                            <div class="clearfix time_noti_view"><span class="time_type pull-left"><i class="icon-alarm"></i><span class="time-tag">0:0</span></span><span class="help_noti pull-right"><span class="pull-right close"><i class="icon-close"></i></span></span></div>
                                                            <div class="quiz-que">
                                                                <p class="que"><i class="icon-arrow-simple"></i>Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor?</p>
                                                                <div class="quiz-ans">
                                                                    <div class="radio"><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label></div>
                                                                    <div class="clearfix"><a href="#" class="next-que pull-right"><i class="icon-hand"></i></a></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row flex-container">
                                            <div class="quiz-intermediate">
                                                <div class="sec-show clearfix">
                                                    <div class="col-sm-6 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                            <h6>Lorem Ipsum</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal1"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                            <div class="modal fade" id="myModal1" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content custom-modal">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                            <h4 class="modal-title">Congratulations!</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>You have 42,000 ProCoins available.</p>
                                                                            <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                            <h6>Lorem Ipsum</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal2"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                            <div class="modal fade" id="myModal2" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content custom-modal">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                            <h4 class="modal-title">Congratulations!</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>You have 42,000 ProCoins available.</p>
                                                                            <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                            <h6>Lorem Ipsum</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal3"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                            <div class="modal fade" id="myModal3" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content custom-modal">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                            <h4 class="modal-title">Congratulations!</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>You have 42,000 ProCoins available.</p>
                                                                            <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                            <h6>Lorem Ipsum</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal4"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                            <div class="modal fade" id="myModal4" role="dialog">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content custom-modal">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                            <h4 class="modal-title">Congratulations!</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>You have 42,000 ProCoins available.</p>
                                                                            <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="quiz-area sec-hide">
                                                    <div class="quiz_view">
                                                        <div class="clearfix time_noti_view"><span class="time_type pull-left"><i class="icon-alarm"></i><span class="time-tag">0:0</span></span><span class="help_noti pull-right"><span class="pull-right close"><i class="icon-close"></i></span></span></div>
                                                        <div class="quiz-que">
                                                            <p class="que"><i class="icon-arrow-simple"></i>Identify the correct terminology for the piece of furniture:</p>
                                                            <div class="quiz-ans">
                                                                <div class="question-img">
                                                                    <img src="{{ Storage::url('img/question-img.jpg') }}" title="Click to enlarge image" class="pop-me">
                                                                </div>
                                                                <div class="radio"><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label></div>
                                                                <div class="clearfix"><a href="#" class="next-que pull-right"><i class="icon-hand"></i></a></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="virtual-plus text-center real-world">
                                        <h4><span>Real-world role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                    </div>
                                    <div class="quiz-advanced quiz-sec">
                                        <div class="sec-upload clearfix sec-show">
                                            <div class="row flex-container">
                                                <div class="col-sm-4 flex-items">
                                                    <div class="quiz-box">
                                                        <div class="img"><i class="icon-image"></i></div>
                                                        <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                        <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 flex-items">
                                                    <div class="quiz-box">
                                                        <div class="img"><i class="icon-video"></i></div>
                                                        <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                        <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 flex-items">
                                                    <div class="quiz-box">
                                                        <div class="img"><i class="icon-document"></i></div>
                                                        <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                        <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="upload-screen quiz-box sec-hide">
                                            <h4>Purchasing Agents & Buyers (Farm Products)</h4>
                                            <span class="pull-right close"><i class="icon-close"></i></span>
                                            <div class="upload-img" id="img-preview">
                                                <span>photo upload</span>
                                                <input type="file" name="pic" accept="image/*" onchange="readURL(this);">
                                            </div>
                                            <button class="btn-primary" type="submit" title="Submit">Submit</button>
                                            <div class="upload-content">
                                                <div class="no-data">
                                                    <div class="nodata-middle">
                                                        No Image found
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="virtual-plus text-center competitive-role">
                                        <h4><span>competitive role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                        <div class="competitive-list quiz-sec">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="quiz-box">
                                                        <div class="img">
                                                            <img src="{{ Storage::url('img/abl-logo.png') }}" alt="abl logo">
                                                        </div>
                                                        <h6>Company Name</h6>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                        <ul class="btn-list">
                                                            <li><a href="#" title="learn more" class="btn">learn more</a></li>
                                                            <li><a href="#" title="Apply" class="btn btn-apply">Apply</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="quiz-box">
                                                        <div class="img">
                                                            <img src="{{ Storage::url('img/ryantec-logo.png') }}" alt="ryantec logo">
                                                        </div>
                                                        <h6>Company Name</h6>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                        <ul class="btn-list">
                                                            <li><a href="#" title="learn more" class="btn">learn more</a></li>
                                                            <li><a href="#" title="Apply" class="btn btn-apply">Apply</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="overlay">
                                                <div class="overlay-inner">
                                                    <div class="icon"><!--<i class="icon-lock"></i>-->
                                                    <img src="{{ Storage::url('img/img-lock.png') }}" alt="lock image"></div>
                                                    <p>Complete previous section<br> to unlock</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="virtual-plus text-center challenge-play">
                                        <h4><span>challenge Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                        <div class="form-challenge">
                                            <form>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group custom-select">
                                                            <select class="form-control">
                                                                <option value="Select a parent or mentor">Select a parent or mentor</option>
                                                                <option value="Parent">Parent</option>
                                                                <option value="Mentor 1">Mentor 1</option>
                                                                <option value="Mentor 2">Mentor 2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button class="btn btn-submit" type="submit" title="a=Add">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="connect-block sec-progress color-swap">
                            <h2>Connect</h2>
                            <div class="bg-white">
                                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Leaderboard</span></span></a></li>
                                    <li class="custom-tab col-xs-6 tab-color-3"><a data-toggle="tab" href="#menu4"><span class="dt"><span class="dtc">Fans of this career</span></span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="menu3" class="tab-pane fade in active">
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
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
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
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-center"><a href="#" title="load more" class="load-more">load more</a></p>
                                    </div>
                                    <div id="menu4" class="tab-pane fade in">
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
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
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
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-center"><a href="#" title="load more" class="load-more">load more</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ad-sec-h">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 850 x 90
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sec-match">
                            <!--<div id="match" class="progress-cl">
                            </div>
                            <h3>Match</h3>-->
                            <div class="progress-match">
                              <div class="barOverflow">
                                <div class="bar"></div>
                              </div>
                              <span>80%</span>
                            </div>
                            <h3>Match</h3>
                        </div>
                        <div class="advanced-sec">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="">Advanced View</a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion1">
                                        <div class="panel-body">
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 1</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="90">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 2</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 3</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 4</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="95">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 5</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="30">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 6</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="45">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 7</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="60">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 8</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="55">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 9</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 10</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 11</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 12</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="65">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 13</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 14</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="90">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 15</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 16</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 17</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="95">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 18</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="30">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 19</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="45">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 20</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="60">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 21</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="55">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 22</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 23</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress-block">
                                                <div class="skill-name">MI parameter 24</div>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="unbox-btn"><a href="javascript:void(0);" title="Unbox Me" class="btn-primary"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                            </div>
                        </div>
                        <div class="sec-tags">
                            <h4>Tags</h4>
                            <div class="sec-popup">
                                <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom">
                                    <i class="icon-question"></i>
                                </a>
                                <div class="hide" id="pop1">
                                    <div class="popover-data">
                                        <a class="close popover-closer"><i class="icon-close"></i></a>
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                    </div>
                                </div>
                            </div>
                            <ul class="tag-list">
                                @forelse($professionsData->professionTags as $professionTags)
                                    <li><a href="{{ url('/teenager/career-tag/'.$professionTags->tag['id']) }}" title="Lorem ipsum">{{$professionTags->tag['pt_name']}}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        <div class="ad-v">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 343 x 400
                                </div>
                            </div>
                        </div>
                        <div class="ad-v-2">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 343 x 800
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
        <!-- mid section end-->
    </div>
@stop
@section('script')
<script src="{{ asset('backend/js/highchart.js')}}"></script>

<script>
    $(document).ready(function() {
        $('.play-icon').click(function() {
            $(this).hide();
            $('iframe').show();
        })
        $('.btn-next').click(function() {
            $('.front_page').hide();
            $('.promise-plus-overlay').show(500);
        })
        $('.promise-plus-overlay .close').click(function() {
            $('.promise-plus-overlay').hide();
            $('.front_page').show(500);
        })
        $('.btn-basic').click(function() {
            $('.quiz-basic .sec-show').addClass('hide');
            $('.quiz-basic .basic-quiz-area').addClass('active');
        })
        $('.quiz-box .close').click(function() {
            $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-intermediate').click(function(){
            $('.quiz-intermediate .sec-show').addClass('hide');
            $('.quiz-intermediate .sec-hide').addClass('active');
        })
        $('.quiz-area .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-advanced').click(function(){
            $('.quiz-advanced .sec-show').addClass('hide');
            $('.quiz-advanced .sec-hide').addClass('active');
        })
        $('.upload-screen .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });

        $(".progress-match").each(function(){

          var $bar = $(this).find(".bar");
          var $val = $(this).find("span");
          var perc = parseInt( $val.text(), 10);

          $({p:0}).animate({p:perc}, {
            duration: 3000,
            easing: "swing",
            step: function(p) {
              $bar.css({
                transform: "rotate("+ (45+(p*1.8)) +"deg)", // 100%=180° so: ° = % * 1.8
                // 45 is to add the needed rotation to have the green borders at the bottom
              });
              $val.text(p|0);
            }
          });
        });
    });
    // timer
    jQuery(document).ready(function($) {
        var count = 1;
        var counter = setInterval(timer, 1000);
        function secondPassed() {
            var minutes = Math.round((count - 30) / 60);
            var remainingcount = count % 60;
            if (remainingcount < 10) {
                remainingcount = "0" + remainingcount;
            }
            $('.time-tag,.time-tag').text(minutes + ":" + remainingcount);
            $('.time-tag').show();
        }
        function timer() {
            if (count < 0) {
            }
            else {
                secondPassed();
            }
            count = count + 1;
            if (count == 60)
            {
                //saveBoosterPoints(teenagerId, professionId, 2,isyoutube);
            }
        }
    });

    $(document).on('click','#add-to-star',function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'careerId=' + '{{$professionsData->id}}';
        $.ajax({
            url : '{{ url("teenager/add-star-to-career") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            dataType: "json",
            success : function (response) {
                if (response != '') {
                    $('#add-to-star').addClass('favourite-career');
                }
            }
        });
    });
    
    <?php
        $high_school_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'high_school_req';
        })->first();
        $junior_college_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'junior_college_req';
        })->first();
        $bachelor_degree_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'bachelor_degree_req';
        })->first();
        $masters_degree_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'masters_degree_req';
        })->first();

        if(isset($high_school_req->pfic_content) && !empty($high_school_req->pfic_content) && isset($junior_college_req->pfic_content) && !empty($junior_college_req->pfic_content) && isset($bachelor_degree_req->pfic_content) && !empty($bachelor_degree_req->pfic_content) && isset($masters_degree_req->pfic_content) && !empty($masters_degree_req->pfic_content)){

            $high_school = strip_tags($high_school_req->pfic_content);
            $junior_college = strip_tags($junior_college_req->pfic_content);
            $bachelor_degree = strip_tags($bachelor_degree_req->pfic_content);
            $masters_degree = strip_tags($masters_degree_req->pfic_content);

            $chartArray[] = array('y'=> (int) strip_tags($high_school), 'name' => 'High School', 'color' => '#ff5f44');
            $chartArray[] = array('y'=> (int) strip_tags($junior_college), 'name' => 'Bachelors Degree', 'color' => '#65c6e6');
            $chartArray[] = array('y'=> (int) strip_tags($bachelor_degree), 'name' => 'Masters Degree', 'color' => '#73376d');
            $chartArray[] = array('y'=> (int) strip_tags($masters_degree), 'name' => 'PhD', 'color' => '#27a6b5');
        }
    ?>

    var educationChartData = <?php echo json_encode($chartArray);  ?>;
    console.log(educationChartData);
    loadChart('column','',educationChartData,'education_chart');

    function loadChart(chartType,total,chartData,loadDiv){
        $('#'+loadDiv).highcharts({
            chart: {
                type: chartType,
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category',
            },
            legend: {
                enabled:false
            },
            yAxis: {                
                title: {
                    text: ''
                },                
                lineWidth: 0                
            },
                        
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: false,
                        format: ''
                    }
                }
            },
            tooltip: {
                pointFormat: ''
            },
            series: [{
                    colorByPoint: true,
                    data: chartData
                }]
           
        });
    }

</script>
@stop