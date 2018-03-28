@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Privacy Policy</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <div class="terms-heading">
            <div class="container">
                <h1>Privacy Policy</h1>
            </div>
        </div>
        <div class="terms-content">
            <div class="container">
                @if(isset($info->cms_body) && !empty($info->cms_body))
                    {!! $info->cms_body !!}
                @else
                <div class="sec-blank-about">
                    <center><p>Will update soon.</p></center>
                </div>
                @endif
            </div>
        </div>
    </div>
    
@stop