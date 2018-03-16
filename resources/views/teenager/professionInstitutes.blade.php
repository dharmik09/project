@extends('layouts.teenager-master')

@push('script-header')
    <title>Institute</title>
@endpush

@section('content')

<div class="bg-offwhite institute-sec">
    <div class="container">
        <div class="col-sm-12 text-center">
            <div class="institute-heading">
                <h1 class="font-blue">Institute List</h1>
            </div>
        </div>
        <div class="institute-filter">
            <div class="row">
                <div class="sec-filter clearfix">
                    <div class="col-sm-4">
                        <div class="form-group custom-select">
                            <select id="questionDropdown" onchange="fetchSearchDropdown();" tabindex="8" class="form-control">
                                <option disabled selected>Select Filter</option>
                                <option value="State">State</option>
                                <option value="City">City</option>
                                <option value="Pincode">Pincode</option>
                                <option value="Institute_Affiliation">Institute Affiliation</option>
                                <option value="Management_Category">Management Category</option>
                                <option value="Accreditation">Accreditation</option>
                                <option value="Hostel">Hostel</option>
                                <option value="Gender">Gender</option>
                                <option value="Fees">Fees</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div id="userAnswer"></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group search-bar clearfix"><input type="text" placeholder="search" tabindex="1" class="form-control search-feild"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white clearfix">
            <div class="institute-list clearfix">
                <div id="pageWiseInstitutes"></div>
            </div>
                <div class="clearfix load-btn text-center load-more" id="loadMoreButton">
                    <div id="loader_con"></div>
                    <p class="text-center">
                        <input type="hidden" id="pageNo" value="1">
                        <a href="javascript:void(0);" title="see more" class="btnLoad load-more" onclick="fetchInstitute()">see more</a>
                    </p>
                </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script>
    
    fetchInstitute(0);

    function fetchInstitute(){
        var pageNo = $('#pageNo').val();

        var questionType = $('#questionDropdown').val();
        
        if(questionType == "Fees"){
            var answer = new Object();
            answer["minimumFees"] = $('#answerDropdownMinimumFees').val();
            answer["maximumFees"] = $('#answerDropdownMaximumFees').val();
        }
        else{
            var answer = $('#answerDropdown').val();
        }

        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-page-wise-institute')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo, 'questionType':questionType, 'answer':answer},
            success: function (response) {
                if(response.instituteCount != 5){
                    $('#loadMoreButton').removeClass('text-center');
                    $('#loadMoreButton').removeClass('load-more');
                    $('#loadMoreButton').addClass('notification-complete');
                    $('#loadMoreButton').html("<p>No more institutes<p>");
                }
                else{
                    $('#pageNo').val(response.pageNo);
                }
                $("#pageWiseInstitutes").append(response.institutes);
                $("#loader_con").html('');
            }
        });
    }

    function fetchSearchDropdown(){
        var questionType = $('#questionDropdown').val();
        $("#userAnswer").html('<img src="{{Storage::url('img/loading.gif')}}">');
        if( questionType == 'State' || questionType == 'City' || questionType == 'Pincode'){
            $("#userAnswer").html('<div class="form-group search-bar clearfix"><input type="text" placeholder="Search By '+ questionType +'" tabindex="1" class="form-control search-feild" id="answerDropdown" onkeyup="fetchInstituteFilter()"><button type="submit" class="btn-search"><i class="icon-search"></i></button></div>');
        }
        else{        
            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/get-institute-filter')}}",
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'question_type':questionType},
                success: function (response) {
                    $("#userAnswer").html(response);
                }
            });
        }
    }

    function fetchInstituteFilter(){
        var questionType = $('#questionDropdown').val();
        if( questionType == 'State' || questionType == 'City' || questionType == 'Pincode'){
            var answer = $('#answerDropdown').val();
            if(answer.length <= 3){
                return false;
            }
        }
        $('#loadMoreButton').addClass('text-center');
        $('#loadMoreButton').addClass('load-more');
        $('#loadMoreButton').removeClass('notification-complete');
        $('#loadMoreButton').html('<div id="loader_con"></div><p class="text-center"><input type="hidden" id="pageNo" value="1"><a href="javascript:void(0);" title="see more" class="btnLoad load-more" onclick="fetchInstitute()">see more</a></p>');
        $('#pageNo').val('1');
        $("#pageWiseInstitutes").html('');
        fetchInstitute();
    }

</script>
@stop