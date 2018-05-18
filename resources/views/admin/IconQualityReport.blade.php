@extends('layouts.admin-master')

@section('content')
<style type="text/css">
    .highlighted{
        color: #fff !important;
        background-color:#3875d7 !important;
    }
</style>
<section class="content-header">
    <h1>
        Icon Qualities Report
    </h1>
</section>

<section class="content"> 
    <div class="row">
        <div class="box box-info">
            <form id="displayLevel1Report" class="form-horizontal" action="{{url('admin/iconQualityReport/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Teen</label>
                        <div class="col-sm-9">
                            <select name="teenagerId"  class="form-control chosen-select-width">
                                <option value="">-Select-</option>
                                <?php
                                foreach ($teenDetails as $key => $teen) {
                                    ?>                    
                                    <option value="{{$teen->id}}" <?php
                                    if (isset($id) && $id == $teen->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$teen->t_name}} -- {{$teen->t_email}} </option>               
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Type</label>
                        <div class="col-sm-3">
                            <select name="icontype"  class="form-control">           
                                <option value="icon" <?php if (isset($selectedType) && $selectedType == 'icon') { echo "selected='selected'";}?>>ICONs</option>
                                <option value="self" <?php if (isset($selectedType) && $selectedType == 'self') { echo "selected='selected'";}?>>Self</option>
                            </select>
                        </div>
                    </div>
                    @include('admin/ChartType')
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
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_option">Chart Loads here...</div>  
            </div>
            </div>
        </div>
    </div>
    
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
    var chartType = '<?php echo $chart?>';
    var data = <?php echo $iconQualityData; ?>;
    var displayMsg = <?php echo json_encode($displayMsg); ?>;
    
    loadChart(chartType,data,'highchart_option',displayMsg);
    
    function loadChart(chartType,chartData,loadDiv,displayMsg){
        $('#'+loadDiv).highcharts({
            chart: {
                type: chartType,
            },
            title: {
                text: displayMsg
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category'
                
            },
            legend: {
                enabled:false
            },
            yAxis: {
                min: 0,                
                title: {
                    text: 'Qualities Selected'
                },                
                lineWidth: 1                
            },
            
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            
            tooltip: {
                pointFormat: '<a href=""><span style="color:{point.color}">Total {point.name}</span>: <b>{point.y}</b><a><br/>'
            },
            series: [{
                    colorByPoint: true,
                    data: chartData
                }]
           
        });
    }                                 
</script>
@stop    

