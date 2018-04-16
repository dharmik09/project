@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')

<div class="bg-offwhite">
    <div class="container">
        <div class="careers-list">
            <div class="top-heading text-center listing-heading">
                <h1>careers</h1>
                <p>You have completed <strong class="font-blue">
                    <?php $attemptedProfessionCount = Helpers::getProfessionCompleteCount(Auth::guard('teenager')->user()->id); 
                            echo (isset($attemptedProfessionCount)) ? $attemptedProfessionCount : 0; ?> of {{$totalProfessionCount}}</strong> careers</p>
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
                                <option value="7">Match Scale</option>
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
                        
                    </div>
                </div>
            </section>
            <!-- mid section end-->
            <div class="sec-blank"></div>
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

    // function fetchProfessionData(id) {
    //     $('.play-btn').show();
    //     $('.iframe').attr('src', '');
    //     if ( !$("#profession"+id).hasClass( "dataLoaded" ) ) {
            
    //         $("#profession"+id).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
    //         $("#profession"+id).addClass('loading-screen-parent');
            
    //         var CSRF_TOKEN = "{{ csrf_token() }}";
    //         $.ajax({
    //             type: 'POST',
    //             url: "{{url('teenager/career-list')}}",
    //             dataType: 'html',
    //             headers: {
    //                 'X-CSRF-TOKEN': CSRF_TOKEN
    //             },
    //             data: {'basket_id':id},
    //             success: function (response) {
    //                 $("#profession"+id).html(response);
    //                 $("#profession"+id).addClass("dataLoaded");
    //                 $("#profession"+id).removeClass('loading-screen-parent');
    //             }
    //         });
    //     }
    // }


    // $(function() {

    //     $('#search').keyup(function ()  {
    //         if($("#search").val().length > 3) {      
    //             $('.iframe').attr('src', '');          
    //             $(".maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
    //             $(".maindiv").addClass('loading-screen-parent');
    //             var value = $("#search").val();
    //             var CSRF_TOKEN = "{{ csrf_token() }}";
    //             $.ajax({
    //                 type: 'POST',
    //                 url: "{{url('teenager/search-career-list')}}",
    //                 dataType: 'html',
    //                 headers: {
    //                     'X-CSRF-TOKEN': CSRF_TOKEN
    //                 },
    //                 data: {'search_text':value},
    //                 success: function (response) {
    //                     $(".maindiv").html(response);
    //                     $(".maindiv").addClass("dataLoaded");
    //                     $(".maindiv").removeClass('loading-screen-parent');
    //                     //$('.maindiv').removeHighlight().highlight($('#search').val());
    //                 }
    //             });
    //         }
    //     });
    // });

    function fetchSearchDropdown() {
        if($("#questionDropdown").val() != 0){
            $("#answerDropdown").html('<img src="{{Storage::url('img/loading.gif')}}">');
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
        // $(".maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
        // $(".maindiv").addClass('loading-screen-parent');
        // var CSRF_TOKEN = "{{ csrf_token() }}";
        // var queId = $("#questionDropdown").val();
        // var ansId = $("#answerId").val();
        // var view = 'LIST';
        // $.ajax({
        //     type: 'POST',
        //     url: "{{url('teenager/get-dropdown-search-result')}}",
        //     dataType: 'html',
        //     headers: {
        //         'X-CSRF-TOKEN': CSRF_TOKEN
        //     },
        //     data: {'queId':queId,'ansId':ansId,'view':view},
        //     success: function (response) {
        //         $(".maindiv").html(response);
        //         $(".maindiv").removeClass('loading-screen-parent');
        //     }
        // });
        fetchProfessionDetails();
    }

    function fetchProfessionDetails()
    {
        $(".sec-blank").remove();
        var filterBy = $("#questionDropdown").val();
        var filterOption = $("#answerId").val();
        var searchText = $("#search").val();
        var layoutType = layoutType;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var formData = 'searchText=' + searchText + '&filterBy=' + filterBy + '&filterOption=' + filterOption;
        $(".maindiv").html('<div id="list-career-loader" style="display: block;" class="loading-screen loading-wrapper-sub"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
        $(".maindiv").addClass('loading-screen-parent');
        $("#list-career-loader").show();
        $.ajax({
            type: 'POST',
            data: formData,
            url: "{{ url('/teenager/get-professions-details') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(response) {
                if (response != '') {
                    $(".maindiv").html(response);
                    $("#list-career-loader").hide();
                    $(".maindiv").removeClass('loading-screen-parent');
                } else {
                    
                }
            }
        });
    }

    $(document).on('keyup','#search',function(e) {
        if ((e.which <= 90 && e.which >= 48) || e.which == 222) {
            if (this.value.length == 1 || this.value.length == 2) {
                return false;
            } else {
                fetchProfessionDetails();
            }
        } else {
            return false;
        }
        e.preventDefault();
    });

    $(window).on("load", function(e) {
        e.preventDefault();
        fetchProfessionDetails();
    });

    function changePageLayout(layoutType, basketId) {
        if (layoutType == 2) {
            $("#grid-layout-"+basketId).show();
            $("#list-layout-"+basketId).hide();
        } else {
            $("#list-layout-"+basketId).show();
            $("#grid-layout-"+basketId).hide();    
        }
    }
</script>
@stop