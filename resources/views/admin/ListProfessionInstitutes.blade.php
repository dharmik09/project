@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.professioninstitueslist')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addProfessionInstituteCourseList') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
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
                    <table id="listProfessionschoollist" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.lblinstitutesid')}}</th>
                                <th>{{trans('labels.lblname')}}</th>
                                <th>{{trans('labels.lblstate')}}</th>
                                <th>{{trans('labels.lblpincode')}}</th>
                                <th>{{trans('labels.lblaffialteduniversity')}}</th>
                                <th>{{trans('labels.lblmanagement')}}</th>
                                <th>{{trans('labels.lblaccredationbody')}}</th>
                                <th>{{trans('labels.lblaccredationscore')}}</th>
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
    $(document).ready(function() {
        var ajaxParams = {};
        getProfessionList(ajaxParams);
    });

    var getProfessionList = function(ajaxParams){
        $('#listProfessionschoollist').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
                "url": "{{ url('admin/getProfessionInstitute') }}",
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
                    }
                }
            },
            "columns": [
                { "data" : "school_id" },
                { "data" : "college_institution" },
                { "data" : "institute_state" },
                { "data" : "pin_code" },
                { "data" : "affiliat_university" },
                { "data" : "management" },
                { "data" : "accreditation_body" },
                { "data" : "accreditation_score" }
            ]
        });
    };
</script>
@stop