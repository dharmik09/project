@if(count($basketsData)>0)
<?php
    $getTeenagerHML = Helpers::getTeenagerMatchScale(Auth::guard('teenager')->user()->id);
    $professionAttemptedCount = 0;
    $matchScaleCount = [];
?>  
    @foreach($basketsData as $key => $value)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" class="collapsed">{{$value->b_name}}</a> <a href="{{url('teenager/career-grid')}}" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
            </div>
            <div class="panel-collapse collapse <?php if($key == 0){echo 'in'; $firstId = $value->id;} ?>" id="accordion{{$value->id}}">
                <div id="profession{{$value->id}}">
                    <div class="panel-body">
                        @if(isset($view))
                            <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$value->b_logo)}});">
                                <div class="">
                                    <div class="play-icon"><a id="link{{$value->id}}" onclick="playVideo(this.id,'{{Helpers::youtube_id_from_url($value->b_video)}}');" class="play-btn" id="iframe-video"><img src="{{Storage::url('img/play-icon.png')}}" alt="play icon"></a></div>
                                </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$value->id}}"></iframe>
                            </div>
                        @endif
                        <div class="related-careers careers-tag">
                            <div class="career-heading clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if(isset($view))
                                            <?php
                                                $professionAttemptedCount = 0;
                                                foreach($value->profession as $k => $v) {
                                                    // if(count($v->professionAttempted) > 0) {
                                                    //     $professionAttemptedCount++;
                                                    // }
                                                    $professionAttempted = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id);
                                                    if(isset($professionAttempted) && $professionAttempted == 100){
                                                        $professionAttemptedCount++;
                                                    } 
                                                    $matchScale = isset($getTeenagerHML[$v->id]) ? $getTeenagerHML[$v->id] : '';
                                                    if($matchScale == "match") {
                                                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-strong";
                                                        $matchScaleCount['match'][] = $v->id;
                                                    } else if($matchScale == "nomatch") {
                                                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-unlikely";
                                                        $matchScaleCount['nomatch'][] = $v->id;
                                                    } else if($matchScale == "moderate") {
                                                        $basketsData[$key]['profession'][$k]['match_scale'] = "match-potential";
                                                        $matchScaleCount['moderate'][] = $v->id;
                                                    } else {
                                                        $basketsData[$key]['profession'][$k]['match_scale'] = "career-data-nomatch";
                                                    }
                                                }
                                            ?>
                                            <p>
                                                You have completed 
                                                    <strong>
                                                        {{$professionAttemptedCount}} of
                                                        @if(isset($totalProfessionCount)) 
                                                            {{$totalProfessionCount}}
                                                        @else
                                                            {{count($value->profession)}}
                                                        @endif
                                                    </strong>
                                                careers
                                            </p>
                                        @else
                                                <?php 
                                                    $matchScale = ( isset($value->profession[0]->id) && isset($getTeenagerHML[$value->profession[0]->id]) ) ? $getTeenagerHML[$value->profession[0]->id] : '';
                                                    if($matchScale == "match") {
                                                        $matchScaleNotView = "match-strong";
                                                        $value->profession[0]->match_scale = "match-strong";
                                                        $matchScaleCount['match'][] = $value->profession[0]->id;
                                                    } else if($matchScale == "nomatch") {
                                                        $matchScaleNotView = "match-unlikely";
                                                        $value->profession[0]->match_scale = "match-unlikely";
                                                        $matchScaleCount['nomatch'][] = $value->profession[0]->id;
                                                    } else if($matchScale == "moderate") {
                                                        $matchScaleNotView = "match-potential";
                                                        $value->profession[0]->match_scale = "match-potential";
                                                        $matchScaleCount['moderate'][] = $value->profession[0]->id;
                                                    } else {
                                                        $matchScaleNotView = "career-data-nomatch";
                                                    }
                                                ?>
                                            @endif
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

                        @foreach($value->profession as $k => $v)
                            <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>
                            <li class="{{$matchScale}} complete-feild"><a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                <?php 
                                    $professionComplete = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id); ?>
                                @if(isset($professionComplete) && $professionComplete == 100)
                                    <a class="complete"><span>Complete <i class="icon-thumb"></i></span></a>
                                @endif
                            </li>
                        @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif