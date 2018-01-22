@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins Buy</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading">
            <div class="container">
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
                    @if ($message = Session::get('error'))
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2 invalid_pass_error">
                            <div class="box-body">
                                <div class="alert alert-error alert-dismissable danger">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                                    {{ $message }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if (count($errors) > 0)
                    <div class="alert alert-danger danger">
                        <strong>{{trans('validation.whoops')}}</strong>
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                        {{trans('validation.someproblems')}}<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <h1 class="font-blue">BUY</h1>
                <p>Request to Parent/Mentor</p>
                <div class="procoin-form">
                    <form id="requestParent" action="{{ url('/teenager/request-parent') }}" method="POST">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Enter valid email of Parent/mentor">
                            <button class="btn btn-submit" type="submit">Send Request to Parent/Mentor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins">
               <div class="icon_arrow"><i class="icon-arrow-spring"></i></div>
                <div class="sec-procoins">
                    <h2>Buy ProCoins</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse suscipit eget massa ac consectetur. </p>
                    <div class="list-procoins">
                        <div class="row flex-container">
                            @if(isset($coinsDetail) && !empty($coinsDetail))
                            <?php $column_count = 1; ?>
                            @foreach($coinsDetail as $key=>$val)
                            <div class="col-sm-6 flex-items">
                                <div class="block-procoins">
                                    <div class="coin-info">
                                        <div class="icon">
                                        <?php
                                            if (isset($val->id) && $val->id != '0') {
                                                $uploadCoinsThumbPath = '/uploads/coins/original/';
                                                if (isset($val->c_image) && $val->c_image != '' && Storage::size($uploadCoinsThumbPath . $val->c_image) > 0) {
                                                    $coinImage = Storage::url($uploadCoinsThumbPath . $val->c_image);
                                                    $altImage = $val->c_image;
                                                } else { 
                                                    $coinImage = Storage::url('frontend/images/proteen_logo.png');
                                                    $altImage = 'Default Image';
                                                }
                                            }
                                        ?>
                                            <img src="{{ $coinImage }}" alt="{{ $altImage }}">
                                        </div>
                                        <h4>{{$val->c_package_name}}</h4>
                                        <h2 class="price">
                                            <span>
                                                &#8377;
                                                <!-- <i class="fa fa-<?php //if ($val->c_currency == 1) { echo 'inr';} else {echo 'usd';}?>" aria-hidden="true"></i> -->
                                            </span>
                                            <?php echo intval($val->c_price); ?>
                                        </h2>
                                        <div class="procoins-value"><?php echo number_format($val->c_coins);?> <span>ProCoins</span>
                                        </div>
                                        <p>{{$val->c_description}}</p>
                                    </div>
                                    <?php $packageId = base64_encode($val->id);?>
                                    <a href="#" title="Buy" class="btn btn-primary" data-toggle="modal" data-target="#buyProCoins" onClick="purchasedCoins('{{$packageId}}', {{$val->c_valid_for}});">Buy</a>
                                </div>
                                
                            </div>
                            <?php
                                $column_count++;
                            ?>
                            @endforeach
                            @else
                                No Packages found.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
    <div class="modal fade" id="buyProCoins" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <h4 class="modal-title">Buy Coins</h4>
                </div>
                <div class="modal-body">
                    <input id="packageId" type="hidden" value="" >
                    <p>This Package valid for <span class="confirm_coins"></span> Days</p>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-next" data-dismiss="modal" onclick="savePurchasedCoinDetails();">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
   <!--  <div id="buyProCoinsModal" title="Buy Coins" style="display:none;">
        <p><span class="confirm_coins"></span></p>
    </div> -->
@stop
   
@section('script')
    <script>
        $(document).ready(function(){
            if ($(".list-procoins .block-procoins").length > 0) {
                $("body").addClass('procoins-buy');
            }
        })
        function purchasedCoins(packageId, days)
        {
            $("#packageId").val(packageId);
            $(".confirm_coins").text(days);
        }
        function savePurchasedCoinDetails()
        {
            var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
            package_id = Base64.decode($("#packageId").val());
            $.ajax({
                url: "{{ url('/teenager/save-coin-purchased-data').'/' }}" + package_id,
                type: 'get',
                data: {
                    "_token": '{{ csrf_token() }}',
                },
            success: function(response) {
                    
                }
            });
        }
    </script>
@stop