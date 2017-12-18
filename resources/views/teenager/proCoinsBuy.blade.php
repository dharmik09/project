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
                                                if (isset($val->c_image) && $val->c_image != '') {
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
                                            <i class="fa fa-<?php if ($val->c_currency == 1) { echo 'inr';} else {echo 'usd';}?>" aria-hidden="true"></i>
                                            <span><?php echo intval($val->c_price); ?></span>
                                        </h2>
                                        <div class="procoins-value"><?php echo number_format($val->c_coins);?> <span>ProCoins</span>
                                        </div>
                                        <p>{{$val->c_description}}</p>
                                    </div>
                                    <a href="#" title="Buy" class="btn btn-primary">Buy</a>
                                </div>
                                
                            </div>
                            <?php
                                    $column_count++;
                                ?>
                                @endforeach
                                @else
                                    No Packages found.
                                @endif
                            <!-- <div class="col-sm-6 flex-items">
                                <div class="block-procoins">
                                    <div class="coin-info">
                                        <div class="icon">
                                            <i class="icon-gold"></i>
                                        </div>
                                        <h4>Gold</h4>
                                        <h2 class="price"><span>&#8377;</span>360</h2>
                                        <div class="procoins-value">250,000 <span>ProCoins</span>
                                        </div>
                                        <p>30 days validity • Includes 185,000 ProCoins for free features • Includes 65,000 ProCoins for Paid features</p>
                                    </div>
                                    <a href="#" title="Buy" class="btn btn-primary">Buy</a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
@stop
   
@section('script')
    <script>
        $(document).ready(function(){
            if ($(".list-procoins .block-procoins").length > 0) {
                $("body").addClass('procoins-buy');
            }
        })
    </script>
@stop