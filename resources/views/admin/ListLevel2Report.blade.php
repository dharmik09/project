@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Level2 Report
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="box box-info">
        <form action="{{url('admin/level2Chart/')}}" class="form-horizontal" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
             <div class="box-body">
                 <div class="form-group">
                     <label for="question" class="col-sm-2 control-label">{{trans('labels.selectquestion')}}</label>
                    <div class="col-md-9">
                        <select name="questionId"  class="form-control chosen-select-width"  onchange="loadGraph(this.value)">
                            <option value="0">All Questions</option>
                            <?php
                            foreach ($level2 as $key => $level) {
                                ?>
                                <option text-align="left" value="{{$level->id}}" <?php
                                if (isset($id) && $id == $level->id) {
                                    echo "selected='selected'";
                                }
                                ?> > {{$level->l2ac_text}}</option>
                                    <?php }
                                    ?>
                        </select>
                    </div>
                 </div>
                 @include('admin/ChartType')
                 @include('admin/GenderType')
                 @include('admin/AgeType')
                 <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-3">
                        <button id="search" type="submit" class="btn btn-primary btn-flat">{{trans('labels.search')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>

    <?php
        if(isset($allQuestion) && !empty($allQuestion)){
        foreach($allQuestion as $key=>$val){ ?>
            <div class="row">
        <div class="box box-info">
            <div class="box-body">
                <div class="col-md-12">
                    <div id="highchart_option_<?php echo $key ?>">Chart Loads here...</div>
                </div>
            </div>
        </div>
    </div>
            
        <?php   }}else{
    ?>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_option_0">Chart Loads here...</div>  
            </div>
            </div>
        </div>
    </div>
        <?php } ?>
</section>    

@stop
@section('script')
<script src="{{ asset('backend/js/highchart.js')}}"></script>
<script src="{{ asset('backend/js/chosen.jquery.js')}}"></script>
<script src="{{ asset('backend/js/report.js')}}"></script>

<script type="text/javascript">
var config = {
    '.chosen-select': {},
    '.chosen-select-deselect': {allow_single_deselect: true},
    '.chosen-select-no-single': {disable_search_threshold: 10},
    '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
    '.chosen-select-width': {width: "85%"},
}
for (var selector in config) {
    $(selector).chosen(config[selector]);
}   

    <?php 
        if(isset($id) && $id == 0){
            $finallevel2 = [];
            foreach($allQuestion as $key=>$val){
                $level2 = [];
                foreach ($val['level2data'] as $option => $value) {
                    $level2[] = array('y' => $value, 'name' => $option);
                }
                $finallevel2 = json_encode($level2);
    ?>
            var questionText = '<?php echo json_encode($val['text']) ?>';
            var total = <?php echo $val['total'] ?>;
            var key = <?php echo $key ?>;
            var chartType = '<?php echo $chart ?>';

            var finallevel2 = <?php echo $finallevel2; ?>;
            var subtitle = '<strong>Total VOTES</strong>:'+total;
            // Level1 Chart  
            Level1And2Report(questionText, total, chartType, finallevel2,key,subtitle);
        <?php    }}else{
    ?>
        
    var finallevel2 = <?php echo $finalLevel2; ?>;
    var questionText = '{{$questionText}}';
    var total = '{{$total}}';
    var chartType = '{{$chart}}';
    var key = 0;
    var subtitle = '<strong>Total VOTES</strong>:'+total;
    Level1And2Report(decodeEntities(questionText),total,chartType,finallevel2,key,subtitle);
    <?php } ?>

    function decodeEntities(encodedString) {
        var textArea = document.createElement('textarea');
        textArea.innerHTML = encodedString;
        return textArea.value;
    }

</script>

@stop    

