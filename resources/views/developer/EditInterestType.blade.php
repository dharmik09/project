@extends('developer.Master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.interesttypes')}}
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
                    <h3 class="box-title"><?php echo (isset($interestDetail) && !empty($interestDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.interesttype')}}</h3>
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

                <form id="addInterestType" class="form-horizontal" method="post" action="{{ url('/developer/saveinteresttype') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($ci_image) && !empty($it_image)) ? $it_image : '' ?>">
                    <input type="hidden" name="id" value="<?php echo (isset($interestDetail) && !empty($interestDetail)) ? $interestDetail->id : '0' ?>">
                    <div class="box-body">

                    <?php
                    if (old('it_name'))
                        $it_name = old('it_name');
                    elseif ($interestDetail)
                        $it_name = $interestDetail->it_name;
                    else
                        $it_name = '';
                    ?>
                    <div class="form-group">
                        <label for="it_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id=it_name"" name="it_name" placeholder="{{trans('labels.formlblname')}}" value="{{$it_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.interestblheadlogo')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="it_logo" name="it_logo" />
                            <?php  
                                if(isset($interestThumbPath)){ 
                                    if(File::exists(public_path($interestThumbPath.$interestDetail->it_logo)) && $interestDetail->it_logo != '') { ?><br>
                                        <img src="{{ url($interestThumbPath.$interestDetail->it_logo) }}" alt="{{$interestDetail->it_logo}}" >
                                    <?php }else{ ?>
                                        <img src="{{ asset('/backend/images/avatar5.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');?>" width="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');?>">
                                <?php   }
                                    }
                                ?>
                        </div>
                    </div>


                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($interestDetail)
                        $deleted = $interestDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/interesttype') }}">{{trans('labels.cancelbtn')}}</a>
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

            var validationRules = {
                it_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addInterestType").validate({
            rules : validationRules,
            messages : {
                it_name : {
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

