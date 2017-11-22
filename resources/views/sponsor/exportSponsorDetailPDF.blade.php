<html>
<head>
<style>
    @page { margin: 90px 50px; }
    #header { position: fixed; top: -80px; right: 0px;  height: 60px; border-bottom: 1px solid;padding-bottom: 15px;}
    .clearfix {
        clear: both;
    }
    #footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 40px; border-top: 1px solid;}
    #footer .page:after { content: counter(page); padding-left : 390px;}
</style>
</head>
<body>

<div id="header">
    <table>
        <tr>
            <td width="80px">
                <div><img src="{{ Storage::url('frontend/images/proteen_logo.png')}}" alt="" width="58px"/></div>
            </td>
            <td>
                <span><h2>SPONSOR ANALYTICS</h2></span>
            </td>
        </tr>
    </table>
</div>

<div id="footer" class="clearfix">
     <p class="page">Copyright &copy; <?php echo date('Y');?> <span style="color:#E66A45;"> ProTeen</span>. All rights reserved.</p>
</div>

<div class="clearfix"></div>
<div>
    <table>
        <tr>
            <td width="50px">
                <div><img src="{{Storage::url($logo)}}" alt="" width="60px" height="60px"/></div>
            </td>
            <td width="600px">
                <div style="text-align:center; font-size: 24px;font-weight: bold; padding-top:10px;"> {{Auth::guard('sponsor')->user()->sp_company_name}}</div>
            </td>
        </tr>
    </table>
</div>

<div style="text-align:right;"><h3>Date :&nbsp;<?php echo date('F jS, Y');?></h3></div>

<div>
    <div>
        <span style="font-size: 20px; font-weight:bold;">{{trans('labels.availableCredits')}} : &nbsp;<span style="color: #e56a45;">{{Auth::guard('sponsor')->user()->sp_credit}}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="font-size: 20px; font-weight:bold;">Total ProCoins Used : &nbsp;<span style="color: #e56a45;">{{$usedCredits}}</span></span>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1" width="720px">
        <tr align="center">
            <th>{{trans('labels.sponsorDashboardSrNo')}}</th>
            <th>{{trans('labels.sponsorDashboardName')}}</th>
            <th>{{trans('labels.sponsorDashboardType')}}</th>
            <th>{{trans('labels.sponsorDashboardImage')}}</th>
            <th>{{trans('labels.sponsorDashboardApplyLevel')}}</th>
            <th>{{trans('labels.sponsorDashboardLocation')}}</th>
            <th>ProCoins Used</th>
            <th>{{trans('labels.sponsorDashboardStartDate')}}</th>
            <th>{{trans('labels.sponsorDashboardEndDate')}}</th>
            <th>{{trans('labels.sponsorDashboardStatus')}}</th>
        </tr>
        <?php
        $serialno = 0;
        if (isset($activityDetail) && !empty($activityDetail)) {
            ?>
            @forelse($activityDetail as $acDetails)
            <?php $serialno++; ?>
            <tr align="center">
                <td><?php echo $serialno; ?></td>
                <td>{{$acDetails->sa_name}}</td>
                <td>
                    <?php $t_id = $acDetails->sa_type; ?>
                    <?php $type = Helpers::type(); ?>
                    <?php
                    foreach ($type as $key => $value) {
                        if ($key == $t_id) {
                            ?>
                            {{$value}}
                            <?php
                            break;
                        }
                    }
                    ?>
                </td>

                <td>
                    <?php if (isset($acDetails->sa_image) && $acDetails->sa_image != '') { ?>
                        <img src="{{ Storage::url($saThumbImagePath.$acDetails->sa_image)}}" />
                    <?php } else { ?>
                        <img src="{{ Storage::url($saThumbImagePath.'proteen-logo.png')}}" class="user-image" alt="Default Image">
                    <?php } ?>
                </td>

                <td>{{($acDetails->sl_name == '')?'All Level':$acDetails->sl_name}}</td>
                <td>{{$acDetails->sa_location}}</td>
                <td>{{$acDetails->sa_credit_used}}</td>
                <td>{{date('d/m/Y', strtotime($acDetails->sa_start_date))}}</td>
                <td>{{date('d/m/Y', strtotime($acDetails->sa_end_date))}}</td>
                <?php $active = $acDetails->deleted; ?>
                <td>
                    {{$active == 1?'Active':'Inactive'}}
                </td>
            </tr>
            @empty
            <tr  align="center">
                <td colspan="10"><?php echo "No records found.."; ?></td>
            </tr>
            @endforelse
        <?php } ?>
    </table>
</div>

