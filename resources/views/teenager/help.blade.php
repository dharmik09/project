@extends('layouts.teenager-master')

@push('script-header')
    <title>FAQ</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="learning-heading faq">
            <div class="container">
            <h1 class="font-blue">How to</h1>
            <p>Frequently asked questions</p>
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
                            <div class="panel-collapse collapse" id="accordion{{$help->id}}">
                                <div class="panel-body">
                                    <p><span>Answer:</span> {!! $help->f_que_answer !!}</p>
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