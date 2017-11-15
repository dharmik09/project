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
        Teen PROMISE Report
    </h1>
</section>

<section class="content"> 
    <div class="row">
        <div class="box box-info">
            <form action="{{url('admin/userApi/')}}" method="post" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Teen</label>
                        <div class="col-sm-9">
                            <select name="teenagerId"  class="form-control chosen-select-width">           
                                <?php
                                foreach ($teenager as $key => $teen) {
                                    if ($teen->t_photo != '') {
                                        $profilePicUrl = Config::get('constant.DEFAULT_AWS').Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH') . $teen->t_photo;
                                    } else {
                                        $profilePicUrl = asset('/backend/images/proteen_logo.png');
                                    }
                                    ?>                    
                                    <option value="{{$teen->id}}" style="background: url({{$profilePicUrl}}); background-size: 20px 20px;
                                        background-repeat: no-repeat; padding-left: 25px;" <?php
                                    if (isset($id) && $id == $teen->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$teen->t_name}} -- {{$teen->t_email}} </option>               
                                        <?php }
                                        ?>
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
                <div id="highchart_mi">Chart Loads here...</div>  
            </div>               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                                
            <div class="col-md-12">
                <div id="highchart_aptitude">Chart Loads here...</div>  
            </div>    
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_personality">Chart Loads here...</div>  
            </div>               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                                
            <div class="col-md-12">
                <div id="highchart_interest">Chart Loads here...</div>  
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

var finalMI = <?php echo $finalscore['MI']; ?>;
var finalMIMax = <?php echo $finalscore['MIMax']; ?>;
var finalAptitude = <?php echo $finalscore['Aptitude']; ?>;
var finalAptitudeMax = <?php echo $finalscore['AptitudeMax']; ?>;
var finalPersonality = <?php echo $finalscore['Personality']; ?>;
var finalPersonalityMax = <?php echo $finalscore['PersonalityMax']; ?>;
var finalInterest = <?php echo $finalscore['Interest']; ?>;
var finalInterestMax = <?php echo $finalscore['finalInterestMax']; ?>;
var chartType = '{{$chart}}';

 
loadChart(finalMI,finalMIMax,'MI','highchart_mi',20); 
loadChart(finalAptitude,finalAptitudeMax,'Aptitude','highchart_aptitude',15); 
loadChart(finalPersonality,finalPersonalityMax,'Personality','highchart_personality',3); 
loadChart(finalInterest,finalInterestMax,'Interest','highchart_interest',5); 
                  
function loadChart(chartData,maxData,lableText,loadDiv,maxValue){
    $('#'+loadDiv).highcharts({
        chart: {
            type: chartType,
        },
        title: {
            text: lableText
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'API Score'
            },
            labels: {
                formatter: function () {
                    return this.value + "";
                }
            },
            lineWidth: 1,
            max : maxValue,
            tickInterval: 1
        },
        legend: {
            enabled: false,
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
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
         pointFormat: '<tr><td style="color:{point.color};padding:0">Point\'s : </td>' +
             '<td style="padding:0"><b>{point.y}</b></td></tr>',
         footerFormat: '</table>',
         shared: true,
         useHTML: true
        },
        series: [{
            colorByPoint: true,
            data: chartData
        }, {                
            
            data: maxData

        }]
    });
}
                    
</script>

@stop    

