@extends('layouts.admin-master')

@section('style')
<link rel='stylesheet' src='https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css'/>
@stop

@section('content')

<section class="content-header">
    <h1>
        School Report
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="box box-info">
            <form id="displaySchoolReport" class="form-horizontal" action="{{url('admin/schoolReport/')}}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="school" class="col-sm-2 control-label">Select School</label>
                        <div class="col-sm-6">
                            <select id="school" name="school_id" class="form-control chosen-select">
                                @if(isset($schools[0]) && !empty($schools[0]) )
                                    <option value="">Select school</option>
                                    @foreach($schools as $school)
                                        <option value="{{$school->id}}" @if($school_id == $school->id) selected='selected' @endif>{{$school->sc_name}}</option>
                                    @endforeach
                                @else
                                    <option>No any schools available</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="school_class">
                        <label for="schoolClass" class="col-sm-2 control-label">Select Class</label>
                        <div class="col-sm-6">  
                            <select id="schoolClass" class="form-control chosen-select" name="class_id">  
                                @if(isset($schoolClass[0]) && !empty($schoolClass[0]) )
                                    <option value="all" @if($class_id == "all") selected='selected' @endif>All</option>
                                    @foreach($schoolClass as $class)
                                        <option value="{{$class->t_class}}" @if($class_id == $class->t_class) selected='selected' @endif>{{$class->t_class}}</option>
                                    @endforeach
                                @else
                                    <option>No class available</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <input id="search_button" type="button" name="search_button" value="Search" class="btn btn-primary btn-flat">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box box-info">
            <div class="box-body">                    
                <table class="table table-striped" id="studentTable" width="100%">
                    <thead>
                        <tr>
                            <th>{{trans('labels.stuname')}}</th>
                            <th>Roll No</th>
                            <th>Email</th>
                            <th>{{trans('labels.class')}}</th>
                            <th>{{trans('labels.division')}}</th>
                            <th>Verified Status</th>
                            <th>School Validate Status</th>
                            <th>Level 1</th>
                            <th>Level 2</th>
                            <th>Level 3</th>
                            <th>Level 4</th>
                            <th>Last Acctivity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentData as $key => $student)
                            <tr>
                                <td>{{ $student->t_name }}</td>
                                <td>{{ $student->t_rollnum }}</td>  
                                <td>{{ $student->t_email }}</td>    
                                <td>{{ $student->t_class }}</td>    
                                <td>{{ $student->t_division }}</td> 
                                <td>@if ($student->t_isverified == 1) <span>Yes</span> @else <span>No</span> @endif</td>
                                <td>@if ($student->t_school_status == 1) <span>Yes</span> @else <span>No</span> @endif</td>
                                <td>{{ $student->level_1 }}</td>
                                <td>{{ $student->level_2 }}</td>
                                <td>{{ $student->level_3 }}</td>
                                <td>{{ $student->level_4 }}</td>
                                <td>{{ ($student->t_last_activity > 0) ? date("d-m-Y", $student->t_last_activity) : '---' }}</td>
                            </tr>
                        @empty
                            <tr> <td colspan="10">No any students yet!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box box-info">
            <div class="box-body" id="myChart">
                Chart will be display here!
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

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

    $(document).ready(function(){
        var table = $('#studentTable').DataTable();
    });

    $("#school").change(function(){
        if($.trim(this.value) != "")
        {
            $("#schoolClass").html("<option value=''>Select class</option>");
            $.ajax({
                url: "{{ url('admin/getClass/') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "school_id": this.value,
                },
                success: function(response) {
                    $("#schoolClass").html(response);
                    $("#schoolClass").trigger("chosen:updated");
                }
            });
        }
        else
        {
            $("#schoolClass").html("<option value=''>No class available</option>");
            $("#schoolClass").trigger("chosen:updated");
        }
    });

    var dataRules = {
        school_id: {
            required: true
        },
        class_id: {
            required: true
        }
    };
    
    $("#displaySchoolReport").validate({
        rules: dataRules,
        messages: {
            school_id: { required: '<?php echo trans('validation.requiredfield') ?>'},
            class_id: { required: '<?php echo trans('validation.requiredfield') ?>'}
        }
    });

    $("#search_button").click(function(){
        var form = $("#displaySchoolReport");
        form.validate();
        if (form.valid()) 
        {
            form.submit();
            $("#search_button").attr("disabled", 'disabled');
        } 
        else 
        {
            $("#search_button").removeAttr("disabled", 'disabled');
        }
    });

    <?php if(isset($studentData) && isset($school_id)) { ?>
        $('#myChart').highcharts({
            chart: {
                type: 'column',
            },
            title: {
                text: 'Total {{count($studentData)}} students from class {{$class_id}}'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category',
                width: '600'
            },
            legend: {
                enabled:false
            },
            yAxis: {                
                title: {
                    text: 'Students'
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
                pointFormat: '<a href=""><span style="color:{point.color}">Total {point.name} students</span>: <b>{point.y}</b><a><br/>'
            },
            series: [{
                colorByPoint: true,
                data: <?php echo $reportDataJson; ?>
            }]
        });
    <?php } ?>

</script>

@stop