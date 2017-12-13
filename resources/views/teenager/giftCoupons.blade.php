@extends('layouts.teenager-master')

@push('script-header')
    <title>Gift Coupons</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading coupons-heading gift-heading">
            <div class="container">
                <h1 class="font-blue">gift coupons</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean pretium pellentesque commodo.</p>
                <div class="procoin-form gift-form coupon-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--coupons sec-->
        <section class="sec-gift-coupon">
            <div class="container">
                <div class="bg-white">
                    <div class="member-list">
                        <div class="member-block">
                            <div class="img" style="background-image: url('{{ Storage::url('img/ellen.jpg') }}'"><!-- --></div>
                            <span class="name">Ellen Ripley</span>
                            <div class="brdr"><!-- --></div>
                            <a href="#" class="btn" title="Gift">gift</a>
                        </div>
                        <div class="member-block">
                            <div class="img" style="background-image: url('{{ Storage::url('img/alex.jpg') }}'"><!-- --></div>
                            <span class="name">Alex Murphy</span>
                            <div class="brdr"><!-- --></div>
                            <a href="#" class="btn" title="Gift">gift</a>
                        </div>
                        <div class="member-block">
                            <div class="img" style="background-image: url('{{ Storage::url('img/diana.jpg') }}'"><!-- --></div>
                            <span class="name">Diana Prince</span>
                            <div class="brdr"><!-- --></div>
                            <a href="#" class="btn" title="Gift">gift</a>
                        </div>
                        <div class="member-block">
                            <div class="img" style="background-image: url('{{ Storage::url('img/peter.jpg') }}'"><!-- --></div>
                            <span class="name">Peter Parker</span>
                            <div class="brdr"><!-- --></div>
                            <a href="#" class="btn" title="Gift">gift</a>
                        </div>
                        <div class="member-block">
                            <div class="img" style="background-image: url('{{ Storage::url('img/rico.jpg') }}'"><!-- --></div>
                            <span class="name">Rico Frost</span>
                            <div class="brdr"><!-- --></div>
                            <a href="#" class="btn" title="Gift">gift</a>
                        </div>
                        <p class="text-center"><strong><a href="#" title="Load More">Load More</a></strong></p>
                    </div>
                </div>
            </div>
        </section> <!--coupons sec end-->
    </div> <!--mid content end-->
@stop
