@if( isset($response['data']) && !empty($response['data']) )
<div class="quiz_view">
    <div class="clearfix time_noti_view">
        <span class="time_type pull-left">
            <i class="icon-alarm"></i>
            <span class="basic-time-tag">0:0</span>
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
            @if($response['data']->totalCorrectOptions >= 1)
                <span style="color:#66c6e6">(You can select multiple answers for this question)</span><br/>
            @endif
            <br/>
            <div class="quiz-ans">
                <div class="{{ ($response['data']->totalCorrectOptions > 1) ? 'checkbox' : 'radio' }} box">
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
                                <label class="">
                                    <input type="{{$optionType}}" id="check{{$value['optionId']}}" name="{{$optionName}}" value="{{$value['optionId']}}" />
                                    <span class="checker"></span>
                                    <em>{!! $value['optionText'] !!}</em>
                                </label>
                        <?php 
                            }
                        } else {
                            echo "<label class=''><em>No Any Options For This Question.</em></label>";
                        }
                    ?>
                </div>
                
                <div class="clearfix">
                    <a href="#" class="next-que pull-right">
                        <i class="icon-hand"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<span title="Play" class="btn-play btn btn-basic">Play</span>
@else
<div class="quiz_view">
    <div class="quiz-que">
        Quiz completed!
    </div>
</div>
@endif
<script type="text/javascript">
    var basicCount = {{ (isset($response['timer']) && $response['timer'] != "") ? $response['timer'] : 0 }};
</script>