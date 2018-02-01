@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professionsubject')}}
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
                    <h3 class="box-title"><?php echo (isset($subject) && !empty($subject)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.professionsubject')}}</h3>
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
                <form id="addProfessionSubject" class="form-horizontal" method="post" action="{{ url('/admin/saveProfessionSubject') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($subject) && !empty($subject)) ? $subject->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($subject) && !empty($subject)) ? $subject->ps_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('ps_name'))
                        $ps_name = old('ps_name');
                    elseif ($subject)
                        $ps_name = $subject->ps_name;
                    else
                        $ps_name = '';
                    ?>
                    <div class="form-group">
                        <label for="ps_name" class="col-sm-2 control-label">{{trans('labels.professionsubjectname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="ps_name" name="ps_name" placeholder="{{trans('labels.professionsubjectname')}}" value="{{$ps_name}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('ps_slug'))
                        $ps_slug = old('ps_slug');
                    elseif ($subject)
                        $ps_slug = $subject->ps_slug;
                    else
                        $ps_slug = '';
                    ?>
                    <div class="form-group">
                        <label for="ps_slug" class="col-sm-2 control-label">{{trans('labels.professionsubjectslug')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="ps_slug" name="ps_slug" placeholder="{{trans('labels.professionsubjectslug')}}" value="{{$ps_slug}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('ps_image'))
                        $ps_image = old('ps_image');
                    elseif ($subject)
                        $ps_image = $subject->ps_image;
                    else
                        $ps_image = '';
                    ?>
                    <div class="form-group">
                        <label for="ps_image" class="col-sm-2 control-label">{{trans('labels.professionsubjectimage')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="ps_image" name="ps_image" onchange="readURL(this);"/>
                            <?php
                                if (isset($subject->id) && $subject->id != '0') {
                                    $image = (isset($subject->ps_image) && $subject->ps_image != "" ) ? Storage::url($subjectThumbImageUploadPath.$subject->ps_image) : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($subject)
                        $deleted = $subject->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professionSubjects') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
        $("#ps_slug").attr('readonly', true);
        <?php if (isset($subject->id) && $subject->id != '0') { ?>
            var validationRules = {
                ps_name : {
                    required : true,
                    minlength : 2
                },
                ps_slug : {
                    required: true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                ps_name : {
                    required : true,
                    minlength : 2
                },
                ps_slug : {
                    required: true
                },
                ps_image : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addProfessionSubject").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                ps_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                ps_slug : {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                ps_image : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
<?php if (empty($subject->ps_slug)){ ?>
    <script>
    $('#ps_name').keyup(function ()
    {
        var str = $(this).val();
        str = str.replace(/[^a-zA-Z0-9\s]/g, "");
        str = str.toLowerCase();
        str = str.replace(/\s/g, '_');
        $('#ps_slug').val(str);
    });
    </script>
    <?php } ?>
@stop
