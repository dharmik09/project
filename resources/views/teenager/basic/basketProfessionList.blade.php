@if(isset($basketsData) && !empty($basketsData))
<div class="panel-body">
    <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH'))}}{{$basketsData->b_logo}})">
        <div class="">
            <div class="play-icon">
                <a id="link{{$basketsData->id}}" onclick="playVideo(this.id, '{{Helpers::youtube_id_from_url($basketsData->b_video)}}')" class="play-btn" id="iframe-video">
                    <img src="{{Storage::url('img/play-icon.png')}}" alt="play icon">
                </a>
            </div>
        </div>
        <iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$basketsData->id}}"></iframe>
    </div>
    <div class="related-careers careers-tag">
        <div class="career-heading clearfix">
            <div class="row">
                <div class="col-md-6">
                    <p>You have completed <strong>{{$professionAttemptedCount}} of {{count($basketsData->profession)}}</strong> careers</p>
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
        </div>
        <ul class="career-list">
            @if(isset($basketsData->profession) && count($basketsData->profession) > 0)
                @forelse($basketsData->profession as $k => $v)
                    <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>
                    <li class="{{$matchScale}} complete-feild">
                        <a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                        @if(isset($v->attempted))
                            <a class="complete"><span>Complete <i class="icon-thumb"></i></span></a>
                        @endif
                    </li>
                @empty
                    <li class="match-strong complete-feild">
                        <p>No any professions found!</p>
                    </li>
                @endforelse
            @else
                <li class="match-strong complete-feild">
                    <p>No any professions found!</p>
                </li>
            @endif
        </ul>
    </div>
</div>
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