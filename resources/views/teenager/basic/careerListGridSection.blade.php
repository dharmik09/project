<div class="panel-body">
<!-- List Layout -->
<div id="list-layout-{{$basket->id}}" class="related-careers careers-tag">
    <div class="career-heading clearfix">
        @if ($industryImageShow == 1)
        <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH')) }}{{ $basket->b_logo }} )">
            <div class="">
                <div class="play-icon">
                    <a id="link{{$basket->id}}" href="javascript:void(0);" class="play-btn" onclick="playVideo(this.id, '{{Helpers::youtube_id_from_url($basket->b_video)}}')">
                        <img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon">
                    </a>
                </div>
            </div>
            <iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$basket->id}}"></iframe></div>
        @endif
        @if ($showElement == 1)
        <div class="row">
                <div class="col-md-6">
                    <p><strong>{{$basket->professionAttemptedCount}}</strong> of <strong>{{count($basket->profession)}}</strong> Career Role Plays Fully Completed
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <ul class="match-list">
                            <li><span class="number match-strong">{{ (isset($basket->match) && count($basket->match) > 0 ) ? count($basket->match) : 0 }}</span> Strong match</li>
                            <li><span class="number match-potential">{{ (isset($basket->moderate) && count($basket->moderate) > 0 ) ? count($basket->moderate) : 0 }}</span> Potential match</li>
                            <li><span class="number match-unlikely">{{ (isset($basket->nomatch) && count($basket->nomatch) > 0 ) ? count($basket->nomatch) : 0 }}</span> Unlikely match</li>
                        </ul>
                    </div>
                </div>
        </div>
        @endif
    </div>
    <ul class="career-list">
        @if (in_array($basket->id, $shownBasketId))
        @foreach ($basket->profession as $profession)
            <?php $matchScale = ( isset($profession->match_scale) && $profession->match_scale != '') ? $profession->match_scale : "career-data-nomatch"; ?>
            <li class="{{ $matchScale }} complete-feild"><a href="{{ url('teenager/career-detail') }}/{{$profession->pf_slug}}" title="{{$profession->pf_name}}">{{$profession->pf_name}}</a>
                @if($profession->attempted == 1)
                <a href="#" class="complete">
                    <span>Complete <i class="icon-thumb"></i></span>
                </a>
                @endif
            </li>
        @endforeach
        @endif
    </ul>
</div>
<!-- Grid Layout -->
<section id="grid-layout-{{$basket->id}}" class="career-content" style="display: none;">
    <div class="bg-white">
        @if ($industryImageShow == 1)
        <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH')) }}{{ $basket->b_logo }} )">
            <div class="">
                    <div class="play-icon">
                        <a id="link{{$basket->id}}" href="javascript:void(0);" class="play-btn" onclick="playVideo(this.id, '{{Helpers::youtube_id_from_url($basket->b_video)}}')">
                            <img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon">
                        </a>
                    </div>
                </div>
                <iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$basket->id}}"></iframe>
        </div>
        @endif
        <section class="sec-category">
            @if ($showElement == 1)
            <div class="row">
                <div class="col-md-6">
                    <p>You have completed <strong>{{$basket->professionAttemptedCount}} of {{count($basket->profession)}}</strong> careers</p>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <ul class="match-list">
                            <li><span class="number match-strong">{{ (isset($basket->match) && count($basket->match) > 0 ) ? count($basket->match) : 0 }}</span> Strong match</li>
                            <li><span class="number match-potential">{{ (isset($basket->moderate) && count($basket->moderate) > 0 ) ? count($basket->moderate) : 0 }}</span> Potential match</li>
                            <li><span class="number match-unlikely">{{ (isset($basket->nomatch) && count($basket->nomatch) > 0 ) ? count($basket->nomatch) : 0 }}</span> Unlikely match</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
            <div class="category-list career-listing">
                <div class="row flex-container">
                    @if (in_array($basket->id, $shownBasketId))
                    @foreach ($basket->profession as $profession)
                    <div class="col-md-4 col-sm-6 flex-items">
                        <?php $matchGridScale = ( isset($profession->match_scale) && $profession->match_scale != '') ? $profession->match_scale : "career-data-nomatch"; ?>
                        <a href="{{ url('teenager/career-detail') }}/{{$profession->pf_slug}}" title="{{$profession->pf_name}}" class="category-block {{ $matchGridScale }}">
                            <figure>
                                <div class="category-img" style="background-image: url('{{Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$profession->pf_logo)}}"></div>
                                <figcaption>
                                    {{$profession->pf_name}}
                                </figcaption>
                                @if($profession->attempted == 1)
                                    <!-- <a href="#" class="complete">
                                        <span>Complete <i class="icon-thumb"></i></span>
                                    </a> -->
                                    <span class="complete">Complete</span>
                                @endif
                            </figure>
                        </a>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </section>
    </div>
</section>
</div>