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
        <div class="forum-module">
            <h2 class="font-blue">Forum Questions</h2>
            <div class="forum-container">
                <div id="questionList"></div>
                <div class="text-center" id="loadMoreButton">
                    <div id="loader_con"></div>
                    <p>
                        <button title="Read More" class="btn btn-primary load-more btnLoad" id="pageNo" value="0" onclick="fetchQuestion(this.value)">Load More</button>
                    </p>
                </div>
            </div>
        </div>
        <!--sec forum end-->
    </div>
</div>    
<!--mid content end-->
        
@stop
@section('script')
<script>
    $('.readMoreClass').click(function(){
        var $this = $(this);
        $this.toggleClass('readMoreClass');
        if($this.hasClass('readMoreClass')){
            $this.text('Read More');         
        } else {
            $this.text('Read Less');
        }
    });

    function fetchQuestion(pageNo){
        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/fetch-page-wise-forum-questions')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo},
            success: function (response) {
                if(response.questionsCount != 5){
                    $('#loadMoreButton').removeClass('text-center');
                    $('#loadMoreButton').removeClass('load-more');
                    $('#loadMoreButton').addClass('question-complete');
                    $('#loadMoreButton').html("<center><p>No more questions<p></center>");
                }
                else{
                    $('#pageNo').val(response.pageNo);
                }
                $("#questionList").append(response.questions);
                $("#loader_con").html('');
            }
        });
    }
    fetchQuestion(0);
</script>
@stop