@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.dashboard')}}
    </h1>
</section>

<section class="content">
    <div class="row">
        @include('flash::message')
        <div class="box box-info" style="padding-top: 50px;">
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
    var chartType = 'pie';

    //Level1 Chart
    $(function () {
        var dashboardData = <?php echo $dashboardData;?>;
        var loginEmiail = "{{Auth::guard('admin')->user()->email}}";
        var otherAdminEmail = '<?php echo trans('labels.adminemailid'); ?>';
        $('#highchart_option').highcharts({
            chart: {
                type: chartType,
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}',
                    },
                    showInLegend: true,
                    size: 300,
                    point: {
                        events: {
                          click: function() {
                            if(loginEmiail != otherAdminEmail) {
                                location.href = this.url;
                            }
                          }
                        }
                      }
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
            },
            series: [{
                colorByPoint: true,
                data: dashboardData
            }]

        });
    });

</script>

@stop