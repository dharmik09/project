@extends('layouts.sponsor-master')

@section('content')

<div>
    <div class="clearfix" id="errorGoneMsg"> </div>
    <div class="col-xs-12">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if ($message = Session::get('error'))
        <div class="col-md-8 col-md-offset-2 invalid_pass_error">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable danger">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="centerlize">
    <div class="container">
        <div class="container_padd">

            <div class="header">
                @if ($days != 0)
                    <div class="promisebtn timer_btn cst_promise_btn cst_report" style="margin-bottom: 10px;">
                        <a href="javascript:void(0);" class="promise btn_golden_border reportbtn" title="" id="report">
                            <span class="promiseplus">Report<i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                            <span class="coinouter">
                                <span class="coinsnum">{{$days}} Days Left</span>
                            </span>
                        </a>
                    </div>
                @else
                    <div id="RdaysReport" style="text-align:right;margin-bottom: 10px;">
                        <div class="promisebtn cst_promise_btn cst_report">
                          <a href="javascript:void(0)" style="margin-top: 0 ;" class="promise btn_golden_border reportbtn" id="report">
                            <span class="promiseplus">Report<i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                            <span class="coinouter">
                                <span class="coinsnum">{{$coins}}</span>
                                <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}">
                                </span>
                            </span>
                          </a>
                        </div>
                    </div>
                @endif
                <div class="button_container coins_button_container">
                    <div class="coin_summary cst_cst_dsh cst_dsh clearfix">
                        <div class="right col-md-3 col-sm-4 col-xs-12">
                            <div class="clearfix"><a href="{{url('/sponsor/data-add')}}" class="btn primary_btn invite_teen_btn">Add Advertisements</a></div>
                        </div>
                        <div class="left col-md-6 col-sm-4 col-xs-12">
                            <span class="coin_img"><img src="{{Storage::url('frontend/images/available_coin.png')}}" alt=""></span>
                            <span>{{trans('labels.availablecoins')}}</span>
                            <span class="coin_count_ttl"><?php echo number_format($loggedInUser->user()->sp_credit);?></span>
                        </div>
                        <div class="dashboard_page col-md-3 col-sm-4 col-xs-12">
                           <a href="javascript:void(0);" rel="tooltip" data-title="Dieser Link führt zu Google"  onclick="giftCoins({{$loggedInUser->user()->sp_credit}});" class="btn primary_btn space_btm">Gift ProCoins</a>                        </div>
                    </div>
                </div>
                <!--<div class="coin_summary row clearfix" style="margin-bottom:0;margin-top:10px;">
                  <div class="col-md-6 col-sm-12 col-xs-12 left">
                      <span class="coin_img"><img src="{{asset('/frontend/images/available_coin.png')}}" alt=""></span>
                      <span>{{trans('labels.availablecoins')}}</span>
                      <span class="coin_count_ttl"><?php //echo number_format(Auth::sponsor()->get()->sp_credit);?></span>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 left">
                    <div class="clearfix"><a href="{{url('/sponsor/dataAdd')}}" class="btn primary_btn cst_pull_right invite_teen_btn">Add Advertisements</a></div>
                  </div>
                </div>-->
            </div>
            <div class="table_container">
                <table class="sponsor_table">
                    <tr>
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
                        <th>{{trans('labels.sponsorDashboardActions')}}</th>
                    </tr>
                    <?php
                    $serialno = 0;
                    if (isset($activityDetail) && !empty($activityDetail)) {
                        ?>
                        @forelse($activityDetail as $acDetails)
                        <?php $serialno++; ?>
                        <tr>
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
                                <?php $saImage = ($acDetails->sa_image != '') ? Storage::url($saThumbImagePath.$acDetails->sa_image) : Storage::url($saThumbImagePath.'proteen-logo.png'); ?>
                                <img src="{{ $saImage }}" alt="Default Image" height="50px" width="50px"/>
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
                            <td>
                                <a href="{{ url('/sponsor/edit') }}/{{$acDetails->id}}" class="btn_edit"><i class="fa fa-edit"></i></a>
                            </td>
                        </tr>
                        @empty 
                        <tr>
                            <td colspan="10"><?php echo "No records found.."; ?></td>
                        </tr>    
                        @endforelse
                    <?php } ?>
                </table>
            </div>

            <!-- Coupon Table -->
            <div class="header" style="margin-top: 30px;">
                <div class="credit">
                    <span style="font-size: 20px;">Coupons</span>
                </div>     
                <div class="button_container">
                    <a href="{{url('/sponsor/add-coupon')}}" class="btn primary_btn small_btn space_btm">Add Coupons</a>
                </div>                
            </div>

            <div class="table_container">
                <table class="sponsor_table">
                    <tr>
                        <th>{{trans('labels.sponsorDashboardSrNo')}}</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>{{trans('labels.sponsorDashboardImage')}}</th>
                        <th>Limit</th>
                        <th>ProCoins Used</th>
                        <th>{{trans('labels.sponsorDashboardStartDate')}}</th>
                        <th>{{trans('labels.sponsorDashboardEndDate')}}</th>
                        <th>{{trans('labels.sponsorDashboardStatus')}}</th>
                        <th>{{trans('labels.sponsorDashboardActions')}}</th>

                    </tr>
                    <?php
                    $serialno = 0;
                    ?>
                    @forelse($coupons as $coupon)
                    <?php $serialno++; ?>
                    <tr>
                        <td><?php echo $serialno; ?></td>
                        <td>{{$coupon->cp_code}}</td>
                        <td>{{$coupon->cp_description}}</td>
                        <td>
                            <?php $cpImage = ($coupon->cp_image != '') ? Storage::url($couponThumbImagePath . $coupon->cp_image) : Storage::url($couponThumbImagePath.'proteen-logo.png'); ?>
                            <img src="{{ $cpImage }}" class="user-image" alt="Default Image" height="50px" width="50px">
                        </td>
                        <td>{{$coupon->cp_limit}}</td>
                        <td>{{$coupon->cp_credit_used}}</td>
                        <td>{{date('d/m/Y', strtotime($coupon->cp_validfrom))}}</td>
                        <td>{{date('d/m/Y', strtotime($coupon->cp_validto))}}</td>
                        <?php $active = $coupon->deleted; ?>
                        <td>
                            {{$active == 1?'Active':'Inactive'}}
                        </td>
                        <td>
                            <a href="{{ url('sponsor/edit-coupon') }}/{{$coupon->id}}" class="btn_edit" title="Edit"><i class="fa fa-edit"></i></a>
                            | <a onclick="getCouponCompeting(<?php echo $coupon->id; ?>)" class="btn_edit" style="cursor:pointer;" title="Coupon usage"><i class="fa fa-users" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    @empty 
                    <tr>
                        <td colspan="11"><?php echo "No record found..."; ?></td>
                    </tr>
                    @endforelse                   
                </table>
            </div>            
        </div>
    </div>
