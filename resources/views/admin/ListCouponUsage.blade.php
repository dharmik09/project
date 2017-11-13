@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
    <div class="col-md-10">
        {{$couponName->cp_code}} ---
        Total <strong>{{count($couponUsage)}}</strong> coupons used        
    </div>    
    </h1>
    <br/>
</section>

<section class="content">
    <div class="row">
         <div class="col-md-12">
            
        </div>
        <div class="col-md-12">
            <a href=" {{ url('admin/coupons') }} ">Back</a>
            <div class="box box-primary">
                
                <div class="box-header">
                </div>
                <div class="box-body">
                    <table class="table table-striped" id="coupon_usage_list" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Email</th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($couponUsage as $coupon)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$coupon->t_name}}
                                </td>
                                <td>
                                    <?php
                                        $t_photo = ($coupon->t_photo != "" && Storage::disk('s3')->exists(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$coupon->t_photo)) ? Config::get('constant.DEFAULT_AWS').Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$coupon->t_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$t_photo}}" title="Proteen-Coupon-User" width="50px" height="50px"/>
                                </td>
                                <td>
                                    {{$coupon->tcu_consumed_email}}
                                </td>                                                        
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4"><center>{{trans('labels.norecordfound')}}</center></td>
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
    $('#coupon_usage_list').DataTable();
</script>
@stop