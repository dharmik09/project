@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professioninstitueslist')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('labels.add')}} {{trans('labels.professioninstitueslist')}}</h3>
                </div><!-- /.box-header -->
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="addProfessionBulk" class="form-horizontal" method="post" files="true" action="{{ url('/admin/saveProfessionInstituteCourseList') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">


                    <div class="form-group">
                        <label for="ps_upload_type" class="col-sm-3 control-label">{{trans('labels.professionschooluploadtype')}}</label>
                        <div class="col-sm-6">
                            <select id="ps_upload_type" name="ps_upload_type" class="form-control">
                                <option selected disabled>{{trans('labels.professionschooluploadtype')}}</option>
                                <option value="1">{{trans('labels.professionschooluploadtypebasicinformation')}}</option>
                                <option value="2">{{trans('labels.professionschooluploadtypeaccreditation')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ps_bulk" class="col-sm-3 control-label">{{trans('labels.professioninstitueslistupload')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="ps_bulk" name="ps_bulk" class="form-control"/>
                        </div>
                    </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professions') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
            <div class="box box-info">
                <div class="box-body">
                    <table id="listTag" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.fileName')}}</th>
                                <th>{{trans('labels.lblupload')}}</th>
                                <th>{{trans('labels.status')}}</th>
                                <th>{{trans('labels.description')}}</th>
                                <th>{{trans('labels.lastuploadtime')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{trans('labels.professionschooluploadtypebasicinformation')}}
                                </td>
                                <td>
                                    @if (file_exists(public_path('uploads/excel/ProfessionInstituteBasic.xlsx')))
                                        <a href="{{ url('/admin/professionInstituteUpload') }}/1" target="_blank" class="btn btn-default">Upload</a>
                                    @else
                                        No file Found
                                    @endif
                                </td>
                                @if(isset($basicExcelData) && count($basicExcelData)>0)
                                    <td>
                                        @if($basicExcelData->status == '0')
                                            Pending
                                        @elseif($basicExcelData->status == '1')
                                            Success
                                        @elseif($basicExcelData->status == '2')
                                            Failed
                                        @endif
                                    </td>
                                    <td>
                                        {{$basicExcelData->description}}
                                    </td>
                                    <td>
                                        {{date('d/M/Y h:i:s A',strtotime($basicExcelData->created_at))}}
                                    </td>
                                @else
                                    <td></td><td></td><td></td>
                                @endif
                            </tr>
                            <tr>
                                <td>
                                    {{trans('labels.professionschooluploadtypeaccreditation')}}
                                </td>
                                <td>
                                    @if (file_exists(public_path('uploads/excel/ProfessionInstituteAccreditation.xlsx')))
                                        <a href="{{ url('/admin/professionInstituteUpload') }}/2" target="_blank" class="btn btn-default">Upload</a>
                                    @else
                                        No file Found
                                    @endif
                                </td>
                                @if(isset($accreditationExcelData) && count($accreditationExcelData)>0)
                                    <td>
                                        @if($accreditationExcelData->status == '0')
                                            Pending
                                        @elseif($accreditationExcelData->status == '1')
                                            Success
                                        @elseif($accreditationExcelData->status == '2')
                                            Failed
                                        @endif
                                    </td>
                                    <td>
                                        {{$accreditationExcelData->description}}
                                    </td>
                                    <td>
                                        {{date('d/M/Y h:i:s A',strtotime($accreditationExcelData->created_at))}}
                                    </td>
                                @else
                                    <td></td><td></td><td></td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {

            var validationRules = {
                ps_upload_type : {
                    required : true
                },
                ps_bulk : {
                    required : true
                }
            }


        $("#addProfessionBulk").validate({
            rules : validationRules,
            messages : {
                ps_upload_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_bulk : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });

</script>
@stop

