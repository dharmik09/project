@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.dashboard')}}
    </h1>
</section>

<section class="content">
    <div class="row">
        @include('flash::message')
        <div class="box box-info" style="padding-top: 50px;">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_option">Chart Loads here...</div>  
            </div>
            </div>
        </div>
    </div>
    
</body>
</section>

@stop
@section('script')

@stop