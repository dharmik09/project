@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.paincomponenets')}}
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
                    <h3 class="box-title"><?php echo (isset($paidComponentsDetail) && !empty($paidComponentsDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.paincomponenets')}}</h3>
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
                <form id="addPaidComponents" class="form-horizontal" method="post" action="{{ url('/admin/savePaidComponents') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($paidComponentsDetail) && !empty($paidComponentsDetail)) ? $paidComponentsDetail->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('pc_element_name'))
                        $pc_element_name = old('pc_element_name');
                    elseif ($paidComponentsDetail)
                        $pc_element_name = $paidComponentsDetail->pc_element_name;
                    else
                        $pc_element_name = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_element_name" class="col-sm-2 control-label">{{trans('labels.formlblelementname')}}</label>
                        <div class="col-sm-6">
                            <?php $Name = Helpers::getAllElememtName();?>
                            <select class="form-control" id="pc_element_name" name="pc_element_name">
                                    <option value="">{{trans('labels.formlblselectelementname')}}</option>
                                     <?php foreach ($Name as $key => $value) {
                                        ?>
                                            <option value="{{$value}}" <?php if($pc_element_name == $value) echo 'selected'; ?>>{{$value}}</option>
                                        <?php
                                        }
                                    ?>
                            </select>
                        </div>
                    </div>

                    <?php
                    if (old('pc_required_coins'))
                        $pc_required_coins = old('pc_required_coins');
                    elseif ($paidComponentsDetail)
                        $pc_required_coins = $paidComponentsDetail->pc_required_coins;
                    else
                        $pc_required_coins = '';
                    ?>

                    <div class="form-group">
                        <label for="pc_required_coins" class="col-sm-2 control-label">{{trans('labels.formlblrequiredcoins')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pc_required_coins" name="pc_required_coins" placeholder="{{trans('labels.formlblrequiredcoins')}}" value="{{$pc_required_coins}}"/>
                        </div>
                    </div>

                    <?php
                    if (old('pc_is_paid'))
                        $pc_is_paid = old('pc_is_paid');
                    elseif ($paidComponentsDetail)
                        $pc_is_paid = $paidComponentsDetail->pc_is_paid;
                    else
                        $pc_is_paid = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_is_paid" class="col-sm-2 control-label">{{trans('labels.formlblpaidornot')}}</label>
                        <div class="col-sm-6">
                            <input type="checkbox" value="1" name="pc_is_paid" id="pc_is_paid" <?php
                            if ($pc_is_paid) {
                                echo 'checked="cehcked"';
                            }
                            ?>/>
                        </div>
                    </div>

                    <?php
                    if (old('pc_valid_upto'))
                        $pc_valid_upto = old('pc_valid_upto');
                    elseif ($paidComponentsDetail)
                        $pc_valid_upto = $paidComponentsDetail->pc_valid_upto;
                    else
                        $pc_valid_upto = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_valid_upto" class="col-sm-2 control-label">{{trans('labels.formlblvalidupto')}}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control numeric" id="pc_valid_upto" name="pc_valid_upto" placeholder="{{trans('labels.formlblvalidupto')}}" value="{{$pc_valid_upto}}"/>
                        </div>
                        <label for="c_valid_for" class="col-sm-1 control-label">{{trans('labels.formlbldays')}}</label>
                    </div>

                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($paidComponentsDetail)
                        $deleted = $paidComponentsDetail->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/paidComponents') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
        var validationRules = {
            pc_element_name : {
                required : true
            },
            pc_required_coins : {
                required : true
            },
            pc_valid_upto : {
                required : true
            },
            deleted : {
                required : true
            }
        }

        $("#addPaidComponents").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                pc_element_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pc_required_coins : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pc_valid_upto : {
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
