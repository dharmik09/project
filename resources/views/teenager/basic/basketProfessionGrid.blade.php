<?php $video = isset($basketsData->b_video) ? Helpers::youtube_id_from_url($basketsData->b_video) : ""; ?>

@if(isset($basketsData) && !empty($basketsData))

<div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH')) }}{{ $basketsData->b_logo }} )">
    <div class="">
        <div class="play-icon">
            <a id="link{{$basketsData->id}}" onclick="playVideo(this.id, '{{$video}}')" class="play-btn" id="iframe-video">
                <img src="{{Storage::url('img/play-icon.png')}}" alt="play icon">
            </a>
        </div>
    </div>
    <iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$basketsData->id}}"></iframe>
</div>
<section class="sec-category">
    <div class="row">
        <div class="col-md-6">
            <p>You have completed <strong>{{ $professionAttemptedCount }} of {{count($basketsData->profession)}} </strong> careers</p>
        </div>
        <div class="col-md-6">
            <div class="pull-right">
                <ul class="match-list">
                    <li><span class="number match-strong">{{ (isset($matchScaleCount['match']) && count($matchScaleCount['match']) > 0 ) ? count($matchScaleCount['match']) : 0 }}</span> Strong match</li>
                    <li><span class="number match-potential">{{ (isset($matchScaleCount['moderate']) && count($matchScaleCount['moderate']) > 0 ) ? count($matchScaleCount['moderate']) : 0 }}</span> Potential match</li>
                    <li><span class="number match-unlikely">{{ (isset($matchScaleCount['nomatch']) && count($matchScaleCount['nomatch']) > 0 ) ? count($matchScaleCount['nomatch']) : 0 }}</span> Unlikely match</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="career-map">
        <div class="row">
            @if(isset($basketsData->profession) && count($basketsData->profession) > 0)
                @foreach($basketsData->profession as $k => $v)
                    <?php
                        $average_per_year_salary = $v->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'average_per_year_salary';
                            })->first();
                        $profession_outlook = $v->professionHeaders->filter(function($item) {
                                return $item->pfic_title == 'profession_outlook';
                            })->first();
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>
                        
                        <div class="category {{$matchScale}}">
                            <a href="{{url('teenager/career-detail')}}/{{$v->pf_slug}}" title="{{ $v->pf_name }}">{{ $v->pf_name }}</a>
                            @if(isset($v->attempted))
                                <span class="complete">
                                    <a href="#" title="Completed"><i class="icon-thumb"></i></a>
                                </span>
                            @endif
                            <div class="overlay">
                                @if(isset($average_per_year_salary))
                                    <span class="salary">Average Salary per year : {!! ($countryId == 1) ? "<i class='fa fa-inr'></i>" : "<i class='fa fa-dollar'></i>" !!}
                                        {{ (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? strip_tags($average_per_year_salary->pfic_content) : '' }}
                                    </span>
                                @else
                                    <span class="salary">Average Salary per year : N/A</span>
                                @endif
                            
                                @if(isset($profession_outlook))
                                    <span class="assessment">Outlook : 
                                        {{ (isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content)) ? strip_tags($profession_outlook->pfic_content) : '' }}
                                    </span>
                                @else
                                    <span class="assessment">Outlook : N/A</span>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            @else
                <li class="match-strong complete-feild">
                    <p>No any professions found!</p>
                </li>
            @endif
        </div>
    </div>
</section>
@else
<div class="panel-body">
    <div class="related-careers careers-tag">
        <div class="career-heading clearfix">
            <div class="row">
                <div class="col-md-6">
                    <p>No records found!</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif