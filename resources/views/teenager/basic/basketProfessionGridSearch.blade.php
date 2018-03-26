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
                                <div class="category-list career-listing">
                                    <div class="row">
                                        @foreach($value->profession as $k => $v)                                            
                                            <div class="col-md-4 col-sm-6">
                                                <?php $matchScale = ( isset($v->match_scale) && $v->match_scale != '') ? $v->match_scale : "career-data-nomatch"; ?>            
                                                    <?php $alias = ' "Also called: '.$v->pf_profession_alias.""; ?>

                                                    <a href="{{url('teenager/career-detail')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}{{($v->pf_profession_alias && $v->pf_profession_alias != '')?$alias.'"':''}}" class="category-block {{$matchScale}}">
                                                    <figure>
                                                        <div class="category-img" style="background-image: url('{{Storage::url(Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH').$v->pf_logo)}}')"></div>
                                                        <figcaption>
                                                           {{$v->pf_name}}
                                                        </figcaption>
                                                        @if(isset($v->attempted) && $v->attempted == 1)
                                                            <span class="complete">
                                                                <a href="#" title="Completed"><i class="icon-thumb"></i></a>
                                                            </span>
                                                        @endif                                         
                                                    </figure>
                                                    </a>    
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