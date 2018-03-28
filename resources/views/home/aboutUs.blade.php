@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : About Us</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <div class="terms-heading">
            <div class="container">
                <h1>About Us</h1>
            </div>
        </div>
        <div class="terms-content">
            <div class="container">
                @if(isset($info->cms_body) && !empty($info->cms_body))
                    {!! $info->cms_body !!}
                @else
                <center><p>Will update soon.</p></center>
                @endif
            </div>
        </div>
        <div class="sec-blank-about">
        </div>
    </div>
@stop