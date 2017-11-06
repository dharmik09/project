@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script> 

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1humaniconcategory')}}
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
                    <h3 class="box-title"><?php echo (isset($humanIconCategoryDetail) && !empty($humanIconCategoryDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1humaniconcategory')}}</h3>
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

                <form id="addHumanIconCategory" class="form-horizontal" method="post" action="{{ url('/admin/saveHumanIconCategory') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($humanIconCategoryDetail) && !empty($humanIconCategoryDetail)) ? $humanIconCategoryDetail->id : '0' ?>">

                    <div class="box-body">

                   <?php
                    if (old('hic_name'))
                        $hic_name = old('hic_name');
                    elseif ($humanIconCategoryDetail)
                        $hic_name = $humanIconCategoryDetail->hic_name;
                    else
                        $hic_name = '';
                    ?>
                    <div class="form-group">
                        <label for="hic_name" class="col-sm-2 control-label">{{trans('labels.cartooniconheadname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="hic_name" name="hic_name" placeholder="{{trans('labels.humaniconheadname')}}" value="{{$hic_name}}" />
                        </div>
                    </div>


                    <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($humanIconCategoryDetail)
                            $deleted = $humanIconCategoryDetail->deleted;
                        else
                            $deleted = '';
                        ?>
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                     </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/humanIconsCategory') }}">{{trans('labels.cancelbtn')}}</a>
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
      <?php if(isset($humanIconCategoryDetail->id) && $humanIconCategoryDetail->id != '0') { ?>
            var validationRules = {
                hic_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                hic_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addHumanIconCategory").validate({
            rules : validationRules,
            messages : {
                hic_name : {
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