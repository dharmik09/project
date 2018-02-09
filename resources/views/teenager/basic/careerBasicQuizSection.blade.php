@if( isset($response['data']) && !empty($response['data']) )
    <div id="basicErrorGoneMsg"></div>
    <div class="quiz_view">
        <div class="loading-screen loading-wrapper-sub basic-question-loader" style="display:none;">
            <div class="loading-text">
                <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
            </div>
            <div class="loading-content"></div>
        </div>
        <div class="clearfix time_noti_view">
            <span class="time_type pull-left">
                <i class="icon-alarm"></i>
                <span class="basic-time-tag">0:0</span>
            </span>
            <span class="help_noti pull-right">
                <span class="pull-right close">
                    <i class="icon-close"></i>
                </span>
            </span>
        </div>
        <form id = "level4_activity_ans" role = "form" enctype = "multipart/form-data" method = "POST" autocomplete = "off" autocorrect = "off" autocapitalize = "off" spellcheck = "false">
            <input type = "hidden" name = "_token" value = "{{ csrf_token() }}">
            <input type = "hidden" id = "questionID" name = "questionID" value = "{{$response['data']->activityID}}" >
            <input type ="hidden" id="blackhole" name="timer" />
            <div class="quiz-que">
                <p class="que">
                    <i class="icon-arrow-simple"></i>{!! $response['data']->question_text !!}
                </p>
                <div class="quiz-ans">
                    @if($response['data']->totalCorrectOptions > 1)
                        <p class="multiple-select">(You can select multiple answers for this question)</p>
                    @endif
                    <div class="{{ ($response['data']->totalCorrectOptions > 1) ? 'checkbox' : 'radio' }} box optionSelection">
                        <?php 
                            if($response['data']->options && !empty($response['data']->options)) {
                                foreach ($response['data']->options as $key => $value) {
                                    if ($response['data']->type == 1) {
                                        $optionType = "radio";
                                        $optionName = "answerID[0]";
                                    } else if ($response['data']->type == 0) {
                                        if ($response['data']->totalCorrectOptions > 1) {
                                            $optionType = "checkbox";
                                            $optionName = "answerID[]";
                                        } else {
                                            $optionType = "radio";
                                            $optionName = "answerID[0]";
                                        }
                                    } else {
                                        $optionType = "radio";
                                        $optionName = "answerID[0]";
                                    }
                                    ?>
                                    <label class="{{$optionType}} class{{$value['optionId']}}">
                                        <input type="{{$optionType}}" id="check{{$value['optionId']}}" name="{{$optionName}}" value="{{$value['optionId']}}" class="selectionCheck multiCast"/>
                                        <span class="checker"></span>
                                        <em>{!! $value['optionText'] !!}</em>
                                    </label>
                            <?php 
                                }
                            } else {
                                echo "<br/><p><strong>No Any Options For This Question.</strong></p>";
                            }
                        ?>
                    </div>
                    
                    <div class="clearfix">
                        <a href="javascript:void(0);" class="next-que pull-right saveMe" onClick="saveBasicAnswer();">
                            <i class="icon-hand"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@else
    @if( isset($response['basicCompleted']) && $response['basicCompleted'] == 1 )
        <div class="quiz_view">
            <div class="clearfix time_noti_view">
                <span class="help_noti pull-right">
                    <span class="pull-right close">
                        <i class="icon-close"></i>
                    </span>
                </span>
            </div>
            <div class="cong-block">
                <div class="row">
                    <div class="col-xs-4">
                        <span class="emojis-img"><img  alt="Congratulations" src="{{Storage::url('img/Original-image/icon-4.png')}}"></span>
                        <span class="emojis-img emojis-icon"><i class="icon-thumb"></i></span>
                    </div>
                    <div class="col-xs-8">
                        <h2>Congratulations!</h2>
                        <p>{{ ucwords($response['teenagerName']) }} ! You are now a rookie ProTeen {{$response['professionName']}}.</p>
                        <!-- <p><strong><span class="font-blue">Your Score : </span> 2500</strong></p> -->
                    </div>
                </div>
            </div>
            
        </div>
    @else
        <div class="quiz_view">
            <div class="clearfix time_noti_view">
                <span class="help_noti pull-right">
                    <span class="pull-right close">
                        <i class="icon-close"></i>
                    </span>
                </span>
            </div>
            <div class="quiz-que">
                No Questions found!
            </div>
        </div>
    @endif
@endif
<script type="text/javascript">
    var basicCount = {{ (isset($response['timer']) && $response['timer'] != "") ? $response['timer'] : 0 }};
    var optionType = '{{ (isset($optionType) && $optionType != '') ? $optionType : 0 }}';
    var optionName = '{{ (isset($optionName) && $optionName != '') ? $optionName : "radio" }}';
    var limitSelect = {{ (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 }};
</script>