@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        Level4 Intermediate Activity
        <a href="{{ url('admin/addIntermediateActivity') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-striped" id="list_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                               <th>{{trans('labels.serialnumber')}}</th>
                               <th>Profession</th>
                               <th>Question Concept</th>
                               <th>Question</th>
                               <th>Time</th>
                               <th>Point</th>
                               <th>Status</th>
                               <th>{{trans('labels.basketblheadaction')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('script')
    <script type="text/javascript">
        var getAllList = function(ajaxParams){
            $('#list_table').DataTable({
                "processing": true,
                "serverSide": true,
                "destroy": true,
                "ajax":{
                    "url": "{{ url('admin/getListLevel4IntermediateActivity') }}",
                    "dataType": "json",
                    "type": "POST",
                    headers: { 
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "data" : function(data) {
                        if (ajaxParams) {
                            $.each(ajaxParams, function(key, value) {
                                data[key] = value;
                            });
                            ajaxParams = {};
                        }
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "pf_name" },
                    { "data": "gt_template_title" },
                    { "data": "l4ia_question_text" },
                    { "data": "l4ia_question_time"},
                    { "data": "l4ia_question_point"},
                    { "data": "deleted"},
                    { "data": "action", "orderable": false }
                ]
            });
        };

        $(document).ready(function() {
            var ajaxParams = {};
            getAllList(ajaxParams);
        });
    </script>
@stop