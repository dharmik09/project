@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins Buy</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading">
            <div class="container">
                <h1 class="font-blue">BUY</h1>
                <p>Lorem ipsum dolor sit amet.</p>
                <div class="procoin-form">
                    <form>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Enter valid email of Parent/mentor">
                            <button class="btn btn-submit" type="submit">Submit</button>
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
                            <div class="col-sm-6 flex-items">
                                <div class="block-procoins">
                                    <div class="coin-info">
                                        <div class="icon">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <h4>Platinum</h4>
                                        <h2 class="price"><span>&#8377;</span>720</h2>
                                        <div class="procoins-value">350,000 <span>ProCoins</span>
                                        </div>
                                        <p>60 days validity (2x Gold Pack!) • Includes 185,000 ProCoins for free features • Includes 165,000 ProCoins for Paid features (2,5x Gold Pack!)</p>
                                    </div>
                                    <a href="#" title="Buy" class="btn btn-primary">Buy</a>
                                </div>
                            </div>
                            <div class="col-sm-6 flex-items">
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
                            </div>
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