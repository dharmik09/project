@extends('developer.Master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.personalitytypes')}}
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
                    <h3 class="box-title"><?php echo (isset($personalityDetail) && !empty($personalityDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.personalitytype')}}</h3>
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

                <form id="addPersonalityType" class="form-horizontal" method="post" action="{{ url('/developer/savepersonalitytype') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail)) ? $personalityDetail->id : '0' ?>">
                    <div class="box-body">

                        <?php
                        if (old('pt_name'))
                            $pt_name = old('pt_name');
                        elseif ($personalityDetail)
                            $pt_name = $personalityDetail->pt_name;
                        else
                            $pt_name = '';
                        ?>
                        <div class="form-group">
                            <label for="pt_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id=pt_name"" name="pt_name" placeholder="{{trans('labels.formlblname')}}" value="{{$pt_name}}" minlength="3" maxlength="50"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.personalityblheadlogo')}}</label>
                            <div class="col-sm-6">
                                <input type="file" id="pt_logo" name="pt_logo" />
                                <?php
                                if (isset($personalityThumbPath)) {
                                    if (File::exists(public_path($personalityThumbPath . $personalityDetail->pt_logo)) && $personalityDetail->pt_logo != '') {
                                        ?><br>
                                        <img src="{{ url($personalityThumbPath.$personalityDetail->pt_logo) }}" alt="{{$personalityDetail->pt_logo}}" >
                                    <?php } else { ?>
                                        <img src="{{ asset('/backend/images/avatar5.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH'); ?>">
                                    <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        if (old('pt_video'))
                            $pt_video = old('pt_video');
                        elseif ($personalityDetail)
                            $pt_video = $personalityDetail->pt_video;
                        else
                            $pt_video = '';
                        ?>
                        <div class="form-group" id="pt_video">
                            <label for="pt_video" class="col-sm-2 control-label">{{trans('labels.multipleintelligencevideo')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="pit_video" name="pt_video" onblur="validateUrl();" placeholder="{{trans('labels.formlblyoutube')}}" value="{{$pt_video}}" />
                            </div>
                        </div>

                        <?php
                        if (old('pt_information'))
                            $pt_information = old('pt_information');
                        elseif ($personalityDetail)
                            $pt_information = $personalityDetail->pt_information;
                        else
                            $pt_information = '';
                        ?>
                        <div class="form-group" id="pt_information">
                            <label for="pt_information" class="col-sm-2 control-label">{{trans('labels.frmdeveloperinformation')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="pt_information" name="pit_information" placeholder="{{trans('labels.frmwritedeveloperinformation')}}" value="{{$pt_information}}" />
                            </div>
                        </div>

                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($personalityDetail)
                            $deleted = $personalityDetail->deleted;
                        else
                            $deleted = '';
                        ?>

                        <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
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
                        <button id="submit" type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/personalitytype') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function () {

        var validationRules = {
            pt_name: {
                required: true
            },
            deleted: {
                required: true
            }
        }

        $("#addPersonalityType").validate({
            rules: validationRules,
            messages: {
                pt_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });

    function validateUrl()
    {
        $('#submit').prop("disabled", false);
        var url = $('#pit_video').val();
        if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                // Do anything for being valid
                // if need to change the url to embed url then use below line            
                //$('#videoObject').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1&enablejsapi=1');
            } else {
                alert("Youtube Video Url is not valid..");
                $('#submit').attr('disabled', true);

                // Do anything for not being valid
            }
        }
    }
</script>
@stop

