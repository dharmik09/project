@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professioncertification')}}
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
                    <h3 class="box-title"><?php echo (isset($certification) && !empty($certification)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.professioncertification')}}</h3>
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
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addProfessionCertification" class="form-horizontal" method="post" action="{{ url('/admin/saveProfessionCertification') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($certification) && !empty($certification)) ? $certification->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($certification) && !empty($certification)) ? $certification->pc_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('pc_name'))
                        $pc_name = old('pc_name');
                    elseif ($certification)
                        $pc_name = $certification->pc_name;
                    else
                        $pc_name = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_name" class="col-sm-2 control-label">{{trans('labels.professioncertificationname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pc_name" name="pc_name" placeholder="{{trans('labels.professioncertificationname')}}" value="{{$pc_name}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('pc_image'))
                        $pc_image = old('pc_image');
                    elseif ($certification)
                        $pc_image = $certification->pc_image;
                    else
                        $pc_image = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_image" class="col-sm-2 control-label">{{trans('labels.professioncertificationimage')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="pc_image" name="pc_image" onchange="readURL(this);"/>
                            <?php
                                if (isset($certification->id) && $certification->id != '0') {
                                    $image = (isset($certification->pc_image) && $certification->pc_image != "" ) ? Storage::url($certificationThumbImageUploadPath.$certification->pc_image) : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($certification)
                        $deleted = $certification->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
                            <?php foreach ($staus as $key => $value) { ?>
                                <option value="{{$key}}" <?php if ($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professionCertifications') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript">
    $('.numeric').on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    jQuery(document).ready(function() {
        <?php if (isset($certification->id) && $certification->id != '0') { ?>
            var validationRules = {
                pc_name : {
                    required : true,
                    minlength : 2
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                pc_name : {
                    required : true,
                    minlength : 2
                },
                pc_image : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addProfessionCertification").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                pc_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pc_image : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
@stop
