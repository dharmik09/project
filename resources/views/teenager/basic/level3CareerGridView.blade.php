@if(count($basketsData)>0)
<?php
    $getTeenagerHML = Helpers::getTeenagerMatchScale(Auth::guard('teenager')->user()->id);
    $professionAttemptedCount = 0;
    $matchScaleCount = [];
?>  
    @foreach($basketsData as $key => $value)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" class="collapsed">{{$value->b_name}}</a> <a href="{{url('teenager/list-career')}}" title="Grid view" class="grid"><i class="icon-list"></i></a></h4>
            </div>
            <div class="panel-collapse collapse <?php if($key == 0){echo 'in';  $firstId = $value->id;} ?>" id="accordion{{$value->id}}">
                <div class="panel-body">
                    <section class="career-content">
                        <div class="bg-white">
                            <div id="profession{{$value->id}}">
                                @if(isset($view))
                                    <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$value->b_logo)}});">
                                        <div class="">
                                            <div class="play-icon"><a id="link{{$value->id}}" onclick="playVideo(this.id,'{{Helpers::youtube_id_from_url($value->b_video)}}');" class="play-btn" id="iframe-video"><img src="{{Storage::url('img/play-icon.png')}}" alt="play icon"></a></div>
                                        </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$value->id}}"></iframe>
                                    </div>
                                @endif
                                <section class="sec-category">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(isset($view))
                                                <?php
                                                    $professionAttemptedCount = 0;
                                                    foreach($value->profession as $k => $v){
                                                        // if(count($v->professionAttempted)>0){
                                                        //     $professionAttemptedCount++;
                                                        // }
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
                                                        $professionAttempted = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id);
                                                        if(isset($professionAttempted) && $professionAttempted == 100){
                                                            $professionAttemptedCount++;
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
                                            @if (!Request::ajax())
                                            <div class="pull-right">
                                                <ul class="match-list">
                                                    <li><span class="number match-strong">{{ (isset($matchScaleCount['match']) && count($matchScaleCount['match']) > 0 ) ? count($matchScaleCount['match']) : 0 }}</span> Strong match</li>
                                                    <li><span class="number match-potential">{{ (isset($matchScaleCount['moderate']) && count($matchScaleCount['moderate']) > 0 ) ? count($matchScaleCount['moderate']) : 0 }}</span> Potential match</li>
                                                    <li><span class="number match-unlikely">{{ (isset($matchScaleCount['nomatch']) && count($matchScaleCount['nomatch']) > 0 ) ? count($matchScaleCount['nomatch']) : 0 }}</span> Unlikely match</li>
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="career-map">
                                        <div class="row">

                                            @foreach($value->profession as $k => $v)
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
                                                    <a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                                    <?php 
                                                        $professionComplete = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id); ?>
                                                    @if(isset($professionComplete) && $professionComplete == 100)
                                                    <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>
                                                    @endif

                                                    <div class="overlay">
                                                        @if(isset($average_per_year_salary))
                                                            <span class="salary">Average Salary per year : 
                                                                @if($countryId == 1)
                                                                    ₹
                                                                @elseif($countryId == 2)
                                                                    $
                                                                @endif
                                                                <?php echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? strip_tags($average_per_year_salary->pfic_content) : ''?>
                                                            </span>
                                                        @else
                                                            <span class="salary">Average Salary per year : N/A</span>
                                                        @endif

                                                        @if(isset($profession_outlook))
                                                            <span class="assessment">Outlook : 
                                                                <?php echo (isset($profession_outlook->pfic_content) && !empty($profession_outlook->pfic_content)) ? strip_tags($profession_outlook->pfic_content) : '' ?>
                                                            </span>
                                                        @else
                                                            <span class="assessment">Outlook : N/A</span>
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif