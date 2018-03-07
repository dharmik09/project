@if(isset($basketsData) && count($basketsData)>0)
    @foreach ($basketsData as $key => $value)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" class="collapsed">{{$value->b_name}}</a> 
                    <a href="{{url('teenager/career-grid')}}" title="Grid view" class="grid"><i class="icon-grid"></i></a>
                </h4>
            </div>
            <div class="panel-collapse collapse in" id="accordion{{$value->id}}">
                <div id="profession{{$value->id}}">
                    <div class="panel-body">
                        <div class="related-careers careers-tag">
                            <div class="career-heading clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                        @if (!Request::ajax())
                                        <div class="pull-right">
                                            <ul class="match-list">
                                                <li><span class="number match-strong">{{ (isset($matchScaleCount[$key]['match']) && count($matchScaleCount[$key]['match']) > 0 ) ? count($matchScaleCount[$key]['match']) : 0 }}</span> Strong match</li>
                                                <li><span class="number match-potential">{{ (isset($matchScaleCount[$key]['moderate']) && count($matchScaleCount[$key]['moderate']) > 0 ) ? count($matchScaleCount[$key]['moderate']) : 0 }}</span> Potential match</li>
                                                <li><span class="number match-unlikely">{{ (isset($matchScaleCount[$key]['nomatch']) && count($matchScaleCount[$key]['nomatch']) > 0 ) ? count($matchScaleCount[$key]['nomatch']) : 0 }}</span> Unlikely match</li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <ul class="career-list">

                                @foreach($value->profession as $k => $v)
                                    <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>
                                    <li class="{{$matchScale}} complete-feild">
                                        <a href="{{url('teenager/career-detail')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                        @if(isset($v->attempted) && $v->attempted != '')
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