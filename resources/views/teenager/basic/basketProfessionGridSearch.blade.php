@if(isset($basketsData) && count($basketsData) > 0)
    @foreach($basketsData as $key => $value)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" class="collapsed">{{$value->b_name}}</a>
                <a href="{{url('teenager/list-career')}}" title="Grid view" class="grid"><i class="icon-list"></i></a>
            </h4>
        </div>
        <div class="panel-collapse collapse in" id="accordion{{$value->id}}">
            <div class="panel-body">
                <section class="career-content">
                    <div class="bg-white">
                        <div id="profession{{$value->id}}">
                            <section class="sec-category">
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <ul class="match-list">
                                                <li><span class="number match-strong">{{ (isset($matchScaleCount[$key]['match']) && count($matchScaleCount[$key]['match']) > 0 ) ? count($matchScaleCount[$key]['match']) : 0 }}</span> Strong match</li>
                                                <li><span class="number match-potential">{{ (isset($matchScaleCount[$key]['moderate']) && count($matchScaleCount[$key]['moderate']) > 0 ) ? count($matchScaleCount[$key]['moderate']) : 0 }}</span> Potential match</li>
                                                <li><span class="number match-unlikely">{{ (isset($matchScaleCount[$key]['nomatch']) && count($matchScaleCount[$key]['nomatch']) > 0 ) ? count($matchScaleCount[$key]['nomatch']) : 0 }}</span> Unlikely match</li>
                                            </ul>
                                        </div>
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
                                                    <a href="{{url('teenager/career-detail')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
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