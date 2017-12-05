@extends('layouts.school-master')

@section('content')
<div>
    @if ($message = Session::get('error'))
      <div class="col-md-8 col-md-offset-2 invalid_pass_error">
          <div class="box-body">
              <div class="alert alert-error alert-dismissable danger">
                  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                  <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                  {{ $message }}
              </div>
          </div>
      </div>
    @endif
    @if ($message = Session::get('success'))
      <div class="col-md-8 col-md-offset-2 invalid_pass_error">
          <div class="box-body">
              <div class="alert alert-success alert-dismissable success_msg">
                  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                  <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                  {{ $message }}
              </div>
          </div>
      </div>
    @endif
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
<div class="container_padd">
    <div class="container lil_profile">
        <div class="row">
            <div class="pricing_title">
                <h1><span class="title_border">{{trans('labels.history')}}</span></h1>
                <div class="btn_cont gift_modal_page">
                    <a href="{{ url('school/get-gift-coins') }}" class="btn primary_btn gift_history" >{{trans('labels.giftcoins')}}</a>
                    <a href="{{ url('school/get-consumption') }}" class="btn primary_btn gift_history" >{{trans('labels.consumption')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop