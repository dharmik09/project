@extends('layouts.parent-master')

@section('content')

<div>
    <div class="clearfix" id="errorGoneMsg"> </div>
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

    @if($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable danger">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container">
        <div class="challenge_container">
            <h1><span class="title_border">My Challengers</span></h1>
<?php
if (!empty($parentChallengeData)) {
    ?>
        <ul class="challenges">
            @forelse($parentChallengeData as $value)
                <li>
                    <div class="vs_box">
                        <div class="icon_cont">
                            <?php $professionMainImage = (isset($value->pf_logo) && !empty($value->pf_logo)) ? Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$value->pf_logo : Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').'proteen-logo.png'; ?>
                            <span class="icon"><img src="{{ Storage::url($professionMainImage) }}" alt="{{$value->pf_name}}"></span>
                        </div>
                        <div class="challenging_profession"><h1><span class="title_border"><span class="outer_intro"><span class="inner_intro">{{$value->pf_name}}</span></span></span></h1></div>
                        <div class="challenger_name">{{$value->t_name}}</div>
                        <div class="research clearfix">
                            <!-- <a href="{{url('/parent/my-challengers-research')}}/{{$value->tpc_profession_id}}/{{$value->tpc_teenager_id}}" class="research btn primary_btn">Research</a>
                            <a href="{{url('/parent/my-challengers-accept')}}/{{$value->tpc_profession_id}}/{{$value->tpc_teenager_id}}" class="btn primary_btn <?php //if($value->L4Attempted == 1) {echo 'accepted'; } else { echo 'accept'; }?>">@if($value->L4Attempted == 1) Playing @else Accept @endif</a> -->
                            <a href="{{url('/parent/career-detail')}}/{{$value->pf_slug}}" class="research btn primary_btn">Research</a>
                            <a href="{{url('/parent/career-detail')}}/{{$value->pf_slug}}" class="btn primary_btn">  Accept </a>
                        </div>
                    </div>
                </li>
            @empty
            <div class="inner_container">
                <div class="no_data_page">
                    <span class="nodata_outer">
                        <span class="nodata_middle">
                            No Any challenge Available Yet...
                        </span>
                    </span>
                </div>
            </div>
            @endforelse
        </ul>
    </div>
<?php
} else {
    ?>
    <div class="inner_container">
        <div class="no_data_page">
            <span class="nodata_outer">
                <span class="nodata_middle">
                    No Any challenge Available Yet...
                </span>
            </span>
        </div>
    </div>
    <?php
}
?>
   </div>
</div>
@stop