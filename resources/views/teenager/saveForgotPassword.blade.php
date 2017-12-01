@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
@endpush

@section('content')

<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>password saved!</h1>
            <h3>Great! We just reset your password. </h3>
            <p>Already enrolled? <a href="{{ url('teenager/login') }}" title="Sign in now">Sign in now</a></p>
        </div>
    </div>
</section>
@stop
@section('script')

@stop