</div>
<div class="modal fade default_popup" id="gift">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close close_next" data-dismiss="modal">Close</button>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Gift Procoins</h2></div>
            <div id="userData">

            </div>
        </div>
    </div>
</div>

@if(!empty($coupons))
<div id="rank_list_global" class="modal fade cst_modals" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content rank_list_global">
            <span id="couponCompeting"></span>
        </div>
    </div>
</div>
@endif

<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>

@stop
@section('script')

<script type="text/javascript">
    $('.table_container').mCustomScrollbar({axis: "x"});
                                function getCouponCompeting(couponId) {
                                    if (couponId > 0) {
                                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                                        var form_data = 'couponId=' + couponId;
                                        $('.ajax-loader').show();
                                        $.ajax({
                                            type: 'get',
                                            data: form_data,
                                            dataType: 'html',
                                            url: "{{ url('/sponsor/get-coupon-competing')}}",
                                            headers: {
                                                'X-CSRF-TOKEN': CSRF_TOKEN
                                            },
                                            cache: false,
                                            success: function(data) {
                                                $("#couponCompeting").html(data);
                                                $("#rank_list_global").modal('show');
                                                $('.ajax-loader').hide();
                                                $(".table_container_outer").mCustomScrollbar({
                                                    axis: "yx"
                                                });
                                            }
                                        });
                                    }
                                }
