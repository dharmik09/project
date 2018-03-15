@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Dashboard Home</title>
@endpush

@section('content')
<!--mid content-->
<div class="bg-offwhite">
    <div class="container">
        <div class="col-xs-12">
            @if ($message = Session::get('success'))
            <div class="row">
                <div class="col-md-12">
                    <div class="box-body">
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                            <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                            {{ $message }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="row">
                <div class="col-md-8 col-md-offset-2 invalid_pass_error">
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
            @if (count($errors) > 0)
            <div class="alert alert-danger danger">
                <strong>{{trans('validation.whoops')}}</strong>
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                {{trans('validation.someproblems')}}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <!-- sec forum -->
        <div class="full-article">
            <div class="article-que">
                <h2>{{$forumQuestionData->fq_que}}</h2>
                <span><button title="Answer" class="btn btn-ans btn-default">Post Answer</button></span>
                <ul class="social">
                   <li>Share :</li>
                    <li><a href="#" target="_blank" title="Facebook"><i class="icon-facebook"></i></a></li>
                    <li><a href="#" target="_blank" title="Twitter"><i class="icon-twitter"></i></a></li>
                    <li><a href="#" target="_blank" title="Google plus"><i class="icon-google"></i></a></li>
                    <li><a href="#" target="_blank" title="Linkedin"><i class="icon-linkdin"></i></a></li>
                </ul>
            </div>
            <div class="your-answer">
                <form id="addForumAnswer" class="form-horizontal" method="post" action="{{ url('/teenager/save-forum-answer') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="queId" value="{{$forumQuestionData->id}}">
                    <textarea name="answer" id="answer" placeholder="Write your answer" class="form-control" onkeyup="checkAnswerEntered();"></textarea>
                    <p class="text-center">
                        <button class="btn btn-default btn-ans" type="submit" title="Submit" id="btnSaveAnswer" disabled="disabled">Submit</button>
                    </p>
                </form>
            </div>
            <div id="answerList"></div>
            <div class="text-center" id="loadMoreButton">
                <div id="loader_con"></div>
                <p>
                    <button title="Read More" class="btn btn-primary load-more btnLoad" id="pageNo" value="0" onclick="fetchAnswer(this.value)">Load More</button>
                </p>
            </div>
        </div>
        <!--sec forum end-->
    </div>
</div>

@stop
@section('script')

<script>
    $(document).ready(function() {
        $('.read-more').each(function(index, el) {
            var img = $(el).closest('.que-image');
            $(this).click(function() {
                $('.que-image').toggleClass('full-img');
            })
        })
        // CKEDITOR.replace('text');
        $('.article-que .btn-ans').on('click',function(){
            $('.your-answer').toggleClass('show')
        })
    });

    function readMoreLess(){
        $('.text-overflow').each(function(index, el) {
            var parent = $(el).closest('.full-text');
            var btn = parent.find('.read-more');
            var elementHt = parent.find('.text-full').outerHeight();
            if (elementHt > 70) {
                btn.addClass('less');
                btn.css('display', 'block');
            }
            btn.click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                if ($(this).hasClass('less')) {
                    $(this).removeClass('less');
                    $(this).addClass('more');
                    $(this).text('Read Less');
                    $(this).attr('title', 'Read Less');
                    var ht = $(this).closest('.full-text').find('.text-full').outerHeight();
                    $(this).closest('.full-text').find('.text-overflow').animate({
                        'height': ht
                    });
                } else {
                    $(this).addClass('less');
                    $(this).removeClass('more');
                    $(this).text('Read More');
                    $(this).attr('title', 'Read More');
                    $(this).closest('.full-text').find('.text-overflow').animate({
                        'height': '70px'
                    });
                }
            });
        });
    }
    
    function checkAnswerEntered() {
        var answer = $('#answer').val();

        if(answer.length != 0){
            $("#btnSaveAnswer").attr("disabled", false);
        }else{
            $("#btnSaveAnswer").attr("disabled", true);
        }
    }

    function fetchAnswer(pageNo){
        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/fetch-question-answer')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo,'queId':{{$forumQuestionData->id}}},
            success: function (response) {
                if(response.answersCount == 0){
                   $('#loadMoreButton').addClass('userData'); 
                   $('#loadMoreButton').html('<div class="no-data bg-offwhite"><div class="data-content"><div><i class="icon-empty-folder"></i></div><p>The first five contributors will win ProCoins! Answer now!!</p></div></div>');
               
                }    
                else if(response.answersCount != 5){
                    
                    $('#loadMoreButton').removeClass('text-center');
                    $('#loadMoreButton').removeClass('load-more');
                    $('#loadMoreButton').addClass('answer-complete');
                    $('#loadMoreButton').html("<center><p>No more answers<p></center");
                }
                else{
                    $('#pageNo').val(response.pageNo);
                }
                $("#answerList").append(response.answers);
                $("#loader_con").html('');
                readMoreLess();
            }
        });
    }
    fetchAnswer(0);
</script>
@stop