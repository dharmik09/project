@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')

<div class="bg-offwhite">
<!-- mid section starts-->
<div class="container">
    <div class="careers-list">
        <div class="top-heading text-center listing-heading">
            <h1>careers</h1>
            <p>You have completed <strong class="font-blue">{{$teenagerTotalProfessionAttemptedCount->professionAttemptCount}} of {{$totalProfessionCount}}</strong> careers</p>
        </div>
        <div class="sec-filter listing-filter">
            <div class="row">
                <div class="col-md-2 text-right"><span>Filter by:</span></div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group custom-select">
                        <select tabindex="8" class="form-control" id="questionDropdown" onchange="fetchSearchDropdown();">
                            <option value="0">All categories</option>
                            <option value="1">Industry</option>
                            <option value="2">Careers</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6" id="answerDropdown">
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group search-bar clearfix"><input type="text" placeholder="search" tabindex="1" class="form-control search-feild" id="search"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                </div>
            </div>
        </div>
        <!-- mid section-->
        <section class="career-content listing-content">
            <div class="bg-white">
                <div class="panel-group maindiv" id="accordion">
                    @forelse($basketsData as $key => $value)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" onclick="fetchProfessionData(this.id)" class="collapsed">{{$value->b_name}}</a> <a href="{{ url('teenager/career-grid') }}" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse <?php if($key == 0){echo 'in'; $firstId = $value->id;} ?>" id="accordion{{$value->id}}">
                            <div id="profession{{$value->id}}"></div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </section>
        <!-- mid section end-->
    </div>
</div>
</div>

@stop

@section('script')
<script>
    function playVideo(id,link) {
        $("#"+id).hide();
        $('.iframe').attr('src', '');
        $("#iframe-video-"+id).attr('src', 'https://www.youtube.com/embed/'+link+'?autoplay=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1');
        $("#iframe-video-"+id).show();
    }

    function fetchProfessionData(id) {
        $('.play-btn').show();
        $('.iframe').attr('src', '');
        if ( !$("#profession"+id).hasClass( "dataLoaded" ) ) {
            
            $("#profession"+id).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
            $("#profession"+id).addClass('loading-screen-parent');
            
            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/career-list')}}",
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'basket_id':id},
                success: function (response) {
                    $("#profession"+id).html(response);
                    $("#profession"+id).addClass("dataLoaded");
                    $("#profession"+id).removeClass('loading-screen-parent');
                }
            });
        }
    }

    fetchProfessionData({{$firstId}});

    $(function() {

        $('#search').keyup(function ()  {
            if($("#search").val().length > 3) {      
                $('.iframe').attr('src', '');          
                $(".maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
                $(".maindiv").addClass('loading-screen-parent');
                var value = $("#search").val();
                var CSRF_TOKEN = "{{ csrf_token() }}";
                $.ajax({
                    type: 'POST',
                    url: "{{url('teenager/search-career-list')}}",
                    dataType: 'html',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: {'search_text':value},
                    success: function (response) {
                        $(".maindiv").html(response);
                        $(".maindiv").addClass("dataLoaded");
                        $(".maindiv").removeClass('loading-screen-parent');
                        //$('.maindiv').removeHighlight().highlight($('#search').val());
                    }
                });
            }
        });
    });

    function fetchSearchDropdown() {
        if($("#questionDropdown").val() != 0){
            $("#answerDropdown").html('');
            var CSRF_TOKEN = "{{ csrf_token() }}";
            var queId = $("#questionDropdown").val();
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/fetch-career-search-dropdown')}}",
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'queId':queId},
                success: function (response) {
                    $("#answerDropdown").html(response);
                }
            });
        }
        else{
            $("#answerDropdown").html('');
        }
    }

    function fetchDropdownResult() {
        $(".maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
        $(".maindiv").addClass('loading-screen-parent');
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var queId = $("#questionDropdown").val();
        var ansId = $("#answerId").val();
        var view = 'LIST';
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-dropdown-search-result')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'queId':queId,'ansId':ansId,'view':view},
            success: function (response) {
                $(".maindiv").html(response);
                $(".maindiv").removeClass('loading-screen-parent');
            }
        });
    }
</script>
@stop