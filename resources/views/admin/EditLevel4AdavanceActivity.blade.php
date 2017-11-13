@extends('layouts.admin-master')

@section('content')


<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script>


<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level4activityadvance')}}
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
                    <h3 class="box-title"><?php echo (isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level4activityadvance')}}</h3>
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

                <form id="addLevel4AdvanceActivity" class="form-horizontal" method="post" action="{{ url('/admin/savelevel4advanceactivity') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? $level4advacneactvityDetail->id : '0' ?>">

                    <div class="box-body">
                        
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Activity Type</label>
                        <div class="col-sm-6">
                            <?php $activity_type = (isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? $level4advacneactvityDetail->l4aa_type : '' ?>
                            <select class="form-control" id="activity_type" name="activity_type">
                                <option value="">Select Activity</option>
                                <option value="1" <?php if($activity_type == 1) echo 'selected'; ?>>Video</option>
                                <option value="2" <?php if($activity_type == 2) echo 'selected'; ?>>Document</option>
                                <option value="3" <?php if($activity_type == 3) echo 'selected'; ?>>Photo</option>                                
                            </select>
                        </div>
                    </div>    
                        
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="description" name="description">{{(isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? $level4advacneactvityDetail->l4aa_description : ''}}</textarea>
                        </div>
                    </div>    
    
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="question_text" name="question_text" placeholder="{{trans('labels.formlbltext')}}" value="{{(isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? $level4advacneactvityDetail->l4aa_text : ''}}" />
                        </div>
                    </div>    
                                       
                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); $deleted = (isset($level4advacneactvityDetail) && !empty($level4advacneactvityDetail)) ? $level4advacneactvityDetail->deleted : '' ?>
                            
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/listlevel4advanceactivity') }}"> {{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop
 <script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
@section('script')

<script type="text/javascript">

    $(document).ready(function()
    {
        var validationRules =
        {
                question_text : {
                    required : true
                },
                activity_type : {
                    required : true
                },
                deleted : {
                    required : true
                }
        }

        jQuery("#addLevel4AdvanceActivity").validate({
            rules : validationRules,
            messages : {
                question_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                activity_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        })
    });
</script>
@stop