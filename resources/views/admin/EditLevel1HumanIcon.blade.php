@extends('layouts.admin-master')

@section('content')


<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1humanicons')}}
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
                    <h3 class="box-title"><?php echo (isset($humanIconDetail) && !empty($humanIconDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1humanicon')}}</h3>
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
                if ($humanIconDetail) {
                    foreach ($humanIconDetail as $value) {
                        $id = $value->id;
                        $hi_category = $value->hi_category;
                        $hi_name = $value->hi_name;
                        $hi_image = $value->hi_image;
                        $deleted = $value->deleted;
                        $hpm_profession_id = $value->hpm_profession_id;
                    }
                } else {
                    $id = '';
                    $hi_category = '';
                    $hi_name = '';
                    $hi_image = '';
                    $deleted = '';
                    $hpm_profession_id = '';
                }

                $profession_id = explode(",", $hpm_profession_id);
                ?>
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addHumanIcon" class="form-horizontal" method="post" action="{{ url('/admin/saveHumanIcon') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($hi_image) && !empty($hi_image)) ? $hi_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="hi_name" class="col-sm-2 control-label">{{trans('labels.cartooniconheadname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="hi_name" name="hi_name" placeholder="{{trans('labels.humaniconheadname')}}" value="{{$hi_name}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="hi_image" class="col-sm-2 control-label">{{trans('labels.cartooniconheadimage')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="hi_image" name="hi_image" onchange="readURL(this);"/>
<?php
if (isset($id) && $id != '0' && isset($humanThumbPath)) {
    if (File::exists(public_path($humanThumbPath . $hi_image)) && $hi_image != '') {
        ?><br>
                                        <img src="{{ url($humanThumbPath.$hi_image) }}" alt="{{$hi_image}}" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    <?php } else { ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    <?php
                                    }
                                }
                                ?>
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>

                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblcategory')}}</label>
                            <div class="col-sm-6">
<?php $category = Helpers::getActiveCategory(); ?>
                                <select class="form-control" id="hi_category" name="hi_category">
                                    <option value="" >{{trans('labels.formlblselectcategory')}}</option>
<?php
foreach ($category as $key => $value) {
    if ($value->id == $hi_category)
        ;
    ?>
                                        <option value="{{$value->id}}" <?php if ($hi_category != '') {
                                        if ($value->id == $hi_category) echo 'selected';
                                    } ?>> {{$value->hic_name}}</option>


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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/humanicons') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
                hi_name: {
                    required: true
                },
                hi_category: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } else { ?>
            var validationRules = {
                hi_name: {
                    required: true
                },
                hi_category: {
                    required: true
                },
                hi_image: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addHumanIcon").validate({
            rules: validationRules,
            messages: {
                hi_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                hi_category: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                hi_image: {
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