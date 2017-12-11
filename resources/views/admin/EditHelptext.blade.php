@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.helptext')}}
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
                    <h3 class="box-title"><?php echo (isset($helptext) && !empty($helptext)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.helptext')}}</h3>
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
                <form id="addHelptext" class="form-horizontal" method="post" action="{{ url('/admin/saveHelpText') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($helptext) && !empty($helptext)) ? $helptext->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('h_title'))
                        $h_title = old('h_title');
                    elseif ($helptext)
                        $h_title = $helptext->h_title;
                    else
                        $h_title = '';
                    ?>
                    <div class="form-group">
                        <label for="h_title" class="col-sm-2 control-label">{{trans('labels.helptexttitle')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="h_title" name="h_title" placeholder="{{trans('labels.helptexttitle')}}" value="{{$h_title}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('h_slug'))
                        $h_slug = old('h_slug');
                    elseif ($helptext)
                        $h_slug = $helptext->h_slug;
                    else
                        $h_slug = '';
                    ?>
                    <div class="form-group">
                        <label for="h_slug" class="col-sm-2 control-label">{{trans('labels.helptextslug')}}</label>
                        <div class="col-sm-6">
                            <input type="text" readonly="true" class="form-control" id="h_slug" name="h_slug" placeholder="{{trans('labels.helptextslug')}}" value="{{$h_slug}}"/>
                        </div>
                    </div>
                    
                    <?php
                    if (old('h_description'))
                        $h_description = old('h_description');
                    elseif ($helptext)
                        $h_description = $helptext->h_description;
                    else
                        $h_description = '';
                    ?>

                    <div class="form-group">
                        <label for="h_description" class="col-sm-2 control-label">{{trans('labels.helptextdescription')}}</label>
                        <div class="col-sm-6">
                            <textarea id="h_description" name="h_description" class="form-control">{{$h_description}}</textarea>
                        </div>
                    </div>
                    <?php
                    if (old('h_page'))
                        $h_page = old('h_page');
                    elseif ($helptext)
                        $h_page = $helptext->h_page;
                    else
                        $h_page = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.helptextpage')}}</label>
                        <div class="col-sm-6">
                            <?php $pageName = Helpers::page(); ?>
                            <select class="form-control" id="h_page" name="h_page">
                                <option value="">Select Page</option>
                                <?php foreach ($pageName as $key => $value) { ?>
                                    <option value="{{$key}}" <?php if ($h_page == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($helptext)
                        $deleted = $helptext->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/helpText') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
        <?php if (isset($helptext->id) && $helptext->id != '0') { ?>
            var validationRules = {
                h_title : {
                    required : true
                },
                h_slug : {
                    required : true
                },
                h_description : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                h_title : {
                    required : true
                },
                h_slug : {
                    required : true
                },
                h_description : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addHelptext").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                h_title : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                h_slug : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                h_description : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
<?php if (empty($helptext)){ ?>
    <script>
        $('#h_title').keyup(function ()
        {
            var str = $(this).val();
            str = str.replace(/[^a-zA-Z0-9\s]/g, "");
            str = str.toLowerCase();
            str = str.replace(/\s/g, '-');
            $('#h_slug').val(str);
        });
    </script>
<?php } ?>
@stop
