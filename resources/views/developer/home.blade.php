@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.dashboard')}}
    </h1>
</section>

<section class="content">
    <div class="row">
        @include('flash::message')
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    To Be Implemented
                </div>
            </div>
        </div>
    </div>
</section>
@stop