<div style="margin-top: 30px;">
    <div>
        <span style="font-size: 20px; font-weight:bold;">Coupons</span>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1">
        <tr align="center">
            <th>{{trans('labels.sponsorDashboardSrNo')}}</th>
            <th>Code</th>
            <th>Description</th>
            <th>{{trans('labels.sponsorDashboardImage')}}</th>
            <th>Limit</th>
            <th>ProCoins Used</th>
            <th>{{trans('labels.sponsorDashboardStartDate')}}</th>
            <th>{{trans('labels.sponsorDashboardEndDate')}}</th>
            <th>{{trans('labels.sponsorDashboardStatus')}}</th>
        </tr>
        <?php
        $serialno = 0;
        ?>
        @forelse($coupons as $coupon)
        <?php $serialno++; ?>
        <tr align="center">
            <td><?php echo $serialno; ?></td>
            <td>{{$coupon->cp_code}}</td>
            <td>{{$coupon->cp_description}}</td>
            <td>
                <?php if (isset($coupon->cp_image) && $coupon->cp_image != '') { ?>
                    <img src="{{ Storage::url($couponThumbImagePath.$coupon->cp_image)}}" />
                <?php } else { ?>
                    <img src="{{ Storage::url($couponThumbImagePath.'proteen-logo.png')}}" class="user-image" alt="Default Image">
                <?php } ?>
            </td>
            <td>{{$coupon->cp_limit}}</td>
            <td>{{$coupon->cp_credit_used}}</td>
            <td>{{date('d/m/Y', strtotime($coupon->cp_validfrom))}}</td>
            <td>{{date('d/m/Y', strtotime($coupon->cp_validto))}}</td>
            <?php $active = $coupon->deleted; ?>
            <td>
                {{$active == 1?'Active':'Inactive'}}
            </td>
        </tr>
        @empty
        <tr  align="center">
            <td colspan="10"><?php echo "No record found..."; ?></td>
        </tr>
        @endforelse
    </table>
</div>


<?php
foreach ($couponsData AS $key => $value) {
    $couponNameD = $value['couponNameD'];
    $couponsDetail = $value['coupons']
    ?>
    <div style="margin-top: 30px;">
        <span style="font-size: 20px; font-weight:bold;">{{ucfirst($couponNameD)}} &nbsp; - &nbsp; Total {{count($couponsDetail)}} coupons used</span>
        <div style="margin-top: 10px;">
        <table border="1" width="500px">
            <tr>
                <th>Icon</th>
                <th>Name</th>
                <th>Use Type</th>
            </tr>
            <?php
            if (isset($couponsDetail) && !empty($couponsDetail)) {
                foreach ($couponsDetail as $competingValue) {
                    ?>
                    <tr align="center">
                        <td>
                            <span>
                                <?php
                                    $teenPhoto = $competingValue->t_photo;
                                    if (isset($teenPhoto) && $teenPhoto != '') {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenPhoto);
                                    } else {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                    }
                                ?>
                                <img src="{{$t_photo}}" title="Proteen-Coupon-User"/>
                            </span>
                        </td>
                        <td>{{ucfirst($competingValue->t_name)}}</td>
                        <td>
                            <?php
                                if($competingValue->tcu_type == "gift"){ echo "Gifted"; }else{ echo "Own Use"; }
                            ?>
                        </td>
                    </tr>

                    <?php
                }
            } else {
                echo "<tr  align='center'><td colspan='5'>No record found</td></tr>";
            }
            ?>
        </table>
    </div>
    </div>
    <?php
}?>

<div style="margin-top: 30px;">
    <div>
        <span style="font-size: 20px; font-weight:bold;">ProCoins</span>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1" width="650px" cellpadding="7px">
        <tr align="center">
            <th>{{trans('labels.component')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
        @if(!empty($deductedCoinsDetail))
        @foreach($deductedCoinsDetail as $key=>$data)
        <tr align="center">
            <td>
                {{$data->pc_element_name}}
            </td>
            <td>
                <?php echo number_format($data->dc_total_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr  align="center"><td colspan="4"><?php echo "No record found..."; ?></td></tr>
        @endif
    </table>
</div>

<div style="margin-top: 30px;">
    <div>
        <span style="font-size: 20px; font-weight:bold;">Transaction History</span>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1" width="700px" cellpadding="7px">
        <tr align="center">
            <th>{{trans('labels.teentblheadname')}}</th>
            <th>{{trans('labels.emaillbl')}}</th>
            <th>{{trans('labels.transectionid')}}</th>
            <th>{{trans('labels.paidamount')}}</th>
            <th>{{trans('labels.formcurrency')}}</th>
            <th>{{trans('labels.formlblcoins')}}</th>
            <th>{{trans('labels.transectiondate')}}</th>
        </tr>
        @if(!empty($transactionDetail))
        @foreach($transactionDetail as $key=>$data)
        <tr align="center">
            <td>
                {{$data->t_name}}
            </td>
            <td>
                {{$data->tn_email}}
            </td>
            <td>
                {{$data->tn_transaction_id}}
            </td>
            <td>
                {{$data->tn_amount}}
            </td>
            <td>
                @if($data->tn_currency == 1)
                INR
                @else
                USD
                @endif
            </td>
            <td>
                <?php echo number_format($data->tn_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->created_at)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr align="center"><td colspan="7">No record found</td></tr>
        @endif
    </table>
</div>

</body>
</html>