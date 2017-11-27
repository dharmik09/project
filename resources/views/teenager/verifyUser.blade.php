@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Teenager Verification</title>
@endpush

@section('content')
    <div class="centerlize">
        <div class="container">
            <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="verifide_content">
                        <span>{{ $verifyMessage }}</span>
                        <a href="{{ url('/teenager/login') }}" class="btn primary_btn"><em>Login</em><span></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop