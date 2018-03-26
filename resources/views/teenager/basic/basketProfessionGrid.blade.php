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
    <div class="category-list career-listing">
        <div class="row">
            @if(isset($basketsData->profession) && count($basketsData->profession) > 0)
                @foreach($basketsData->profession as $k => $v)           
                    <div class="col-md-4 col-sm-6">
                        <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>            
                        <div class="category-block {{$matchScale}}">
                            <figure>
                                <div class="category-img" style="background-image: url('{{Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$v->pf_logo)}}')"></div>
                                <figcaption>
                                    <a href="{{url('teenager/career-detail')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                </figcaption>
                                @if(isset($v->attempted) && $v->attempted == 1)
                                    <span class="complete">
                                        <a href="#" title="Completed"><i class="icon-thumb"></i></a>
                                    </span>
                                @endif                                         
                            </figure>
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