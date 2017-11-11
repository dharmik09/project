@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
    <div class="col-md-10">
        {{trans('labels.coupons')}}
    </div>
    <div class="col-md-1">
         <a href="{{ url('admin/add-coupon') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
    </div>
    <div class="col-md-1">
         <a href="{{ url('admin/add-coupon-bulk') }}" class="btn btn-block btn-primary">{{trans('labels.bulk')}}</a>
    </div>
    </h1>
</section>
<section class="content">
    <div class="row">
         <div class="col-md-12">
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-striped" id="coupon_list" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.couponblheadcode')}}</th>
                                <th>{{trans('labels.couponblheadlogo')}}</th>
                                <th>{{trans('labels.couponblheadsponsor')}}</th>
                                <th>{{trans('labels.couponblheadvalidfrom')}}</th>
                                <th>{{trans('labels.couponblheadvalidto')}}</th>
                                <th>Limit</th>
                                <th>No of coupons used</th>
                                <th>{{trans('labels.couponblheadstatus')}}</th>
                                <th>{{trans('labels.couponblheadaction')}}</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($coupons as $coupon)
                                <?php $serialno++; ?>
                                <tr>
                                    <td>
                                        <?php echo $serialno; ?>
                                    </td>
                                    <td>
                                        {{$coupon->cp_code}}
                                    </td>
                                    <td>
                                        <?php
                                            $t_photo = ($coupon->cp_image != "" && Storage::disk('s3')->exists($uploadCouponThumbPath.$coupon->cp_image)) ? Config::get('constant.DEFAULT_AWS').$uploadCouponThumbPath.$coupon->cp_image : asset('/backend/images/proteen_logo.png');
                                        ?>
                                        <img src="{{$t_photo}}" width="50px" height="50px"/>
                                    </td>
                                    <td>
                                        {{$coupon->sp_company_name}}
                                    </td>
                                    <td>
                                        {{date('d/m/Y', strtotime($coupon->cp_validfrom))}}
                                    </td>
                                    <td>
                                        {{date('d/m/Y', strtotime($coupon->cp_validto))}}
                                    </td>
                                    <td>{{$coupon->cp_limit}}</td>
                                    <td>{{$coupon->cp_used}}</td>
                                    <td>
                                        @if ($coupon->deleted == 1)
                                            <i class="s_active fa fa-square"></i>
                                        @else
                                            <i class="s_inactive fa fa-square"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/admin/edit-coupon') }}/{{$coupon->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/delete-coupon') }}/{{$coupon->id}}"><i class="i_delete fa fa-trash"></i></a>
                                    </td>
                                    <td>
                                        <a href="{{ url('/admin/coupon-usage') }}/{{$coupon->id}}"><i class="fa fa-eye"></i> &nbsp;&nbsp;</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"><center>{{trans('labels.norecordfound')}}</center></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    $('#coupon_list').DataTable();
</script>
@stop