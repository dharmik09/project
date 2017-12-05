@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Teenager Verification</title>
@endpush

@section('content')
<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>teen sign up verification</h1>
            <br/>
            <div class="form-sec">
                <h4>{{ $verifyMessage }}</h4>
                <a href="{{ url('/teenager/login') }}" class="btn primary_btn"><em>Sign In</em><span></span></a>
            </div>
        </div>
    </div>
</section>
@stop