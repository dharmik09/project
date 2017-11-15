@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.lblprofessionlearningstyle')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
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

                <form id="addLearningStyleBulk" class="form-horizontal" method="post" action="{{ url('/admin/addLeaningStyleImportExcel') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">

                    <div class="form-group">
                        <label for="learning_style_bulk" class="col-sm-2 control-label">Import Learning Style</label>
                        <div class="col-sm-6">
                            <input type="file" id="importfile" name="importfile" />
                        </div>
                    </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >submit</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professionLearningStyle') }}">{{trans('labels.cancelbtn')}}</a>
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
                 importfile : {
                    required : true
                }
            }


        $("#addLearningStyleBulk").validate({
            rules : validationRules,
            messages : {
                  importfile : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });


</script>
@stop

