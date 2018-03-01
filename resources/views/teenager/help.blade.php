@extends('layouts.teenager-master')

@push('script-header')
    <title>FAQ</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="learning-heading faq">
            <div class="container">
            <h1 class="font-blue">WHY? HOW? WHAT?</h1>
            <p>Frequently asked questions</p>
                <div class="procoin-form gift-form">
                    <form id="help_search_form" role="form" method="POST" action="{{ url('/teenager/search-help') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        {{csrf_field()}}
                        <div class="form-group search-bar clearfix">
                            <input name="search_help" type="text" placeholder="search" tabindex="1" class="form-control search-feild" value="{{ (isset($searchText) && !empty($searchText)) ? $searchText : '' }}">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- accordian section-->
        <div class="sec-accordian sec-faq">
            <div class="container">
                <div class="learning-guidance faq-accordian">
                    <div class="panel-group" id="accordion">
                        @forelse($helps as $help)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$help->id}}" class="collapsed"><span>Question:</span> {{ $help->f_question_text }}</a></h4>
                            </div>
                            <?php 
                                if (isset($ansIds) && !empty($ansIds) && in_array($help->id, $ansIds)) {
                                    $collapseIn = "in";
                                    } else {
                                        $collapseIn = "";
                                    }
                            ?>
                            <div class="panel-collapse collapse {{ $collapseIn }}" id="accordion{{$help->id}}">
                                <div class="panel-body">
                                    <?php
                                        $faqImage = (isset($help->f_photo) && $help->f_photo != '') ? Storage::url($faqThumbImageUploadPath . $help->f_photo) : Storage::url($faqThumbImageUploadPath . 'proteen-logo.png'); 
                                    ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                        <p>{!! $help->f_que_answer !!}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mar15">
                                                <img src="{{ $faqImage }}" alt="Img" class="img-responsive">
                                            </div>
                                        </div>
                                    </div>                                                                                                                                               
                                </div>
                            </div>
                        </div>
                        @empty
                            No question found.
                        @endforelse
                    </div>
                </div>
            </div>
        <!-- accordian section end-->
        <!-- mid section end-->
        </div>
    </div>
@stop