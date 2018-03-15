@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
@endpush

@section('content')

<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                    <p class="header_text">Great! We just reset your password</p>
                    <center><a href="{{ url('/parent/login') }}" class="btn primary_btn"><em>Login</em><span></span></a></center>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')

@stop
