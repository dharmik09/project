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

