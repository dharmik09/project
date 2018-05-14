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
        @page { margin: 90px 50px; }
        #header { position: fixed; top: -60px; right: 0px;  height: 40px;padding-bottom: 10px;}
        .clearfix:after {
          clear: both;
        }
        .pagebreak {
            page-break-after:always;
            position: relative;
        }
        #footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 40px; border-top: 1px solid;}
        #footer .page:after { content: counter(page); text-align: right; position: absolute; right:0; }
        .content_link a { color: #00f; }
    </style>
</head>

<body>
   <div id="header">
        <table>
            <tr>
                <td>
                    <span><h1 style="color:#ff5f44; font-family: 'am';line-height: 1;">{{$professionsData->pf_name}}</h1></span>
                </td>
            </tr>
        </table>
    </div>
    <div id="footer" class="clearfix">
        <p class="page" >Copyright &copy; <?php echo date('Y');?> <span style="color:#E66A45;"> ProTeen</span>. All rights reserved.</p>
    </div>
    <div style="height: 500px; width: 99.5%; margin-top: 30px;">
        <img src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}" style="width: 100%;height: 100%;">
        <div style="position: absolute;display: block;top: 50%;left: 50%;transform: translate(-50%,-50%);">
        </div>
    </div>

    <div style="margin-top: 10px; width: 100%;" class="clearfix">
        <div style="width: 100%;display: inline-block; vertical-align: top;padding-top: 20px; margin-bottom: 10px;" class="clearfix">
            <div class="clearfix" style="padding: 5px 0px 5px 0px;">
                <div class="clearfix" style="display: inline-block;">
                    <div class="clearfix" style="padding: 18px 5px 18px 5px; background: #ff5f44">
                        <div style="width: 17%; display: inline-block; vertical-align: middle;color: #fff;font-size: 54px; text-align: right;">
                            <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?>
                        </div>
                        <div class="clearfix" style="width: 31%; display: inline-block; margin-top: 10px;">
                            <div style="display: block;">
                                <?php
                                    $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                        return $item->pfic_title == 'average_per_year_salary';
                                    })->first();
                                ?>
                                <h4 style="font-family: ht;font-size: 34px;color: #fff;margin-bottom: 0;">
                                    <?php 
                                        echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' 
                                    ?>
                                </h4>
                            </div>
                            <div style="display: block;">
                                <p style="color: #fff;font-family: hn-b;">Average per year</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix" style="display: inline-block;">
                    <div class="clearfix" style="padding: 18px 0px 18px 0px; background: #65c6e6">
                        <div style="width: 17%; display: inline-block; vertical-align: middle;color: #fff;font-size: 54px; text-align: right;">
                            <i class="icon-clock"></i>
                        </div>
                        <div class="clearfix" style="width: 31%; display: inline-block; margin-top: 10px;">
                            <div style="display: block;">
                                <?php
                                    $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                        return $item->pfic_title == 'work_hours_per_week';
                                    })->first();
                                ?>
                                <h4 style="font-family: ht;font-size: 34px;color: #fff;margin-bottom: 0;">
                                    <?php echo (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?>
                                </h4>
                            </div>
                            <div style="display: block;">
                                <p style="color: #fff;font-family: hn-b;">Hours per week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix" style="padding: 0px 0px 5px 0px;">
                <div class="clearfix" style="display: inline-block;">
                    <div class="clearfix" style="padding: 18px 5px 18px 5px; background: #27a6b5">
                        <div style="width: 17%; display: inline-block; vertical-align: middle;color: #fff;font-size: 54px; text-align: right;">
                            <i class="icon-pro-user"></i>
                        </div>
                        <div class="clearfix" style="width: 31%; display: inline-block; margin-top: 10px;">
                            <div style="display: block;">
                                <?php
                                    $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                        return $item->pfic_title == 'positions_current';
                                    })->first();
                                ?>
                                <h4 style="font-family: ht;font-size: 34px;color: #fff;margin-bottom: 0;">
                                    <?php echo (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?>
                                </h4>
                            </div>
                            <div style="display: block;">
                                <p style="color: #fff;font-family: hn-b;">Employment 2017</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix" style="display: inline-block;">
                    <div class="clearfix" style="padding: 18px 0px 18px 0px; background: #73376d">
                        <div style="width: 17%; display: inline-block; vertical-align: middle;color: #fff;font-size: 54px; text-align: right;">
                            <i class="icon-pro-user"></i>
                        </div>
                        <div class="clearfix" style="width: 31%; display: inline-block; margin-top: 10px;">
                            <div style="display: block;">
                                <?php
                                    $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                        return $item->pfic_title == 'positions_projected';
                                    })->first();
                                ?>
                                <h4 style="font-family: ht;font-size: 34px;color: #fff;margin-bottom: 0;">
                                    <?php echo (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?>
                                </h4>
                            </div>
                            <div style="display: block;">
                                <p style="color: #fff;font-family: hn-b;">Projected for 2026</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix" style="">
            <table class="clearfix" style="font-size: 18px; color: #565b5f; font-family: hl;">
                <tr>
                    <td style="font-size: 28px;">
                        Career Details
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_description';
                            })->first();
                        ?>
                        <?php echo (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?>
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Outlook</td>
                </tr>
                <tr>
                    <td>
                        <?php
                            $profession_outlook = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_outlook';
                            })->first();
                        ?>
                        @if(isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content))
                            {!!$profession_outlook->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">AI Redundancy Threat</td>
                </tr>
                <tr>
                    <td>
                        <?php
                            $AI_redundancy_threat = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'ai_redundancy_threat';
                            })->first();
                        ?>
                        @if(isset($AI_redundancy_threat->pfic_content) && !empty($AI_redundancy_threat->pfic_content))
                            {!!$AI_redundancy_threat->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Subjects</td>
                </tr>   
            </table>
            <div class="clearfix pagebreak" style="margin-top: 30px;">
            @if(isset($professionsData->professionSubject) && !empty($professionsData->professionSubject))
            <?php $column_count = 0; ?>
            @forelse($professionsData->professionSubject as $professionSubject)
            @if($professionSubject->parameter_grade == 'M' || $professionSubject->parameter_grade == 'H')
            <?php
            if(isset($professionSubject->subject['ps_image']) && $professionSubject->subject['ps_image'] != '' && Storage::size($professionSubjectImagePath.$professionSubject->subject['ps_image']) > 0){
                    $subjectImageUrl = $professionSubjectImagePath.$professionSubject->subject['ps_image'];
                } else {
                    $subjectImageUrl = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                }
            ?>
            <div style="width:300px; text-align:center; display:inline-block; margin:0 10px 10px 0; vertical-align: top;">
                <img src="{{ Storage::url($subjectImageUrl) }}" alt="{{$professionSubject->subject['ps_name']}}" width="250px" height="170px" class="circular" style="display: block;">
                <span style="display: block; font-size: 18px; font-family: 'h'; font-weight: 700; color: #565b5f;">{{$professionSubject->subject['ps_name']}}</span>
            </div>
            <?php
                $column_count++;
                if ($column_count == 2) {
                    $column_count = 0;
                    echo '<br>';
                }
            ?>
            @endif
            @empty
            <span style="display: block; font-size: 18px; font-family: 'h'; font-weight: 700; color: #565b5f;">No records found </span>
            @endforelse
            @else
            @endif
        </div>
        <table style="font-size: 18px; color: #565b5f; font-family: hl;">
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td style="font-size: 28px;">Abilities</td>
            </tr>
        </table>
        <div class="clearfix" style="margin-top: 30px;">
        @if(isset($professionsData->ability) && !empty($professionsData->ability))
        <?php $abilityColCount = 0; ?>
        @foreach($professionsData->ability as $key => $value)
            <div style="width: 300px; text-align:center; display:inline-block; margin:0 10px 10px 0;">
                <img src="{{ $value['cm_image_url'] }}" alt="{{$value['cm_name']}}" width="250px" height="170px" class="circular" >
                <span style="display: block; font-size: 18px; font-family: 'h'; font-weight: 700; color: #565b5f;">{{$value['cm_name']}}</span>
            </div>
            <?php
                $column_count++;
                if ($column_count == 2) {
                    $column_count = 0;
                    echo '<br>';
                }
            ?>
        @endforeach
        @else
            <span style="display: block; font-size: 18px; font-family: 'h'; color: #565b5f;">
                No records found
            </span>
        @endif
        </div>
        <table style="font-size: 18px; color: #565b5f; font-family: hl;">
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Activities</td>
                </tr>
                <tr>
                    <td>
                       <?php
                            $profession_job_activities = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_job_activities';
                            })->first();
                        ?>
                        @if(isset($profession_job_activities->pfic_content) && !empty($profession_job_activities->pfic_content))
                            {!!$profession_job_activities->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Work Place</td>
                </tr>
                <tr>
                    <td>
                       <?php
                            $profession_workplace = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_workplace';
                            })->first();
                        ?>
                        @if(isset($profession_workplace->pfic_content) && !empty($profession_workplace->pfic_content))
                            {!!$profession_workplace->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Skills</td>
                </tr>
                <tr>
                    <td>
                        <?php
                            $profession_skills = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_skills';
                            })->first();
                        ?>
                        @if(isset($profession_skills->pfic_content) && !empty($profession_skills->pfic_content))
                            {!!$profession_skills->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Personality</td>
                </tr>
                <tr>
                    <td>
                        <?php
                            $profession_personality = $professionsData->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_personality';
                            })->first();
                        ?>
                        @if(isset($profession_personality->pfic_content) && !empty($profession_personality->pfic_content))
                            {!!$profession_personality->pfic_content!!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Education</td>
                </tr>
                <tr>
                    <td>
                        Sophomore 10th Grade
                    </td>
                </tr>
                <tr>
                    <td>
                        Senior 12th Grade or equivalent diploma 
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="img/graph-img.png" alt="graph">
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Certifications</td>
                </tr>
                <!-- <tr>
                    <td></td>
                </tr> -->
            </table>
            <div class="clearfix pagebreak" style="margin-top: 30px;">
            @if(isset($professionsData->professionCertificates) && !empty($professionsData->professionCertificates))
            <?php $certificationColCount = 0; ?>
            @forelse($professionsData->professionCertificates as $professionCertificate)
                <div style="width: 300px; text-align:center; display:inline-block; margin:0 10px 10px 0;">
                    <img src="{{ Storage::url($professionCertificationImagePath.$professionCertificate->certificate['pc_image']) }}" alt="{{$professionCertificate->certificate['pc_image']}}" width="250px" height="170px" class="circular" >
                    <!-- <span style="display: block; font-size: 18px; font-family: 'h'; font-weight: 700; color: #565b5f;">Certificate name</span> -->
                </div>
                <?php
                    $certificationColCount++;
                    if ($certificationColCount == 2) {
                        $certificationColCount = 0;
                        echo '<br>';
                    }
                ?>
            @empty
            <span style="display: block; font-size: 18px; font-family: 'h'; color: #565b5f;">
                No records found
            </span>
            @endforelse
            @endif
            </div>
            <table style="font-size: 18px; color: #565b5f; font-family: hl;">
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Licensing</td>
                </tr>
                <tr>
                    <td>
                    <?php
                        $profession_licensing = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'profession_licensing';
                        })->first();
                    ?>
                    @if(isset($profession_licensing->pfic_content) && !empty($profession_licensing->pfic_content))
                        {!!$profession_licensing->pfic_content!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Experience</td>
                </tr>
                <tr>
                    <td>
                    <?php
                        $profession_experience = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'profession_experience';
                        })->first();
                    ?>
                    @if(isset($profession_experience->pfic_content) && !empty($profession_experience->pfic_content))
                        {!!strip_tags($profession_experience->pfic_content)!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Growth Path</td>
                </tr>
                <tr>
                    <td>
                    <?php
                        $profession_growth_path = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'profession_growth_path';
                        })->first();
                    ?>
                    @if(isset($profession_growth_path->pfic_content) && !empty($profession_growth_path->pfic_content))
                        {!!$profession_growth_path->pfic_content!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Salary Range</td>
                </tr>
                <tr>
                    <td>
                    <?php
                        $salary_range = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'salary_range';
                        })->first();
                    ?>
                    @if(isset($salary_range->pfic_content) && !empty($salary_range->pfic_content))
                        {!!$salary_range->pfic_content!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">Apprenticeships</td>
                </tr>
                <tr>
                    <td class="content_link">
                    <?php
                        $profession_bridge = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'profession_bridge';
                        })->first();
                    ?>
                    @if(isset($profession_bridge->pfic_content) && !empty($profession_bridge->pfic_content))
                        {!!$profession_bridge->pfic_content!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td style="font-size: 28px;">General Information And Links</td>
                </tr>
                <tr>
                    <td class="content_link">
                    <?php
                        $trends_infolinks_usa = $professionsData->professionHeaders->filter(function($item) {
                            return $item->pfic_title == 'trends_infolinks';
                                    })->first();
                    ?>
                    @if(isset($trends_infolinks_usa->pfic_content) && !empty($trends_infolinks_usa->pfic_content))
                        {!!$trends_infolinks_usa->pfic_content!!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td class="content_link">
                        @if(isset($trends_infolinks_usa->pfic_content) && !empty($trends_infolinks_usa->pfic_content))
                            {!!$trends_infolinks_usa->pfic_content!!}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <table style="font-size: 18px; color: #565b5f; font-family: hl;">
            <tr>
                Advanced View
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
        </table>
        <div style="page-break-before: always;"></div>
        <div style="width: 100%; display: inline-block;vertical-align: top; padding: 0px 5px 5px 5px;margin-top: 0;">
                    <?php 
                        $matchScoreArray = ['match' => 100, 'nomatch' => 33, 'moderate' => 66];
                        $matchScalePoint = ( isset($professionsData->id) && isset($getTeenagerHML[$professionsData->id]) && isset($matchScoreArray[$getTeenagerHML[$professionsData->id]]) ) ? $matchScoreArray[$getTeenagerHML[$professionsData->id]] : 0;
                    ?>
                    <div class="clearfix">
                        <table style="font-size: 10px;">
                            <tr style="text-align: center;">
                                <td width="80px">
                                    <div style="height: 10px; width: 10px; background-color: #07c9a7; display: inline-block;">
                                    </div>
                                    <div style="display: inline-block; margin-top: 2px;">Strong Match</div>
                                </td>
                                <td width="90px">
                                    <div style="height: 10px; width: 10px; background-color: #f1c246; display: inline-block;">
                                    </div>
                                    <div style="display: inline-block; margin-top: 2px;">
                                        Potential Match
                                    </div>
                                </td>
                                <td width="90px">
                                    <div style="height: 10px; width: 10px; background-color: #f58634; display: inline-block;">
                                    </div>
                                    <div style="display: inline-block; margin-top: 2px;">
                                        Unlikely Match
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <?php 
                                    $matchScoreArray = ['match' => 100, 'nomatch' => 33, 'moderate' => 66];
                                    $matchScalePoint = ( isset($professionsData->id) && isset($getTeenagerHML[$professionsData->id]) && isset($matchScoreArray[$getTeenagerHML[$professionsData->id]]) ) ? $matchScoreArray[$getTeenagerHML[$professionsData->id]] : 0;
                                    if ($matchScalePoint == 33) {
                                        $matchName = 'Unlikely'; 
                                        $matchColor = '#f58634'; 
                                        $percentage = '100';
                                    } else if ($matchScalePoint == 66) {
                                        $matchName = 'Potential'; 
                                        $matchColor = '#f1c246'; 
                                        $percentage = '100';
                                    } else if ($matchScalePoint == 100) {
                                        $matchName = 'Strong'; 
                                        $matchColor = '#07c9a7'; 
                                        $percentage = '100';
                                    } else {
                                        $matchName = 'No Attempt'; 
                                        $matchColor = '#ccc'; 
                                        $percentage = '0';
                                    }
                                ?>
                                <td style="color: {{$matchColor}}; font-size: 15px;">
                                    Match
                                </td>
                                <td>
                                    <div style="background-color: {{$matchColor}}; height: 5px; display: inline-block; width: 100%"></div>
                                </td>    
                            </tr>
                            <tr>
                                <td height="10px">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" width="100%" height="50px" align="center" cellpadding="20" style="font-size: 20px; background-color: #565B5F; color: #fff;">Advanced View</td>
                            </tr>
                            @forelse($teenagerStrength as $value)
                                <tr>
                                    <td colspan="3" width="100%" height="50px" align="left" cellpadding="20" style="font-family: hn-b;font-size: 16px; color: #565b5f; background-color: #EEEEEF; padding-left: 10px;">{{$value['name']}}</td>
                                    <td colspan="3" width="100%" height="50px" align="left" cellpadding="20" style=" padding-left: 10px;">
                                        <!-- <div style="width: 70px; background-color: #ebebeb;"> -->
                                            <div style="width: 300px; background-color: #ebebeb; position: relative; height:20px; z-index: 1;"> 
                                                <div style="background-color: #ffe195; position: absolute;left: 0; top:0; height: 20px; z-index: 2; width: {{$value['score']}}%;"></div>
                                                <div style="background-color: #65c6e6; position: absolute; left: 0; top:0; height: 20px; z-index: 2; width: {{round($value['lowscoreH'])}}%;"></div>
                                            </div>
                                        <!-- </div> -->
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </table>
                    </div>
        </div>
        <table style="font-size: 18px; color: #565b5f; font-family: hl;">
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td style="font-size: 28px;">Hobbies</td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
        </table>
        <div class="clearfix">
            @forelse($professionsData->professionTags as $professionTags)
                <div style="display: inline-block; height: 20px; color: #fff; font-family: hl; font-size: 16px; background: #73376d; padding: 0px 2px 10px 5px;">
                    {{$professionTags->tag['pt_name']}}
                </div>
            @empty
            @endforelse
        </div>
    </div>
</body>

</html>
