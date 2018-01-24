@if(count($basketsData)>0)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$basketsData->id}}" id="{{$basketsData->id}}" class="collapsed">{{$basketsData->b_name}}</a> <a href="{{url('teenager/career-grid')}}" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
        </div>
        <div class="panel-collapse collapse in" id="accordion{{$basketsData->id}}">
            <div id="profession{{$basketsData->id}}">
                <div class="panel-body">
                    <div class="banner-landing banner-career" style="background-image:url({{Storage::url(Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH').$basketsData->b_logo)}});">
                        <div class="">
                            <div class="play-icon"><a id="link{{$basketsData->id}}" onclick="playVideo(this.id,'{{Helpers::youtube_id_from_url($basketsData->b_video)}}');" class="play-btn" id="iframe-video"><img src="{{Storage::url('img/play-icon.png')}}" alt="play icon"></a></div>
                        </div><iframe width="100%" height="100%" frameborder="0" allowfullscreen class="iframe" id="iframe-video-link{{$basketsData->id}}"></iframe>
                    </div>
                    <div class="related-careers careers-tag">
                        <div class="career-heading clearfix">
                            <div class="row">
                                <div class="col-md-6">
                                    @if(isset($view))
                                        <?php
                                            $professionAttemptedCount = 0;
                                            foreach($basketsData->profession as $k => $v){
                                                if(count($v->professionAttempted)>0){
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
                                                        {{count($basketsData->profession)}}
                                                    @endif
                                                </strong>
                                            careers
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="pull-right">
                                        <ul class="match-list">
                                            <li><span class="number match-strong">4</span> Strong match</li>
                                            <li><span class="number match-potential">5</span> Potential match</li>
                                            <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="career-list">

                    @foreach($basketsData->profession as $k => $v)
                        <li class="match-strong complete-feild"><a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                            @if(count($v->professionAttempted)>0)
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
@else
    <center><h3>No result Found</h3></center>
@endif