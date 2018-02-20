@if(isset($level1Activities[0]))
<div class="opinion-questionnaire">
    <p class="que-sec">{{$level1Activities[0]->l1ac_text}}</p>
    <div class="opinion-ans opinion-ans-functional">
        <div class="row">
            <?php
                $dasignSetArray = ['7', '2', '6', '7', '2', '6'];
            ?>
            @foreach($level1Activities[0]->options as $key => $option)
                <div class="col-xs-4">
                    <input type="radio" class="radio_item" value="{{$option['optionId']}}" name="option" id="option{{$option['optionId']}}" onchange="saveAnswer({{$option['optionId']}}, {{$level1Activities[0]->activityID}}, {{$key}})">
                    <label class="label_item" for="radio{{$option['optionId']}}">
                        <img class="emojis-icon-{{ ( isset($dasignSetArray[$key]) ) ? $dasignSetArray[$key] : '4' }}" alt="{{$option['optionText']}}" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG4AAAB6AQMAAABk0vQ1AAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGAXYAAAcmAAFZQcRjAAAAAElFTkSuQmCC"> 
                        <span onClick="saveAnswer({{$option['optionId']}}, {{$level1Activities[0]->activityID}}), {{$key}}">{{$option['optionText']}}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
<?php
    $firstLevelAnswerTrend = Helpers::calculateTrendForLevel1($level1Activities[0]->activityID,1);
    $totalTrend = Helpers::calculateTotalTrendForLevel1($level1Activities[0]->activityID,1);
?>
<div class="opinion-result" style="display:none;">
    <div class="row">
        @foreach($firstLevelAnswerTrend as $trend)
        <div class="col-sm-3 col-xs-4">
            <div class="progress progress-bar-vertical">
                <div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="height: {{round($trend['percentage'])}}%;">
                    <span class="sr-only">{{round($trend['percentage'])}}%</span>
                </div>
            </div>
            <span class="bottom">{{$trend['label']}}</span>
        </div>
        @endforeach
        <div class="col-sm-3 col-xs-12">
            <div class="top-status clearfix">
                <div class="left-selection-box">
                    Total Votes<br> {{$totalTrend}}
                </div>
            </div>
        </div>
    </div>
</div>
@endif