@extends('layouts.sponsor-master')

@section('content')

<div>
    <div class="clearfix" id="errorGoneMsg"> </div>
    <div class="col-xs-12">
        @if ($message = Session::get('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="box-body">
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                        <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                        {{ $message }}
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                           <a href="javascript:void(0);" rel="tooltip" data-title="Dieser Link f�hrt zu Google"  onclick="giftCoins({{$loggedInUser->user()->sp_credit}});" class="btn primary_btn space_btm">Gift ProCoins</a>                        </div>
                    </div>
                </div>
                <!--<div class="coin_summary row clearfix" style="margin-bottom:0;margin-top:10px;">
                  <div class="col-md-6 col-sm-12 col-xs-12 left">
                      <span class="coin_img"><img src="{{Storage::url('frontend/images/available_coin.png')}}" alt=""></span>
                      <span>{{trans('labels.availablecoins')}}</span>
                      <span class="coin_count_ttl"><?php //echo number_format(Auth::sponsor()->get()->sp_credit);?></span>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12 left">
                    <div class="clearfix"><a href="{{url('/sponsor/data-add')}}" class="btn primary_btn cst_pull_right invite_teen_btn">Add Advertisements</a></div>
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
                                <a href="{{ url('/sponsor/edit') }}/{{$acDetails->id}}" class="btn_edit"><i class="fa fa-edit"></i></a> @if($acDetails->sa_type == 3)| <a onclick="getTeenWhoseAppliedForProgram(<?php echo $acDetails->id; ?>)" class="btn_edit" style="cursor:pointer;" title="Applied for Scholarship"><i class="fa fa-users" aria-hidden="true"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty 
                        <tr>
                            <td colspan="11"><?php echo "No records found.."; ?></td>
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
            <div class="close close_next">
                <i class="icon-close" data-dismiss="modal"></i>
            </div>
            <!-- <button type="button" class="close close_next" data-dismiss="modal">Close</button> -->
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Gift Procoins</h2></div>
            <div id="userDataDisplay">

            </div>
        </div>
    </div>
</div>

@if(!empty($coupons))
<div id="rank_list_global" class="modal fade cst_modals">
    <div class="modal-dialog">
        <div class="modal-content rank_list_global">
            <span id="couponCompeting"></span>
        </div>
    </div>
</div>
@endif

<!-- <div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div> -->

<div class="loading-screen loading-wrapper-sub loader-transparent" style="display:none;">
    <div class="loading-content"></div>
</div>

@if(!empty($activityDetail))
<div id="teenager_details" class="modal fade cst_modals" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content rank_list_global">
            <span id="appliedForScholarship"></span>
        </div>
    </div>
</div>
@endif
<div id="confirm" title="Congratulations!" style="display:none;">
    <div class="confirm_coins"></div><br/>
    <div class="confirm_detail"></div>
</div>
@stop
@section('script')

<script type="text/javascript">
    $('.table_container').mCustomScrollbar({axis: "x"});
    function getCouponCompeting(couponId) {
        if (couponId > 0) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = 'couponId=' + couponId;
            $('.loader-transparent').show();
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
                    $('.loader-transparent').hide();
                    $(".table_container_outer").mCustomScrollbar({
                        axis: "yx"
                    });
                }
            });
        }
    }
    $('.table_container').mCustomScrollbar({axis: "x"});
    function getTeenWhoseAppliedForProgram(activityId) {
        if (activityId > 0) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = 'activityId=' + activityId;
            $('.loader-transparent').show();
            $.ajax({
                type: 'get',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/sponsor/get-teenager-whose-applied-for-scholarship')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $("#appliedForScholarship").html(data);
                    $("#teenager_details").modal('show');
                    $('.loader-transparent').hide();
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
                                          ],
                                          open: function(event, ui) {
                                                $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close confirm-close"></i>' );
                                            }
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
                                          ],
                                          open: function(event, ui) {
                                                $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close confirm-close"></i>' );
                                            }
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
                // var path = '<?php echo url('/sponsor/export-pdf/'); ?>';
                // var win = window.open(path, '_blank');
                var windowName = 'Console'; 
                var popUp = window.open('{{url("/sponsor/export-pdf/")}}', windowName, 'width=1000, height=700, left=24, top=24, scrollbars, resizable');
                if (popUp == null || typeof(popUp)=='undefined') {  
                    alert('Please disable your pop-up blocker and click the "Open" link again.'); 
                } 
                else {  
                    popUp.focus();
                }
                //win.focus();
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
    $('.loader-transparent').show();
    $.ajax({
        url: "{{ url('sponsor/gift-coins') }}",
        type: 'post',
        data: {
            "_token": '{{ csrf_token() }}'
        },
        success: function(response) {
           $('.loader-transparent').hide();
           $('#userDataDisplay').html(response);
           $('#gift').modal('show');
        }
    });
}
$(document).on('click','.confirm-close', function(){
    $( "#confirm" ).dialog( "close" );
});
$(document).on('click','.coupon-close', function(){
    $("#rank_list_global").modal("hide");
});
$(document).on('click','.event-close', function(){
    $("#teenager_details").modal("hide");
});
</script>
@stop
