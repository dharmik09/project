@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Level1 Report
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="box box-info">
            <form id="displayLevel1Report" class="form-horizontal" action="{{url('admin/level1Chart/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="question" class="col-sm-2 control-label">{{trans('labels.selectquestion')}}</label>
                        <div class="col-sm-6">
                            <select id="question" name="questionId" class="form-control chosen-select">
                                <option value="0">All Questions</option>
                                <?php
                                foreach ($level1Questions as $key => $level) {
                                    ?>
                                    <option text-align="left" value="{{$level->id}}" <?php
                                    if (isset($id) && $id == $level->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$level->l1ac_text}}</option>
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    @include('admin/ChartType')
                    @include('admin/GenderType')
                    @include('admin/AgeType')

                    <?php $gender = (isset($gender)) ? $gender : '';
                        if ($gender == 1) {
                            $gender = "Male";
                        } else if ($gender == 2) {
                            $gender = "Female";
                        }?>
                    <div class="form-group" style="display: none;">
                        <label for="gender" class="col-sm-2 control-label">Gender</label>
                        <div class="col-sm-2">
                            <select id="gender" name="gender" class="form-control" style="cursor:pointer;">
                                <option value="">Select</option>
                                <option <?php if ($gender == 1) {
                        echo 'selected="selected"';
                    } ?> value="1">Male</option>
                                <option <?php if ($gender == 2) {
                        echo 'selected="selected"';
                    } ?> value="2">Female</option>
                            </select>
                        </div>
                    </div>

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

        <?php }?>

</body>
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
        '.chosen-select-width': {width: "95%"},
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    <?php
        if(isset($id) && $id == 0){
            $finallevel1 = [];
            foreach($allQuestion as $key=>$val){
                $surveyicon1 = [];
                foreach ($val['trenddata'] as $option => $value) {
                    $surveyicon1[] = array('y' => $value, 'name' => $option);
                }
                $finallevel1 = json_encode($surveyicon1);
    ?>
            var questionText = '<?php echo $val['text'] ?>';
            var total = <?php echo $val['total'] ?>;
            var anstotal = <?php echo $val['anstotal'] ?>;
            var key = <?php echo $key ?>;
            var chartType = '<?php echo $chart ?>';
            var gender = '';
            gender = <?php echo json_encode($gender); ?>;
            var subtitle = '<strong>Offline VOTES</strong>:'+total+'<br/><strong>Online VOTES '+gender+'</strong>:'+anstotal+'<br/><strong>Total VOLES '+'</strong>:'+(parseInt(total)+parseInt(anstotal));
            var finallevel1 = <?php echo $finallevel1; ?>;
            // Level1 Chart
            Level1And2Report(questionText, total, chartType, finallevel1,key,subtitle);
        <?php    }}else{
    ?>

        var questionText = '{{$questionText}}';
        var total = '{{$total}}';
        var anstotal = '{{$anstotal}}';
        var chartType = '{{$chart}}';
        var finallevel1 = <?php echo $finallevel1; ?>;
        var gender = '';
        gender = <?php echo json_encode($gender); ?>;
        var subtitle = '<strong>Offline VOTES</strong>:'+total+'<br/><strong>Online VOTES '+gender+'</strong>:'+anstotal+'<br/><strong>Total VOLES '+'</strong>:'+(parseInt(total)+parseInt(anstotal));
        var key = 0;
        // Level1 Chart
        Level1And2Report(decodeEntities(questionText), total, chartType, finallevel1, key,subtitle);
    <?php } ?>

    function decodeEntities(encodedString) {
        var textArea = document.createElement('textarea');
        textArea.innerHTML = encodedString;
        return textArea.value;
    }


</script>

@stop

