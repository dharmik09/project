@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script> 

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.lblforumquestion')}}
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
                    <h3 class="box-title"><?php echo (isset($data) && !empty($data)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.lblforumquestion')}}</h3>
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

                <?php
                    if($data)
                    {
                        $id = $data->id;
                        $fq_que = $data->fq_que;
                        $deleted = $data->deleted;
                    }
                    else
                    {
                        $id = '';
                        $fq_que = '';
                        $deleted = '';
                    }
                ?>
                <form id="addForumQuestion" class="form-horizontal" method="post" action="{{ url('/admin/saveForumQuestion') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : ''?>">

                    <div class="box-body">
                    
                        <div class="form-group">
                            <label for="fq_que" class="col-sm-2 control-label">{{trans('labels.lblenterquestion')}}</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="fq_que" name="fq_que" placeholder="{{trans('labels.lblenterquestion')}}">{{$fq_que}}</textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/forumQuestions') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop
@section('script')

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        var validationRules =
        {
            fq_que : {
                required : true
            }
        }


        $validate("#addForumQuestion").validate({
            rules : validationRules,
            messages : {
                fq_que : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        });
    });
</script>
@stop