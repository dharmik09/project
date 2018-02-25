<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico?v=2">
    <title>Career Detail</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .custom-tab-container{border-bottom: none;}
        .nav{margin-bottom: 0;}
        .nav:before, .nav:after{display: table;content: " ";}
        .custom-tab-container .custom-tab.active{z-index: 99;}
        .custom-tab-container .custom-tab{position: relative;}
        .nav-tabs > li{margin-bottom: -1px;}
        .nav > li{display: block;}
        .nav-tabs > li > a, .nav-tabs > li > a:focus, .nav-tabs > li > a:hover, .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{border: none;}
        .active.custom-tab a, .active.custom-tab a > span, .active.custom-tab a:before, .active.custom-tab a:after{background-color: #fff !important;}
        .custom-tab-container .custom-tab a:after, .custom-tab-container .custom-tab a:before {position: absolute;content: '';top: 0;display: block;width: 50px;height: 110px;-webkit-transition: background-color 0.3s ease-out;-o-transition: background-color 0.3s ease-out;transition: background-color 0.3s ease-out}
        .custom-tab-container .custom-tab a:after {right: 0;-webkit-transform: rotate(-22deg);-ms-transform: rotate(-22deg);-o-transform: rotate(-22deg);transform: rotate(-22deg);}
        .custom-tab-container .custom-tab a:before {left: 0;-webkit-transform: rotate(22deg);-ms-transform: rotate(22deg);-o-transform: rotate(22deg);transform: rotate(22deg);}
        .custom-tab-container .custom-tab a:after, .custom-tab-container .custom-tab a:before {position: absolute;content: '';top: 0;display: block;width: 50px; height: 110px; -webkit-transition: background-color 0.3s ease-out;-o-transition: background-color 0.3s ease-out;transition: background-color 0.3s ease-out;}
        .custom-tab-container .custom-tab a > span {position: relative;z-index: 99;-webkit-transition: background-color 0.3s ease-out;-o-transition: background-color 0.3s ease-out;transition: background-color 0.3s ease-out;}
    </style>
</head>

<body>
    <div style="background: #eeeeef;">
        <div style="width: 1230px;margin: 0 auto;padding: 0 15px;">
            <div style="padding: 87px 0 0;">
                <h1 style="font-size: 54px;color:#ff5f44;margin-bottom: 30px;font-family: 'am';line-height: 1;">{{$professionsData->pf_name}}</h1>
                <div style="height: 643px;position: relative;width: 100%;"><img src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}" style="width: 100%;height: 100%;">
                    <div style="position: absolute;display: block;top: 50%;left: 50%;transform: translate(-50%,-50%);"><a href="javascript:void(0);" id="iframe-video"><img src="img/play-icon.png" alt="play icon" style="height: 100%;width: 100%;"></a></div>
                </div>
            </div>
            <!-- detail content -->
            <div style="position: relative">
                <div style="margin: 0" class="clearfix">
                    <div style="width: 66.66666667%;position: relative;float: left;padding: 0;">
                        <div style="margin-top: 5px">
                            <div style="margin: 0" class="clearfix">
                                <div style="width: 50%;float: left;">
                                    <ul style="background: #ff5f44;padding: 25px 20px;list-style-type:none;margin-right: 5px;margin-bottom: 5px;">
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;width: 15%;margin-right: 20px;text-align: right">
                                            <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?>
                                        </li>
                                        <?php
                                            $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'average_per_year_salary';
                                            })->first();
                                        ?>
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;">
                                            <h4 style="font-family: ht;font-size: 31px;color: #fff;margin-bottom: 0;">
                                                <?php 
                                                    echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' 
                                                ?>
                                            </h4>
                                            <p style="color: #fff;font-family: hn-b;">Average per year</p>
                                        </li>
                                    </ul>
                                </div>
                                <div style="width: 50%;float: left;">
                                    <ul style="background: #65c6e6;padding: 25px 20px;list-style-type:none;margin-right: 5px;margin-bottom: 5px;">
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;width: 15%;margin-right: 20px;text-align: right"><i class="icon-clock"></i></li>
                                        <?php
                                            $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'work_hours_per_week';
                                            })->first();
                                        ?>
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;">
                                            <h4 style="font-family: ht;font-size: 31px;color: #fff;margin-bottom: 0;">
                                                <?php echo (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?>
                                            </h4>
                                            <p style="color: #fff;font-family: hn-b;">Hours per week</p>
                                        </li>
                                    </ul>
                                </div>
                                <div style="width: 50%;float: left;">
                                    <ul style="background: #27a6b5;padding: 25px 20px;list-style-type:none;margin-right: 5px;margin-bottom: 5px;">
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;width: 15%;margin-right: 20px;text-align: right"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_current';
                                            })->first();
                                        ?>
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;">
                                            <h4 style="font-family: ht;font-size: 31px;color: #fff;margin-bottom: 0;">
                                                <?php echo (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?>
                                            </h4>
                                            <p style="color: #fff;font-family: hn-b;">Employment 2017</p>
                                        </li>
                                    </ul>
                                </div>
                                <div style="width: 50%;float: left;">
                                    <ul style="background: #73376d;padding: 25px 20px;list-style-type:none;margin-right: 5px;margin-bottom: 5px;">
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;width: 15%;margin-right: 20px;text-align: right"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_projected';
                                            })->first();
                                        ?>
                                        <li style="display: inline-block;vertical-align: middle;color: #fff;font-size: 50px;">
                                            <h4 style="font-family: ht;font-size: 31px;color: #fff;margin-bottom: 0;">
                                                <?php echo (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?>
                                            </h4>
                                            <p style="color: #fff;font-family: hn-b;">Projected for 2026</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--description-->
                        <div style="padding: 50px 20px 50px 30px" class="clearfix">
                            <div style="margin-bottom: 25px;">
                                <h4 style="display: inline-block;font-size: 31px;font-family: hl;color: #565b5f;margin-bottom: 15px;">{{$professionsData->pf_name}}</h4>
                            </div>
                            <?php
                                $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                            ?>
                            <p style="font-size: 20px;color: #565b5f;font-family: h;line-height: 1.6;margin-bottom: 10px;">
                                <?php echo (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?>
                            </p>
                        </div>
                        <!--description end-->
                        <!-- tabbing section-->
                        <div style="padding: 0 0 42px;margin-bottom: 50px;margin-right: 5px;background: #fff;">
                            <div style="padding: 40px 30px 0">

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_outlook = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_outlook';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">
                                        Outlook
                                    </h4>
                                    @if(isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">{!!$profession_outlook->pfic_content!!}</p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $AI_redundancy_threat = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'ai_redundancy_threat';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">
                                        AI Redundancy Threat
                                    </h4>
                                    @if(isset($AI_redundancy_threat->pfic_content) && !empty($AI_redundancy_threat->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">{!!$AI_redundancy_threat->pfic_content!!}</p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Subjects</h4>
                                    @if(isset($professionsData->professionSubject) && !empty($professionsData->professionSubject))
                                        <div style="margin-top: 50px;">
                                            <ul style="padding: 0;text-align: left;list-style: none;">
                                                @forelse($professionsData->professionSubject as $professionSubject)
                                                    @if($professionSubject->parameter_grade == 'M' || $professionSubject->parameter_grade == 'H')
                                                    <?php
                                                        if(isset($professionSubject->subject['ps_image']) && $professionSubject->subject['ps_image'] != '' && Storage::size($professionSubjectImagePath.$professionSubject->subject['ps_image']) > 0){
                                                            $subjectImageUrl = $professionSubjectImagePath.$professionSubject->subject['ps_image'];
                                                        }
                                                        else{
                                                            $subjectImageUrl = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                                        }
                                                    ?>
                                                        <li style="display: inline-block;width: 50%;margin-left: -4px;margin-bottom: 30px;vertical-align: top;padding: 0 15px;">
                                                            <div style="max-height: 190px;">
                                                                <img src="{{ Storage::url($subjectImageUrl) }}" alt="{{$professionSubject->subject['ps_name']}}" style="width: 100%;height: 190px;">
                                                            </div>
                                                            <span style="display: block;font-size: 18px;font-family: 'h';margin: 10px 0;font-weight: 700;color: #565b5f;">
                                                                {{$professionSubject->subject['ps_name']}}
                                                            </span>
                                                        </li>
                                                    @endif
                                                @empty
                                                @endforelse
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Abilities</h4>
                                    @if(isset($professionsData->ability) && !empty($professionsData->ability))
                                        <div style="margin-top: 50px;">
                                            <ul style="padding: 0;text-align: left;list-style: none;">
                                                @foreach($professionsData->ability as $key => $value)
                                                    <li style="display: inline-block;width: 50%;margin-left: -4px;margin-bottom: 30px;vertical-align: top;padding: 0 15px;">
                                                        <div style="max-height: 190px;">
                                                            <img src="{{ $value['cm_image_url'] }}" alt="{{$value['cm_name']}}" style="width: 100%;height: 190px;">
                                                        </div>
                                                        <span style="display: block;font-size: 18px;font-family: 'h';margin: 10px 0;font-weight: 700;color: #565b5f;">
                                                            {{$value['cm_name']}}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_job_activities = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_job_activities';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Activities</h4>
                                    @if(isset($profession_job_activities->pfic_content) && !empty($profession_job_activities->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">{!!$profession_job_activities->pfic_content!!}</p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_workplace = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_workplace';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Work Place</h4>
                                    @if(isset($profession_workplace->pfic_content) && !empty($profession_workplace->pfic_content))
                                        {!!$profession_workplace->pfic_content!!}
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_skills = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_skills';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Skills</h4>
                                    @if(isset($profession_skills->pfic_content) && !empty($profession_skills->pfic_content))
                                        {!!$profession_skills->pfic_content!!}
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_personality = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_personality';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Personality</h4>
                                    @if(isset($profession_personality->pfic_content) && !empty($profession_personality->pfic_content))
                                        {!!$profession_personality->pfic_content!!}
                                    @endif
                                </div>

                                <!-- <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Education</h4>
                                    <p style="font-size: 20px;font-family: hl;color: #565b5f;">Sophomore 10th Grade <br> Senior 12th Grade<br> Senior Year in - Landscape Architecture (BLA) <br> Master of Landscape Architecture (MLA)</p>
                                    <p style="margin-top: 30px;"><img src="img/graph-img.png" alt="graph"></p>
                                </div> -->

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Certifications</h4>
                                    @if(isset($professionsData->professionCertificates) && !empty($professionsData->professionCertificates))
                                        <div style="margin-top: 50px;">
                                            <ul style="padding: 0;text-align: left;list-style: none;">
                                                @forelse($professionsData->professionCertificates as $professionCertificate)
                                                    <li style="display: inline-block;width: 50%;margin-left: -4px;margin-bottom: 30px;vertical-align: top;padding: 0 15px;">
                                                        <div style="max-height: 190px;">
                                                            <img src="{{ Storage::url($professionCertificationImagePath.$professionCertificate->certificate['pc_image']) }}" alt="{{$professionCertificate->certificate['pc_image']}}" style="width: 100%;height: 190px;">
                                                        </div>
                                                    </li>
                                                @empty
                                                @endforelse
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_licensing = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_licensing';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Licensing</h4>
                                    @if(isset($profession_licensing->pfic_content) && !empty($profession_licensing->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">
                                            {!!$profession_licensing->pfic_content!!}
                                        </p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_experience = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_experience';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Experience</h4>
                                    @if(isset($profession_experience->pfic_content) && !empty($profession_experience->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">
                                            {!!strip_tags($profession_experience->pfic_content)!!}
                                        </p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_growth_path = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_growth_path';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Growth Path</h4>
                                    @if(isset($profession_growth_path->pfic_content) && !empty($profession_growth_path->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">
                                            {!!$profession_growth_path->pfic_content!!}
                                        </p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $salary_range = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'salary_range';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Salary Range</h4>
                                    @if(isset($salary_range->pfic_content) && !empty($salary_range->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;color: #565b5f;">
                                            {!!$salary_range->pfic_content!!}
                                        </p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $profession_bridge = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_bridge';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">Apprenticeships</h4>
                                    @if(isset($profession_bridge->pfic_content) && !empty($profession_bridge->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;">{!!$profession_bridge->pfic_content!!}</p>
                                    @endif
                                </div>

                                <div style="margin-bottom: 40px;font-size: 20px;color: #565b5f;font-family: hl;">
                                    <?php
                                        $trends_infolinks_usa = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'trends_infolinks';
                                        })->first();
                                    ?>
                                    <h4 style="font-size: 31px;font-family: hl;color: #565b5f;text-transform: capitalize;margin-bottom: 25px">General Information And Links</h4>
                                    @if(isset($trends_infolinks_usa->pfic_content) && !empty($trends_infolinks_usa->pfic_content))
                                        <p style="font-size: 20px;font-family: hl;">{!!$trends_infolinks_usa->pfic_content!!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- tabbing section end-->

                        <!-- connect sec -->
                            <!-- <div style="margin-bottom: 6px;margin-right: 5px;background: #eeeeef;">
                                <h2 style="transform-style: 31px;font-family:hl;color: #565b5f;">Connect</h2>
                                <div style="background: #fff;padding: 0 0 42px;margin-top: 40px;">
                                    <ul style="background: #eeeeef;overflow: hidden;list-style: none;padding: 0;" class="clearfix">
                                        <li style="padding: 0 ;float: left;width: 50%;">
                                            <a href="#" style="background: #fff;color: #565b5f;display: block;position: static;padding: 0 25px;height: 90px;margin: 0;font-family: hn-b;font-size: 26px;"><span style="display: table;height: 100%;width: 100%;">
                                            <span style="display: table-cell;width: 100%;height: 100%;vertical-align: middle;">Leaderboard</span>
                                        </span></a>
                                        </li>
                                        <li style="padding: 0;float: left;width: 50%;">
                                            <a href="#" style="background: #73376d ;color: #fff;display: block;position: static;padding: 0 25px;height: 90px;margin: 0;font-family: hn-b;font-size: 26px;"><span style="display: table;height: 100%;width: 100%;">
                                            <span style="display: table-cell;width: 100%;height: 100%;vertical-align: middle;">Fans of this career</span>
                                        </span></a>
                                        </li>
                                    </ul>
                                    <div style="padding: 40px">
                                        <div style="max-width: 670px;margin: 0 auto 30px;display: flex;flex-wrap: wrap;font-size: 21px;color: #565b5f;">
                                            <div style="display: inline-flex;flex-basis: 50%;align-items: center;padding: 0 15px;">
                                                <div style="font-family: hl;font-size: 21px;color: #565b5f;position: relative;width: 100%;display: table;">
                                                    <div style="width: 83px;height: 83px;display: table-cell;"><img src="img/ellen.jpg" alt="team"></div><a href="#" title="Ellen Ripley" style="color: #565b5f;text-decoration: none;display: table;margin: 0 10px 0 20px;line-height: 1.2;"> Ellen Ripley</a>
                                                </div>
                                            </div>
                                            <div style="display: inline-flex;flex-basis: 50%;align-items: center;padding: 0 15px;">
                                                <div style="font-family: hn-b;">520,000 points<a href="#" title="Chat" style="color: #565b5f;"><i class="icon-chat" style="font-size: 60px;color: #73376d;font-weight: 700;margin-left: 60px;display: inline-block;vertical-align: middle;"></i></a></div>
                                            </div>
                                        </div>
                                        <div style="max-width: 670px;margin: 0 auto 30px;display: flex;flex-wrap: wrap;font-size: 21px;color: #565b5f;">
                                            <div style="display: inline-flex;flex-basis: 50%;align-items: center;padding: 0 15px;">
                                                <div style="font-family: hl;font-size: 21px;color: #565b5f;position: relative;width: 100%;display: table;">
                                                    <div style="width: 83px;height: 83px;display: table-cell;"><img src="img/ellen.jpg" alt="team"></div><a href="#" title="Ellen Ripley" style="color: #565b5f;text-decoration: none;display: table;margin: 0 10px 0 20px;line-height: 1.2;"> Ellen Ripley</a>
                                                </div>
                                            </div>
                                            <div style="display: inline-flex;flex-basis: 50%;align-items: center;padding: 0 15px;">
                                                <div style="font-family: hn-b;">520,000 points<a href="#" title="Chat" style="color: #565b5f;"><i class="icon-chat" style="font-size: 60px;color: #73376d;font-weight: 700;margin-left: 60px;display: inline-block;vertical-align: middle;"></i></a></div>
                                            </div>
                                        </div>
                                        <p style="text-align: center;"><a href="javascript:void(0);" title="see more" style="font-family: hn-b;font-size: 18px;text-transform: uppercase;color: #73376d;text-decoration: underline;">see more</a></p>
                                    </div>
                                </div>
                            </div> -->
                        <!-- connect sec end-->
                        
                        <!-- ad sec -->
                            <!-- <div style="height: 90px;max-width: 850px;width: 100%;background: #fff;margin: 5px 0 50px;">
                                <div style="display: table; width: 100%;height: 100%;text-align: center;">
                                    <div style="display: table-cell;vertical-align: middle;font-size: 21px;font-family: hn-b;color: #161c22;text-transform: capitalize;">
                                        Ad 2 850 x 90
                                    </div>
                                </div>
                            </div> -->
                        <!-- ad sec end -->

                    </div>
                    <div style="width: 33.33333333%;float: left;">
                        <div style="background: #fff;text-align: center;padding: 40px 0;position: relative;">
                            <div style="position: relative;text-align: center;padding-top: 30px;">
                                <div style="position: relative;overflow: hidden;width: 185px;height: 90px;margin: 0 auto -14px;">
                                    <div style="position: absolute;top: 0;left: 0;width: 185px;height: 190px;-webkit-border-radius: 50%;border-radius: 50%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;border: 10px solid #eee;border-bottom-color: #1ec89d;border-right-color: #1ec89d;transform: rotate(189deg);"></div>
                                </div>
                            </div>
                            <h3 style="margin-top: 0;position: absolute;bottom: 12px;left: 50%;transform: translateX(-50%);font-size: 38px;font-family: hl;color: rgba(22, 28, 34, 0.7);">Match</h3>
                        </div>
                        <!-- advanced section-->
                        <div style="margin-top: 4px;margin-bottom: 20px;">
                            <div style="font-size: 16px;">
                                <a href="#" style="display: block;padding: 27px 15px 27px 45px;color: #fff;font-family: ht;font-size: 24px;position: relative;background: #565b5f;">Advanced View</a>
                            </div>
                            <div style="padding: 4px 0;">
                                <div style="background: rgba(255, 255, 255, 0.5);margin-bottom: 4px;padding: 14px 15px;">
                                    <div style="width: 45%;display: inline-block;vertical-align: middle; font-family: hn-b;font-size: 16px;color: #565b5f; padding-right: 5px;">
                                        MI parameter 1
                                    </div>
                                    <div style="width: 55%;display: inline-block;margin-left: -4px;vertical-align: middle;margin-bottom: 0;height: 12px;background: #ebebeb;overflow:hidden;box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);">
                                        <div style="float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;text-align: center;box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);transition: width .6s ease;background: linear-gradient(to right, #f28538 0%, #f2c156 58%, #07c9a5 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#endColorstr='#07c9a5', GradientType=1);" role="progressbar" data-width="90" class="progress-bar">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- advanced section end-->
                        <!-- tag section-->
                        <div style="padding: 0 10px;margin-bottom: 40px;">
                            <h4 style="font-family: hl;font-size: 24px;color: #565b5f;display: inline-block;">Tags</h4>
                            <ul style="padding: 0;list-style-type: none;margin-top: 20px">
                                @forelse($professionsData->professionTags as $professionTags)
                                    <li style="display: inline-block;font-family: hl;font-size: 16px;color: #fff;text-transform: capitalize;border: 1px solid #73376d;padding: 9px 10px;margin: 0 3px 3px 0px;background: #73376d;"><a href="#" title="Lorem ipsum" style="color: #fff;">{{$professionTags->tag['pt_name']}}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        <!-- tag section end-->

                        <!--ad sec-->
                            <!-- <div style="max-width: 343px;height: 400px;background: #fff;width: 100%;margin-bottom: 7px;">
                                <div style="display: table;width: 100%;height: 100%;text-align: center;">
                                    <div style="display: table-cell;vertical-align: middle;font-size: 21px;font-family: hn-b;color: #161c22;text-transform: capitalize;">
                                        Ad 2 343 x 400
                                    </div>
                                </div>
                            </div> -->
                        <!--ad sec end-->
                        
                        <!--ad sec-->
                            <!-- <div style="max-width: 343px;height: 800px;background: #fff;width: 100%;margin-bottom: 7px;">
                                <div style="display: table;width: 100%;height: 100%;text-align: center;">
                                    <div style="display: table-cell;vertical-align: middle;font-size: 21px;font-family: hn-b;color: #161c22;text-transform: capitalize;">
                                        Ad 2 343 x 800
                                    </div>
                                </div>
                            </div> -->
                        <!--ad sec end-->

                    </div>
                </div>
            </div>
            <!-- detail content end-->

        </div>
    </div>
</body>

</html>
