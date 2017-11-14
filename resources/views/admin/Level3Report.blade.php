@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Level3 Report
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="box box-info">
            <form id="displayLevel1Report" class="form-horizontal" action="{{url('admin/level3Report/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="basket" class="col-sm-2 control-label">Select Basket</label>
                        <div class="col-sm-9">
                            <select id="basket" name="basket" class="form-control chosen-select">
                                <?php
                                foreach ($baskets as $key => $basket) {
                                    ?>
                                    <option text-align="left" value="{{$basket->id}}" <?php
                                    if (isset($basketId) && $basketId == $basket->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$basket->b_name}}</option>
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    @include('admin/ChartType')
                    @include('admin/GenderType')
                    <?php $gender = (isset($gender)) ? $gender : '';
                        if ($gender == 1) {
                            $gender = "Male";
                        } else if ($gender == 2) {
                            $gender = "Female";
                        }?>
                    <div class="form-group">
                        <label for="basket" class="col-sm-2 control-label">Select Top</label>
                        <div class="col-sm-2">
                            <select id="top" name="top" class="form-control">
                                <option value="top" <?php if (isset($topList) && $topList == 'top') { echo "selected='selected'";}?>>Top 10</option>
                                <option value="bottom" <?php if (isset($topList) && $topList == 'bottom') { echo "selected='selected'";}?>>Bottom 10</option>
                                <option value="all" <?php if (isset($topList) && $topList == 'all') { echo "selected='selected'";}?>>All Explored</option>
                                <option value="not" <?php if (isset($topList) && $topList == 'not') { echo "selected='selected'";}?>>Not Explored</option>
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
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_level3">Chart Loads here...</div>  
            </div>               
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <table class="table table-striped">
                        <?php $professionEncode = json_decode($professionData); ?>
                        <tr><th colspan="2">Total : {{count($professionEncode)}}</th></tr>
                        <tr>
                            <th>Profession</th>
                            <th>No of teen explored</th>
                        </tr>
                        @forelse($professionEncode as $profession)
                         <tr>
                            <td>
                                {{$profession->name}}
                            </td>
                            <td>
                                {{$profession->y}} @if ($gender != '' && ($profession->y != 0)) ({{$gender}}) @endif
                            </td>
                         </tr>
                         @empty
                         <tr>
                            <td colspan="2"><center>{{trans('labels.norecordfound')}}</center></td>
                         </tr>
                         @endforelse
                    </table>              
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
   
    var total = 'Total';
    var chartType = '{{$chart}}';
    var professionData = <?php echo $professionData;?>;          
    
    // Level1 Chart  
    var topLabel = '<?php echo $topList ?>';
    if(topLabel == 'top'){
        displayLabel = 'Top 10 explored professions';
    }else if(topLabel == 'bottom'){
       displayLabel = 'Bottom 10 explored professions'; 
    }else{
       displayLabel = 'All explored professions';  
    }
    loadChart(professionData,displayLabel,'highchart_level3'); 
                  
    function loadChart(chartData,lableText,loadDiv,maxValue)
    {
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
                    text: 'No of teen researching'
                },
                labels: {
                    formatter: function () {
                        return this.value + "";
                    }
                },
                lineWidth: 1,
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
                pointFormat: 'No of teens researching : <b>{point.y}</b>'
            },
            series: [{
                colorByPoint: true,
                data: chartData
            }]
        });
    }
   
</script>

@stop    

