@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Level 4 Intermediate Report
    </h1>
</section>

<section class="content"> 
    <div class="row">
        <div class="box box-info">
            <form id="level4AdvanceReport" class="form-horizontal" action="{{url('admin/level4IntermediateReport/')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-9">
                            <select name="professionId" id="professionId" class="form-control chosen-select-width" onchange="getConcept(this.value)">           
                                <?php
                                foreach($professions as $key => $profession) {
                                    ?>                    
                                    <option value="{{$profession->id}}" <?php
                                    if (isset($professionid) && $professionid == $profession->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$profession->pf_name}}</option>               
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="profession_concept"> 
                        
                    </div>
                    <div class="form-group"> 
                        <label for="question" class="col-sm-2 control-label">Select Teen</label>
                        <div class="col-sm-9">
                            <select name="teenagerId" id="teenagerId" class="form-control chosen-select-width">
                                <option value="0">All Teenager</option>
                                <?php
                                foreach ($teenDetails as $key => $teen) {
                                    ?>
                                    <option value="{{$teen->id}}" <?php
                                    if (isset($teenagerid) && $teenagerid == $teen->id) {
                                        echo "selected='selected'";
                                    }
                                    ?> > {{$teen->t_name}} -- {{$teen->t_email}}</option>
                                        <?php }
                                        ?>
                            </select>
                        </div>
                    </div>
                    @include('admin/ChartType')
                    <div id="genderFilter" style="display:none;">
                        @include('admin/GenderType')
                    </div>
                    <?php $gender = (isset($gender)) ? $gender : '';
                        if ($gender == 1) {
                            $gender = "Male";
                        } else if ($gender == 2) {
                            $gender = "Female";
                        }?>
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
    var selectedProfession = $('select[name=professionId]').val(); 
    var concept = '<?php echo $concept?>';
    
    getConcept(selectedProfession,concept);
    function getConcept(professionid,concept)
    {
        $.ajax({
            url: "{{ url('/admin/getProfessionConcepts') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionid": professionid,
                "concept": concept,
                "all":1
            },
            success: function(response) {
                $('#profession_concept').html(response);
                $('#concept').chosen();
            }
        });
    }

    var chartType = '<?php echo $chart?>';
    var chartData = <?php echo $intermediateData; ?>;
    var displayMsg = 'test';
    var totalTeen = <?php echo $totalTeen; ?>;
    var totalTeenByGen = <?php echo $totalTeenByGender; ?>;
    var gender = '';
    gender = <?php echo json_encode($gender); ?>;
    var displayMsg = 'test';
    var subtitle = 'Total Teenagers:'+totalTeen+'<br/>';
    if ($('#teenagerId').val() == '0' && gender != ''){
        subtitle += gender+':'+totalTeenByGen;
    }

    loadChart(chartType,chartData,'highchart_option',displayMsg);

    function loadChart(chartType,data,loadDiv,displayMsg){
        $('#'+loadDiv).highcharts({
            chart: {
                type: chartType,
            },
            title: {
                text: 'Level4 Intermediate Report'
            },
            subtitle: {
                text: subtitle
            },
            xAxis: {
                type: 'category',
                width :650

            },
            legend: {
                enabled:false
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Points'
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
                pointFormat: '<a href=""><span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><a><br/>'
            },
            series: [{
                    colorByPoint: true,
                    data: chartData
                }]

        });
    }

    if ($('#teenagerId').val() == '0'){
        $("#genderFilter").show();
    }
    $('#teenagerId').on('change', function() {
      if ( this.value == '0')
      {
        $("#genderFilter").show();
      }
      else
      {
        $("#genderFilter").hide();
      }
    });

</script>
@stop

