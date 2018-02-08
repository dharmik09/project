<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <!-- Bootstrap -->
        <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('css/owl.css')}}" rel="stylesheet">
        <link href="{{asset('css/magnific-popup.css')}}" rel="stylesheet">
        <link href="{{asset('css/aos.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
    </head>
    <body>
    <div class="bg-offwhite">
    <!-- mid section starts-->
    <!-- mid section-->
    <div class="container">
        <section class="career-detail">
            <h1>{{$professionsData->pf_name}}</h1>
           
            <div class="career-banner banner-landing">
                    <img src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}">  
                </div>
            <div class="detail-content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="career-stat">
                            <div class="row">

                                <div class="col-sm-6">
                                    <ul class="color-1">
                                        <li class="icon"><?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?></li>
                                        <?php
                                            $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'average_per_year_salary';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' ?></h4>
                                            <p>Average per year</p>
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
                                            <p>Hours per week</p>
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
                                            <p>Employment 2017</p>
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
                                            <p>Projected for 2026</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <div class="heading">
                                <h4>{{$professionsData->pf_name}}</h4>
                            </div>
                            <?php
                                $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                            ?>
                            <p><?php echo (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?></p>
                        </div>
                        <div class="career-detail-tab bg-white">
                            <div class="tab-content">
                                <div id="menu1" class="tab-pane fade in active">
                                    <?php
                                        $profession_outlook = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_outlook';
                                        })->first();
                                    ?>
                                                                        
                                    <div class="block">
                                        <h4> Outlook</h4>
                                        @if(isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content))
                                            <p>{!!$profession_outlook->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <?php
                                        $AI_redundancy_threat = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'ai_redundancy_threat';
                                        })->first();
                                    ?>

                                    <div class="block">
                                        <h4> AI Redundancy Threat</h4>
                                        @if(isset($AI_redundancy_threat->pfic_content) && !empty($AI_redundancy_threat->pfic_content))
                                            <p>{!!$AI_redundancy_threat->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <div class="block">
                                        <h4>Subjects</h4>
                                        @if(isset($professionsData->professionSubject) && !empty($professionsData->professionSubject))
                                            <div class="img-list">
                                                <ul>
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
                                                            <li>
                                                                <img src="{{ Storage::url($subjectImageUrl) }}" alt="compatia logo">
                                                                <a href="{{url('/teenager/interest')}}/it_{{$professionSubject->subject['ps_slug']}}"><span>{{$professionSubject->subject['ps_name']}}</span></a>
                                                            </li>
                                                        @endif
                                                    @empty
                                                    @endforelse
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="block">
                                        <h4>Abilities</h4>
                                        @if(isset($professionsData->ability) && !empty($professionsData->ability))
                                            <div class="img-list">
                                                <ul>
                                                    @foreach($professionsData->ability as $key => $value)
                                                        <li>
                                                            <img src="{{ $value['cm_image_url'] }}" alt="compatia logo">
                                                            <a href="{{$value['cm_slug_url']}}"><span>{{$value['cm_name']}}</span></a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <?php
                                        $profession_job_activities = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_job_activities';
                                        })->first();
                                    ?>

                                    <div class="block">
                                        <h4>Activities</h4>
                                        @if(isset($profession_job_activities->pfic_content) && !empty($profession_job_activities->pfic_content))
                                            {!!$profession_job_activities->pfic_content!!}
                                        @endif
                                    </div>

                                    <?php
                                        $profession_workplace = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_workplace';
                                        })->first();
                                    ?>

                                    <div class="block">
                                        <h4>Work Place</h4>
                                        @if(isset($profession_workplace->pfic_content) && !empty($profession_workplace->pfic_content))
                                            {!!$profession_workplace->pfic_content!!}
                                        @endif
                                    </div>

                                    <?php
                                        $profession_skills = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_skills';
                                        })->first();
                                    ?>

                                    <div class="block">
                                        <h4>Skills</h4>
                                        @if(isset($profession_skills->pfic_content) && !empty($profession_skills->pfic_content))
                                            {!!$profession_skills->pfic_content!!}
                                        @endif
                                    </div>

                                    <?php
                                        $profession_personality = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_personality';
                                        })->first();
                                    ?>

                                    <div class="block">
                                        <h4>Personality</h4>
                                        @if(isset($profession_personality->pfic_content) && !empty($profession_personality->pfic_content))
                                            {!!$profession_personality->pfic_content!!}
                                        @endif
                                    </div>

                                    <?php
                                        $profession_education_path = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_education_path';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Education</h4>
                                        @if(isset($profession_education_path->pfic_content) && !empty($profession_education_path->pfic_content))
                                        <p>{!!$profession_education_path->pfic_content!!}</p>
                                        @endif
                                        <div id="education_chart">{!!$chartHtml!!}</div>  
                                    </div>

                                    <div class="block">
                                        <h4>Certifications</h4>
                                        @if(isset($professionsData->professionCertificates) && !empty($professionsData->professionCertificates))
                                            <div class="img-list">
                                                <ul>
                                                    @forelse($professionsData->professionCertificates as $professionCertificate)
                                                    <li><img src="{{ Storage::url($professionCertificationImagePath.$professionCertificate->certificate['pc_image']) }}" alt="compatia logo"></li>
                                                    @empty
                                                    @endforelse
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <?php
                                        $profession_licensing = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_licensing';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Licensing</h4>
                                        @if(isset($profession_licensing->pfic_content) && !empty($profession_licensing->pfic_content))
                                            <p>{!!$profession_licensing->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <?php
                                        $profession_experience = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_experience';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Experience</h4>
                                        @if(isset($profession_experience->pfic_content) && !empty($profession_experience->pfic_content))
                                            {!!strip_tags($profession_experience->pfic_content)!!}
                                        @endif
                                    </div>

                                    <?php
                                        $profession_growth_path = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_growth_path';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Growth Path</h4>
                                        @if(isset($profession_growth_path->pfic_content) && !empty($profession_growth_path->pfic_content))
                                        <p>{!!$profession_growth_path->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <?php
                                        $salary_range = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'salary_range';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Salary Range</h4>
                                        @if(isset($salary_range->pfic_content) && !empty($salary_range->pfic_content))
                                            <p>{!!$salary_range->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <?php
                                        $profession_bridge = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'profession_bridge';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>Apprenticeships</h4>
                                        @if(isset($profession_bridge->pfic_content) && !empty($profession_bridge->pfic_content))
                                            <p>{!!$profession_bridge->pfic_content!!}</p>
                                        @endif
                                    </div>

                                    <?php
                                        $trends_infolinks_usa = $professionsData->professionHeaders->filter(function($item) {
                                            return $item->pfic_title == 'trends_infolinks';
                                        })->first();
                                    ?>
                                    <div class="block">
                                        <h4>General Information and Links</h4>
                                        @if(isset($trends_infolinks_usa->pfic_content) && !empty($trends_infolinks_usa->pfic_content))
                                            <p>{!!$trends_infolinks_usa->pfic_content!!}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sec-tags">
                            <h4>Tags</h4>
                            <ul class="tag-list">
                                @forelse($professionsData->professionTags as $professionTags)
                                    <li><a href="{{ url('/teenager/career-tag/'.$professionTags->tag['pt_slug']) }}" title="Lorem ipsum">{{$professionTags->tag['pt_name']}}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
        <!-- mid section end-->
    </div>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/masonry.pkgd.js') }}"></script>
    <script src="{{ asset('js/aos.js') }}"></script>
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/general.js') }}"></script>
    <script src="{{ asset('backend/js/highchart.js')}}"></script>
    </body>
</html>