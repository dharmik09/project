@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Terms & Conditions</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <div class="terms-heading">
            <div class="container">
                <h1>Disclaimer & Terms of Use</h1>
            </div>
        </div>
        <div class="terms-content">
            <div class="container">
                @if(isset($termInfo->cms_body) && !empty($termInfo->cms_body))
                    {!! $termInfo->cms_body !!}
                @else
                <div class="sec-blank-about">
                    <center><p>Will update soon.</p></center>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop