@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Signup</title>
@endpush

@section('content')

<div class="col-xs-12">
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
</div>
<div class="centerlize">
    <div class="container">
            <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
                <div class="col-md-12 col-sm-12 col-xs-12">                    
                    <p class="login-box-msg">{!!$responseMsg!!}</p>
                    @if($userType == 1)
                    <p class="login-box-msg"><a href="{{url('parent/login')}}">For login click here</a></p>
                    @else
                    <p class="login-box-msg"><a href="{{url('counselor')}}">For login click here</a></p>
                    @endif
                </div>
            </div>
    </div>
</div>
    @stop
    @section('script')

    @stop