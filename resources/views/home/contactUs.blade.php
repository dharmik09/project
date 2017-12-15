@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Contact Us</title>
@endpush

@section('content')
    <div class="section">
        <div class="bg-offwhite">
            <div class="contact-map">
                <div id="map-container">
                    <div class="map-overlay">
                        <!-- -->
                    </div>
                    <div class="sec-map active" id="mumbai">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3774.134933166877!2d72.8199561144652!3d18.925420687174462!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7d1e94d493f71%3A0x965149b3e344a9f3!2sUniDEL+Ventures+Pvt.+Ltd.!5e0!3m2!1sen!2sin!4v1511932284856" allowfullscreen></iframe>
                    </div>
                    <div class="sec-map" id="singapore">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.7921158011445!2d103.85028666429565!3d1.2995330990521734!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da19bb3d92a31b%3A0xd725e0a4a17bb90a!2sGSM+Building!5e0!3m2!1sen!2sin!4v1511932426569" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="contact-detail">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <h3>Mumbai Office</h3>
                            <p>UniDEL Ventures Pvt. Ltd.<br> 86 Jolly Maker 2,<br> 225 Nariman Point,<br> Mumbai – 400 021, India</p>
                            <p class="mail">
                                <img src="{{ Storage::url('img/mail.png') }}" alt="Contact email :">
                                <a href="mailto:info@proteenlife.com" title="Mail Us">info@proteenlife.com</a>
                            </p>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <h3>Singapore Office</h3>
                            <p>UniDEL Pte. Ltd.<br> 141, Middle Road,<br> #04-07 GSM Building,<br> Singapore - 188976</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@stop