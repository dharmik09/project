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
                            <div class="form-group custom-select"><select tabindex="8" class="form-control"><option value="all categories">all categories</option><option value="Strong match">Strong match</option><option value="Potential match">Potential match</option><option value="Unlikely match">Unlikely match</option></select></div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select bg-blue"><select tabindex="8" class="form-control"><option value="all careers">all careers</option><option value="agriculture">agriculture</option><option value="conservation">conservation</option><option value="Veterinarians">Veterinarians</option></select></div>
                        </div>
                        <div class="col-md-4 col-sm-12 col-xs-12">
                            <div class="form-group search-bar clearfix"><input type="text" placeholder="search" id="search" tabindex="1" class="form-control search-feild"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                        </div>
                    </div>
                </div>
                <!-- mid section-->
                
                <section class="career-content listing-content grid-view">
                    <div class="bg-white">
                        <div class="panel-group maindiv" id="accordion">
                            @forelse($basketsData as $key => $value)
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}"  onclick="fetchProfessionData(this.id)" class="collapsed">{{$value->b_name}}</a> <a href="{{ url('teenager/list-career') }}" title="List view" class="grid"><i class="icon-list"></i></a></h4>
                                </div>
                                <div class="panel-collapse collapse <?php if($key == 0){echo 'in'; $firstId = $value->id;} ?>" id="accordion{{$value->id}}">
                                    <div class="panel-body">
                                        <section class="career-content">
                                            <div class="bg-white">
                                                <div id="profession{{$value->id}}"></div>
                                            </div>
                                        </section>
                                    </div>
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
        $("#iframe-video-"+id).attr('src', 'https://www.youtube.com/embed/'+link+'?autoplay=1');
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
                url: "{{url('teenager/career-grid')}}",
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
                    url: "{{url('teenager/search-career-grid')}}",
                    dataType: 'html',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    data: {'search_text':value},
                    success: function (response) {
                        $(".maindiv").html(response);
                        $(".maindiv").addClass("dataLoaded");
                        $(".maindiv").removeClass('loading-screen-parent');
                        $('.maindiv').removeHighlight().highlight($('#search').val());
                    }
                });
            }
        });
    });

</script>
@stop