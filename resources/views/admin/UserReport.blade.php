@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Teens Report
    </h1>
</section>

<section class="content"> 
   <div class="row">
        <div class="box box-info">
            <form id="displayLevel1Report" class="form-horizontal" action="{{url('admin/userReport/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                                
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
            <div class="col-md-6">
                <div id="highchart_device">Chart Loads here...</div>  
            </div>
            <div class="col-md-6">
                <div id="highchart_gender">Chart Loads here...</div>  
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-6">
                <div id="highchart_sponsor">Chart Loads here...</div>  
            </div>
            <div class="col-md-6">
                <div id="highchart_account">Chart Loads here...</div>  
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
    var chartType = '{{$chart}}';
    var total = '{{$total}}';
    var deviceData = <?php echo $deviceWiseUserJson; ?>;
    var genderData = <?php echo $genderWiseUserJson; ?>;
    var sponsorData = <?php echo $sponsorWiseUserJson; ?>;
    var accountData = <?php echo $accountWiseUserJson; ?>;
    var webGenderData = <?php echo $webGenderWiseUserJson; ?>;

    //loadChart(chartType,total,deviceData,'highchart_device');
    loadChart(chartType,total,genderData,'highchart_gender');
    loadChart(chartType,total,sponsorData,'highchart_sponsor');
    loadChart(chartType,total,accountData,'highchart_account');
    loadChart(chartType,total,webGenderData,'highchart_device');

    function loadChart(chartType,total,chartData,loadDiv){
        $('#'+loadDiv).highcharts({
            chart: {
                type: chartType,
            },
            title: {
                text: 'Total active teens : '+total
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category',
                width: '350'
            },
            legend: {
                enabled:false
            },
            yAxis: {                
                title: {
                    text: 'Teens'
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
                pointFormat: '<a href=""><span style="color:{point.color}">Total {point.name} teens</span>: <b>{point.y}</b><a><br/>'
            },
            series: [{
                    colorByPoint: true,
                    data: chartData
                }]
           
        });
    }             

                    
</script>

@stop    

