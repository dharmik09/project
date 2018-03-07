@extends('layouts.teenager-master')

@push('script-header')
    <title>My Careers</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="container">
            <div class="careers-container">
                <div class="top-heading text-center">
                    <h1>my careers</h1>
                    <p>You have completed <strong>
                        <?php $attemptedProfessionCount = Helpers::getProfessionCompleteCount(Auth::guard('teenager')->user()->id, 1); 
                        echo (isset($attemptedProfessionCount)) ? $attemptedProfessionCount : 0; ?> of {{$teenagerTotalProfessionStarRatedCount}}</strong> careers from your shortlist</p>
                </div>
                <div class="sec-filter">    
                    <div class="row">
                        <div class="col-md-2 text-right">
                            <span>Filter by:</span>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select">
                                <select id="questionDropdown" tabindex="8" class="form-control" onchange="fetchSearchDropdown();">
                                  <option value="">all categories</option>
                                  @foreach ($filterData as $keyFilter => $valFilter)
                                    <option value="{{$keyFilter}}">{{$valFilter}}</option>
                                  @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="answerDropdown" class="col-md-3 col-xs-6">
                            
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group search-bar clearfix">
                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild" id="search">
                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- category list-->
                <div id="maindiv">
                    @include('teenager/basic/searchdMyCareers')
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
<script type="text/javascript">
    $(function() {

        $('#search').keyup(function ()  {
            if($("#search").val().length > 3) {      
                fetchDropdownResult();
            } else {
                return false;
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
        $("#maindiv").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
        $("#maindiv").addClass('loading-screen-parent');
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var queId = $("#questionDropdown").val();
        var ansId = $("#answerId").val();
        var searchText = $("#search").val();
        //var view = 'LIST';
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-my-career-dropdown-search-result')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'queId':queId,'ansId':ansId,'searchText':searchText},
            success: function (response) {
                $("#maindiv").html(response);
                $("#maindiv").removeClass('loading-screen-parent');
            }
        });
    }
</script>
@stop