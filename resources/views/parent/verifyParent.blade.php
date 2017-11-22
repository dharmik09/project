@extends('layouts.parent-master')
@section('content')
    <div class="centerlize">
        <div class="container">
            <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="verifide_content">
                        <span>{{ $varifymessage }}</span>
                        @if($userType == 1)
                        <a href="{{ url('/parent/login') }}" class="btn primary_btn"><em>Login</em><span></span></a>
                        @else
                        <a href="{{ url('/counselor') }}" class="btn primary_btn"><em>Login</em><span></span></a>
                        @endif
                    </div><!-- verifide_content End -->
                </div>
            </div>

        </div>
    </div>
@stop