$(document).on('click', '#report', function (e) {

    var sponsor_id = '{{ $loggedInUser->user()->id }}';
    $.ajax({
        url: "{{ url('/sponsor/get-available-coins') }}",
        type: 'POST',
        data: {
            "_token": '{{ csrf_token() }}',
            "sponsorId": sponsor_id
        },
        success: function(response) {
            days = response;
            $.ajax({
                url: "{{ url('/sponsor/get-available-coins-for-sponsor') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "sponsorId": '{{ $loggedInUser->user()->id }}'
                },
                success: function(response) {
                    coins = response;
                    $.ajax({
                        url: "{{ url('/sponsor/get-coins-for-sponsor') }}",
                        type: 'POST',
                        data: {
                            "_token": '{{ csrf_token() }}',
                            "sponsorId": '{{ $loggedInUser->user()->id }}'
                        },
                        success: function(response) {
                            if (response > 1) {
                                if (days == 0) {
                                    $(".confirm_coins").text('<?php echo 'You have '; ?>' + format(response) + '<?php echo ' ProCoins available.'; ?>');
                                    $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(coins) + '<?php echo ' ProCoins and play on'; ?>');
                                    $.ui.dialog.prototype._focusTabbable = function(){};
                                    $( "#confirm" ).dialog({

                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    draggable: false,
                                    modal: true,
                                    buttons: [
                                            {
                                                text: "Ok",
                                                class : 'btn primary_btn',
                                                click: function() {
                                                   showReport(days);
                                                   $( this ).dialog( "close" );
                                                  //$(".confirm_coins").text(' ');
                                                }
                                            },
                                            {
                                                text: "Cancel",
                                                class : 'btn primary_btn',
                                                click: function() {
                                                  $( this ).dialog( "close" );
                                                  $(".confirm_coins").text(' ');
                                                }
                                            }
                                          ]
                                    });
                                } else {
                                    showReport(days);
                                }

                            } else {
                                if (days != 0) {
                                    showReport(days);
                                } else {
                                    $("#confirm").attr('title', 'Notification!');
                                    $(".confirm_coins").text('Insufficient ProCoins! You need to buy a ProCoins Package to proceed');
                                    $.ui.dialog.prototype._focusTabbable = function(){};
                                    $( "#confirm" ).dialog({

                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    draggable: false,
                                    modal: true,
                                    buttons: [
                                            {
                                                text: "Buy",
                                                class : 'btn primary_btn',
                                                click: function() {
                                                    var path = '<?php echo url('/sponsor/mycoins/'); ?>';
                                                    location.href = path;
                                                    $( this ).dialog( "close" );
                                                  //$(".confirm_coins").text(' ');
                                                }
                                            },
                                            {
                                                text: "Cancel",
                                                class : 'btn primary_btn',
                                                click: function() {
                                                  $( this ).dialog( "close" );
                                                  $(".confirm_coins").text(' ');
                                                }
                                            }
                                          ]
                                    });
                                }
                            }
                        }
                    });
                }
            });
        }
    });
});

function showReport(days) {
    $.ajax({
          url: "{{ url('/sponsor/purchased-coins-to-view-report') }}",
          type: 'POST',
          data: {
              "_token": '{{ csrf_token() }}',
              "sponsorId": '{{ $loggedInUser->user()->id }}'
          },
          success: function(response) {
                var path = '<?php echo url('/sponsor/export-pdf/'); ?>';
                var win = window.open(path, '_blank');
                win.focus();
                if (days == 0){
                    getRemaningDaysForReport('{{ $loggedInUser->user()->id }}');
                }
          }
      });
}

function getRemaningDaysForReport(parent_id) {
    $.ajax({
        url: "{{ url('/sponsor/get-remainig-days-for-sponsor') }}",
        type: 'POST',
        data: {
            "_token": '{{ csrf_token() }}',
            "sponsorId": '{{ $loggedInUser->user()->id }}'
        },
        success: function(response) {
           $('#RdaysReport').html(response);
           $('#RdaysReport').show();
        }
    });
}

function format(x) {
    return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function giftCoins(coins)
{
    if(coins == 0){
        window.scrollTo(0,0);
        if($("#useForClass").hasClass('r_after_click')){
            $("#errorGoneMsg").html('');
        }
        $("#errorGoneMsg").append("<div class='col-md-8 col-md-offset-2 r_after_click' id='useForClass'><div class='box-body'><div class='alert alert-error danger'><button aria-hidden='true' data-dismiss='alert' class='close' type='button'>X</button><span class='fontWeight'>Hey! You can only gift from what you have!</span></div></div></div>");
        return false;
    }
    $('.ajax-loader').show();
    $.ajax({
        url: "{{ url('sponsor/gift-coins') }}",
        type: 'post',
        data: {
            "_token": '{{ csrf_token() }}'
        },
        success: function(response) {
           $('.ajax-loader').hide();
           $('#userData').html(response);
           $('#gift').modal('show');
        }
    });

}

</script>
@stop
