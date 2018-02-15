<div class="panel-heading">
    <h4 class="panel-title">
        <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{$section}}" class="collapsed career-cl" id="{{$section}}" aria-expanded="true" @if(isset($activities[0]) && count($activities[0]) > 0) onclick="fetch2ActivityQuestion(this.id)" @endif>
            Profile Builder {{$section}}<span id="percentageSection{{$section}}">{{$sectionPercentage}}</span>
        </a>
    </h4>
</div>
<div class="panel-collapse collapse in" id="accordion{{$section}}">
    <div class="panel-body" id="section{{$section}}">
        @if(isset($activities) && !empty($activities))
            <div class="quiz_view">
                <div class="clearfix time_noti_view">
                    <span class="time_type pull-left">
                        <i class="icon-alarm"></i>
                        <span class="time-tag" id="blackhole"></span>
                    </span>
                    <span class="sec-popup help_noti">
                        <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                    </span>
                    <div class="hide popoverContent">
                        
                    </div>
                </div>
                <div class="quiz-que">
                    <p class="que">
                        <i class="icon-arrow-simple"></i> {!! $activities[0]->l2ac_text !!}
                    </p>
                    <div class="quiz-ans">
                        @if ($activities[0]->l2ac_image)
                            @if ($activities[0]->l2ac_image != '')
                                <div class="question-img">
                                    <img src="{{Storage::url($level2ActivityOriginalImageUploadPath.$activities[0]->l2ac_image)}}" title="Click to enlarge image" class="pop-me" />
                                </div>
                            @endif
                        @endif
                        <div class="radio">
                            @foreach($activities[0]->options as $key => $value)
                                <label>
                                    <input type="radio" name="{{$activities[0]->activityID}}l2AnsId" onclick="saveAns('{{$activities[0]->activityID}}', '{{$key}}')" value="{{$value['optionId']}}" />
                                    <span class="checker"></span>
                                    <em>{{$value['optionText']}}</em>
                                </label>
                            @endforeach
                            <input type="hidden" id="{{$activities[0]->activityID}}l2AnsSection" value="{{$section}}">
                        </div>
                        <div class="clearfix"><span class="next-que pull-right"><i class="icon-hand"></i></span></div>
                    </div>
                </div>
            </div>
        @else
            <center><h3>You have successfully completed this Quiz</h3></center>
        @endif
    </div>
</div>
<script type="text/javascript">
    var count = '{{$timer}}';
    var isSectionCompleted = '{{$isSectionCompleted}}';
</script>