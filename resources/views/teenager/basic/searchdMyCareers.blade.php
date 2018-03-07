@if(count($basketsData)>0)
    <?php 
        $getTeenagerHML = Helpers::getTeenagerMatchScale(Auth::guard('teenager')->user()->id);
        $professionAttemptedCount = 0;
        
    ?>
    @foreach($basketsData as $key => $value)
        <?php $matchScaleCount = []; ?>
        <section class="sec-category">
            <h2>{{$value->b_name}}</h2>
            <div class="row">
                <div class="col-md-6">
                    <?php
                        // $basketTotalProfession = 0;
                        // $professionAttemptedCount = 0;
                        foreach($value->profession as $k => $v){
                            
                            //Check if profession attempted or not
                            // $professionComplete = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id);
                            // if (count($v->starRatedProfession) > 0) {
                            //     if(isset($professionComplete) && $professionComplete == 100){
                            //         $professionAttemptedCount++;
                            //     }
                            //     $basketTotalProfession++;
                            // }
                            if (count($v->starRatedProfession) > 0) {
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
                        }
                    ?>
                    <?php
                        $professionAttemptedCount = Helpers::getProfessionCompleteCount(Auth::guard('teenager')->user()->id, 1, $value->id);
                        $basketTotalProfession = Helpers::getTotalBasketProfession($value->id);
                    ?>
                    <p>You have completed <strong>{{(isset($professionAttemptedCount)) ? $professionAttemptedCount : 0 }} of {{(isset($basketTotalProfession)) ? $basketTotalProfession : 0 }}</strong> careers</p>
                </div>
                @if(!Request::ajax())
                <div class="col-md-6">
                    <div class="pull-right">
                        <ul class="match-list">
                            <li><span class="number match-strong">{{ (isset($matchScaleCount['match']) && count($matchScaleCount['match']) > 0 ) ? count($matchScaleCount['match']) : 0 }}</span> Strong match</li>
                            <li><span class="number match-potential">{{ (isset($matchScaleCount['moderate']) && count($matchScaleCount['moderate']) > 0 ) ? count($matchScaleCount['moderate']) : 0 }}</span> Potential match</li>
                            <li><span class="number match-unlikely">{{ (isset($matchScaleCount['nomatch']) && count($matchScaleCount['nomatch']) > 0 ) ? count($matchScaleCount['nomatch']) : 0 }}</span> Unlikely match</li>
                        </ul>
                    </div>
                </div>
                @endif
            </div>
            <div class="category-list">
                <div class="row">
                    @foreach($value->profession as $k => $v)
                        @if(count($v->starRatedProfession)>0)
                            <div class="col-sm-6">
                                <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>
                                <div class="category-block {{$matchScale}}">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url($professionImagePath.$v->pf_logo) }} ')"></div>
                                        <figcaption>
                                            <a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{ str_limit($v->pf_name, $limit = 35, $end = '...') }}</a>
                                        </figcaption>
                                        <?php
                                            $professionAttempted = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $v->id);
                                            if(isset($professionAttempted) && $professionAttempted == 100) { ?>
                                            <span class="complete">Complete</span>
                                            <?php } ?>
                                    </figure>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif