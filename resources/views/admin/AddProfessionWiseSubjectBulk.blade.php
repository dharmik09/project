@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professionwisesubject')}}
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
                    <h3 class="box-title">{{trans('labels.add')}} {{trans('labels.professionwisesubject')}}</h3>
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

                <form id="addProfessionSubejctBulk" class="form-horizontal" method="post" files="true" action="{{ url('/admin/saveProfessionWiseSubjectBulk') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">

                    <div class="form-group">
                        <label for="p_bulk" class="col-sm-3 control-label">{{trans('labels.formprofessionwisesubjectbulkupload')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="p_bulk" name="p_bulk" />
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
                 p_bulk : {
                    required : true
                }
            }


        $("#addProfessionSubejctBulk").validate({
            rules : validationRules,
            messages : {
                  p_bulk : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });


</script>
@stop

