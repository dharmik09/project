@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.lblcoinpackage')}}
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
                    <h3 class="box-title"><?php echo (isset($coinsDetail) && !empty($coinsDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.lblcoinpackage')}}</h3>
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

                <form id="addCoins" class="form-horizontal" method="post" action="{{ url('/admin/saveCoins') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($coinsDetail) && !empty($coinsDetail)) ? $coinsDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_image" value="<?php echo (isset($coinsDetail) && !empty($coinsDetail)) ? $coinsDetail->c_image : '' ?>">
                    <div class="box-body">

                    <?php
                    if (old('c_package_name'))
                        $c_package_name = old('c_package_name');
                    elseif ($coinsDetail)
                        $c_package_name = $coinsDetail->c_package_name;
                    else
                        $c_package_name = '';
                    ?>
                    <div class="form-group">
                        <label for="c_package_name" class="col-sm-2 control-label">{{trans('labels.packagename')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="c_package_name" name="c_package_name" placeholder="{{trans('labels.packagename')}}" value="{{$c_package_name}}"/>
                        </div>
                    </div>

                    <?php
                    if (old('c_coins'))
                        $c_coins = old('c_coins');
                    elseif ($coinsDetail)
                        $c_coins = $coinsDetail->c_coins;
                    else
                        $c_coins = '';
                    ?>
                    <div class="form-group">
                        <label for="c_coins" class="col-sm-2 control-label">{{trans('labels.totalcoins')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control numeric" id="c_coins" name="c_coins" placeholder="{{trans('labels.totalcoins')}}" value="{{$c_coins}}"/>
                        </div>
                    </div>

                    <?php
                    if (old('c_currency'))
                        $c_currency = old('c_currency');
                    elseif ($coinsDetail)
                        $c_currency = $coinsDetail->c_currency;
                    else
                        $c_currency = '';
                    ?>
                    <div class="form-group">
                        <label for="c_currency" class="col-sm-2 control-label">{{trans('labels.formcurrency')}}</label>
                        <div class="col-sm-6">
                            <?php $Currency = Helpers::getAllCurrency();?>
                            <select class="form-control" id="c_currency" name="c_currency">
                                    <option value="">{{trans('labels.formlblselectcurrency')}}</option>
                                     <?php foreach ($Currency as $key => $value) {
                                        ?>
                                            <option value="{{$key}}" <?php if($c_currency == $key) echo 'selected'; ?>>{{$value}}</option>
                                        <?php
                                        }
                                    ?>
                            </select>
                        </div>
                    </div>

                    <?php
                    if (old('c_price'))
                        $c_price = old('c_price');
                    elseif ($coinsDetail)
                        $c_price = $coinsDetail->c_price;
                    else
                        $c_price = '';
                    ?>
                    <div class="form-group">
                        <label for="c_price" class="col-sm-2 control-label">{{trans('labels.price')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control numeric" id="c_price" name="c_price" placeholder="{{trans('labels.price')}}" value="{{$c_price}}"/>
                        </div>
                    </div>

                    <?php
                    if (old('c_user_type'))
                        $c_user_type = old('c_user_type');
                    elseif ($coinsDetail)
                        $c_user_type = $coinsDetail->c_user_type;
                    else
                        $c_user_type = '';
                    ?>
                    <div class="form-group">
                        <label for="c_user_type" class="col-sm-2 control-label">{{trans('labels.formusertype')}}</label>
                        <div class="col-sm-6">
                            <?php $UserType = Helpers::getAllUserTypes();?>
                            <select class="form-control" id="c_user_type" name="c_user_type">
                                    <option value="">{{trans('labels.formlblselectusertype')}}</option>
                                     <?php foreach ($UserType as $key => $value) {
                                        ?>
                                            <option value="{{$key}}" <?php if($c_user_type == $key) echo 'selected'; ?>>{{$value}}</option>
                                        <?php
                                        }
                                    ?>
                            </select>
                        </div>
                    </div>


                    <?php
                    if (old('c_valid_for'))
                        $c_valid_for = old('c_valid_for');
                    elseif ($coinsDetail)
                        $c_valid_for = $coinsDetail->c_valid_for;
                    else
                        $c_valid_for = '';
                    ?>
                    <div class="form-group">
                        <label for="c_valid_for" class="col-sm-2 control-label">{{trans('labels.formlblvalidupto')}}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control numeric" id="c_valid_for" name="c_valid_for" placeholder="{{trans('labels.formlblvalidupto')}}" value="{{$c_valid_for}}"/>
                        </div>
                        <label for="c_valid_for" class="col-sm-1 control-label">{{trans('labels.formlbldays')}}</label>
                    </div>

                    <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="c_image" name="c_image"/>
                                <?php
                                if (isset($coinsDetail->id) && $coinsDetail->id != '0') {
                                    $image = ($coinsDetail->c_image != "" && Storage::disk('s3')->exists($uploadCoinsThumbPath.$coinsDetail->c_image)) ? Config::get('constant.DEFAULT_AWS').$uploadCoinsThumbPath.$coinsDetail->c_image : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                            </div>
                        </div>

                    <?php
                        if (old('c_description'))
                            $c_description = old('c_description');
                        elseif ($coinsDetail)
                            $c_description = $coinsDetail->c_description;
                        else
                            $c_description = '';
                    ?>
                    <div class="form-group">
                        <label for="c_description" class="col-sm-2 control-label">{{trans('labels.formlbldescription')}}</label>
                            <div class="col-sm-6">
                                <textarea name='c_description' id='c_description' rows="3"cols="84" placeholder="{{trans('labels.formlbldescription')}}">{{ $c_description }}</textarea>
                            </div>
                    </div>

                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($coinsDetail)
                        $deleted = $coinsDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>
                                </select>
                            </div>
                    </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/coins') }}">{{trans('labels.cancelbtn')}}</a>
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
        <?php if(isset($coinsDetail->id) && $coinsDetail->id != '0') { ?>
            var validationRules = {
                c_coins : {
                    required : true,
                },
                c_price : {
                    required : true,
                    min : 1
                },
                c_currency : {
                    required : true
                },
                c_user_type : {
                    required : true
                },
                c_package_name : {
                    required : true
                },
                c_description : {
                    required : true
                },
                c_valid_for : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                c_coins : {
                    required : true,
                },
                c_price : {
                    required : true,
                    min : 1
                },
                c_currency : {
                    required : true
                },
                c_user_type : {
                    required : true
                },
                c_package_name : {
                    required : true
                },
                c_description : {
                    required : true
                },
                c_valid_for : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addCoins").validate({
            rules : validationRules,
            messages : {
                c_coins : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_price : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_currency : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_user_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_package_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_description : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                c_valid_for : {
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