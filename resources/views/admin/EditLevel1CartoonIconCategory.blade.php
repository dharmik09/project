@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script> 

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1cartooniconcategory')}}
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
                    <h3 class="box-title"><?php echo (isset($cartoonIconCategoryDetail) && !empty($cartoonIconCategoryDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1cartooniconcategory')}}</h3>
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

                <form id="addCartoonIconCategory" class="form-horizontal" method="post" action="{{ url('/admin/saveCartoonIconCategory') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($cartoonIconCategoryDetail) && !empty($cartoonIconCategoryDetail)) ? $cartoonIconCategoryDetail->id : '0' ?>">

                    <div class="box-body">

                   <?php
                    if (old('cic_name'))
                        $cic_name = old('cic_name');
                    elseif ($cartoonIconCategoryDetail)
                        $cic_name = $cartoonIconCategoryDetail->cic_name;
                    else
                        $cic_name = '';
                    ?>
                    <div class="form-group">
                        <label for="cic_name" class="col-sm-2 control-label">{{trans('labels.cartooniconheadname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="cic_name" name="cic_name" placeholder="{{trans('labels.cartooniconheadname')}}" value="{{$cic_name}}" />
                        </div>
                    </div>


                    <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($cartoonIconCategoryDetail)
                            $deleted = $cartoonIconCategoryDetail->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/cartoonIconsCategory') }}">{{trans('labels.cancelbtn')}}</a>
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
      <?php if(isset($cartoonIconCategoryDetail->id) && $cartoonIconCategoryDetail->id != '0') { ?>
            var validationRules = {
                cic_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                cic_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addCartoonIconCategory").validate({
            rules : validationRules,
            messages : {
                cic_name : {
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