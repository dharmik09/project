@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1cartoonicons')}}
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
                    <h3 class="box-title"><?php echo (isset($cartoonIconDetail) && !empty($cartoonIconDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1cartoonicon')}}</h3>
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
                if (isset($cartoonIconDetail) && !empty($cartoonIconDetail)) {
                    foreach ($cartoonIconDetail as $value) {
                        $id = $value->id;
                        $ci_category = $value->ci_category;
                        $ci_name = $value->ci_name;
                        $ci_image = $value->ci_image;
                        $deleted = $value->deleted;
                        $cpm_profession_id = $value->cpm_profession_id;
                    }
                } else {
                    $id = '';
                    $ci_category = '';
                    $ci_name = '';
                    $ci_image = '';
                    $deleted = '';
                    $cpm_profession_id = '';
                }

                $profession_id = explode(",", $cpm_profession_id);
                ?>
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addCartoonIcon" class="form-horizontal" method="post" action="{{ url('/admin/saveCartoon') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($ci_image) && !empty($ci_image)) ? $ci_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="l1ac_text" class="col-sm-2 control-label">{{trans('labels.cartooniconheadname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="l1ci_name" name="l1ci_name" placeholder="{{trans('labels.cartooniconheadname')}}" value="{{$ci_name}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.cartooniconheadimage')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="l1ci_image" name="l1ci_image" onchange="readURL(this);"/>
                                @if(isset($cartoonIconDetail[0]->id) && $cartoonIconDetail[0]->id != '0')
                                    <?php
                                        $image = ($cartoonIconDetail[0]->ci_image != "" && Storage::disk('s3')->exists($cartoonThumbPath.$cartoonIconDetail[0]->ci_image)) ? Config::get('constant.DEFAULT_AWS').$cartoonThumbPath.$cartoonIconDetail[0]->ci_image : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>


                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblcategory')}}</label>
                            <div class="col-sm-6">
                                <?php $category = Helpers::getActiveCartoonCategory(); ?>
                                <select class="form-control" id="ci_category" name="ci_category">
                                    <option value="" >{{trans('labels.formlblselectcategory')}}</option>
                                <?php
                                foreach ($category as $key => $value) {
                                    if ($value->id == $ci_category)
                                        
                                        ?>                                         
                                        <option value="{{$value->id}}" <?php if ($ci_category != '') {
                                        if ($value->id == $ci_category) echo 'selected';
                                    } ?>> {{$value->cic_name}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/cartoons') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
<?php if (isset($id) && $id != '0' && $id != '') { ?>
            var validationRules = {
                l1ci_name: {
                    required: true
                },
                ci_category: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }

<?php } else { ?>
            var validationRules = {
                l1ci_name: {
                    required: true
                },
                l1ci_image: {
                    required: true
                },
                ci_category: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addCartoonIcon").validate({
            rules: validationRules,
            messages: {
                l1ci_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                l1ci_image: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                ci_category: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        })

    });
</script>
@stop