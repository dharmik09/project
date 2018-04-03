@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-3">
            {{trans('labels.professioninstitueslist')}}
        </div>
        <div class="col-md-3">
            <a href="{{ url('admin/updateeducationspeciality') }}" class="btn btn-block btn-primary">Refresh Education Speciality</a>
        </div>
        <div class="col-md-3">
                <a href="{{ url('admin/exportInstitute') }}" class="btn btn-block btn-primary">Export All Institute</a>
            </div>
        <div class="col-md-3">
            <a href="{{ url('admin/addProfessionInstituteCourseList') }}" class="btn btn-block btn-primary">Upload Institutes</a>
        </div>
    </h1>
</section>


<div class="modal modal-centered fade" id="addInstituePhotoModal" tabindex="-1" role="dialog" aria-labelledby="addInstituePhotoModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="addInstituePhotoModalHeader">{{trans('labels.lbladdphotos')}}</h2>
            </div>
            <div class="modal-body">
                <form id="deleteRelationshipManager" class="form-horizontal" method="post" action="{{ url('admin/saveProfessionInstitutePhoto') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="institute_id" name="institute_id" value="0">
                    <input type="hidden" id="oldimage" name="oldimage" value="">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="institute_photo" class="col-sm-4 control-label">{{trans('labels.lblelselectinstitutephoto')}}</label>
                            <div class="col-sm-6">
                                <input type="file" class="form-control-file" id="institute_photo" name="institute_photo">
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <div class="pull-right">
                            <button type="submit" class="btn save-btn bg-purple">{{trans('labels.savebtn')}}</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('labels.cancelbtn')}}</button>
                        </div>
                    </div><!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>
</div>

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
                                <th>{{trans('labels.lblcountry')}}</th>
                                <th>{{trans('labels.lblimage')}}</th>
                                <th>{{trans('labels.lblaction')}}</th>
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


    function showProfessionImageUploadModel(id,schoolId,Image){
        $("#institute_id").val(id);
        $("#oldimage").val(Image);
        $("#addInstituePhotoModalHeader").html("Update Photo for School Id : " + schoolId);
        $('#addInstituePhotoModal').modal('show');
    }

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
                { "data" : "accreditation_score" },
                { "data" : "country" },
                { "data" : "image" },
                { "data" : "action" }
            ]
        });
    };
</script>
@stop