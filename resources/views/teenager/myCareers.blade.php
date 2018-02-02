@extends('layouts.teenager-master')

@push('script-header')
    <title>My Careers</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="container">
            <div class="careers-container">
                <div class="top-heading text-center">
                    <h1>my careers</h1>
                    <p>You have completed <strong>{{$teenagerTotalProfessionAttemptedCount}} of {{$teenagerTotalProfessionStarRatedCount}}</strong> careers from your shortlist</p>
                </div>
                <div class="sec-filter">    
                    <div class="row">
                        <div class="col-md-2 text-right">
                            <span>Filter by:</span>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select">
                                <select tabindex="8" class="form-control">
                                  <option value="all categories">all categories</option>
                                  <option value="Strong match">Strong match</option>
                                  <option value="Potential match">Potential match</option>
                                  <option value="Unlikely match">Unlikely match</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select bg-blue">
                                <select tabindex="8" class="form-control">
                                      <option value="all careers">all careers</option>
                                      <option value="agriculture">agriculture</option>
                                      <option value="conservation">conservation</option>
                                      <option value="Veterinarians">Veterinarians</option>
                                                                     </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group search-bar clearfix">
                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild" id="search">
                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- category list-->
                <div id="maindiv">
                    @if(count($basketsData)>0)
                        <?php 
                            $getTeenagerHML = Helpers::getTeenagerMatchScale(Auth::guard('teenager')->user()->id);
                            $professionAttemptedCount = 0;
                            $matchScaleCount = [];
                        ?>
                        @foreach($basketsData as $key => $value)
                        <?php $matchScaleCount = []; ?>
                            <section class="sec-category">
                                <h2>{{$value->b_name}}</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                            $professionAttemptedCount = 0;
                                            foreach($value->profession as $k => $v){
                                                if(count($v->professionAttempted)>0){
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
                                        <p>You have completed <strong>{{$professionAttemptedCount}} of {{count($value->profession)}}</strong> careers</p>
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
                                                                <a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                                            </figcaption>
                                                            @if(count($v->professionAttempted)>0)
                                                                <span class="complete">Complete</span>
                                                            @endif
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
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
<script type="text/javascript">
    $(function() {

        $('#search').keyup(function ()  {
            if($("#search").val().length > 3) {      
                $('.iframe').attr('src', '');          
                $("#maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
                $("#maindiv").addClass('loading-screen-parent');
                var value = $("#search").val();
                var CSRF_TOKEN = "{{ csrf_token() }}";
                $.ajax({
                    type: 'POST',
                    url: "{{url('teenager/get-my-careers-search')}}",
                    dataType: 'html',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: {'search_text':value},
                    success: function (response) {
                        $("#maindiv").html(response);
                        $("#maindiv").addClass("dataLoaded");
                        $("#maindiv").removeClass('loading-screen-parent');
                    }
                });
            }
        });
    });
</script>
@stop