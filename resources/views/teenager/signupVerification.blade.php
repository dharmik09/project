@extends('layouts.home-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Teenager Verification</title>
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
<section class="sec-login">
    <div class="container-small">
        <div class="login-form">
            <h1>teen sign up verification</h1>
            <div class="form-sec">
                <h4>{!!$responseMsg!!}</h4>
            </div>
        </div>
    </div>
</section>
@stop
@section('script')

@stop