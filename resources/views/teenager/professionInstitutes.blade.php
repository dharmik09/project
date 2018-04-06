@extends('layouts.teenager-master')

@push('script-header')
    <title>College Finder</title>
@endpush

@section('content')
<div class="bg-offwhite institute-sec">
    <div class="container">
        <div class="col-sm-12 text-center">
            <div class="institute-heading">
                <h1 class="font-blue">College Finder</h1>
            </div>
        </div>
        <div class="institute-filter">
            <div class="row">
                <div class="sec-filter clearfix">
                    <div class="col-sm-2">
                        <span>Filter by:</span>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group custom-select">
                            <select id="questionDropdown" onchange="fetchSearchDropdown();" tabindex="8" class="form-control">
                                <option value="All_Institue">All Colleges</option>
                                <option value="Speciality" <?php echo (isset($speciality) && !empty($speciality)) ? 'selected' : '' ?> >Education Stream</option>
                                <option value="State">State</option>
                                <option value="City">City</option>
                                <option value="Pincode">Pincode</option>
                                <option value="Institute_Affiliation">Institute Affiliation</option>
                                <option value="Management_Category">Category</option>
                                <option value="Accreditation">Accreditation By</option>
                                <option value="Hostel">Hostel Count</option>
                                <option value="Gender">Status</option>
                                <option value="Autonomous">Autonomous</option>
                                <option value="Fees">Fees Range</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div id="userAnswer">
                        @if(isset($speciality) && !empty($speciality))
                            <div class="form-group custom-select">
                                <select id="answerDropdown" onchange="fetchInstituteFilter()" tabindex="8" class="form-control">
                                    <option disabled selected>Select Education Stream</option>
                                    @forelse($institutesSpecialityData as $key => $value)
                                        <option value="{{$value->pis_name}}" <?php if(isset($speciality) && ($speciality == $value->pis_name)){ echo "selected"; } ?> >{{$value->pis_name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        @endif 
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="Search" id="answerName" onkeyup="fetchInstituteFilter()" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search">
                                <i class="icon-search"></i>
                            </button>
                        </div>
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
                        <input type="hidden" id="pageNo" value="0">
                        <a href="javascript:void(0);" title="see more" class="btnLoad load-more" onclick="fetchInstitute()">see more</a>
                    </p>
                </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('frontend/js/jquery.autocomplete.min.js') }}"></script>
<script>
    
<?php
    $stateList = json_encode($state);
    $cityList = json_encode($city);
?>
    var stateData = <?php echo $stateList ?>;
    var cityData = <?php echo $cityList ?>;

    $(document).ready(function() {
        <?php if(isset($speciality) && $speciality != ""){ ?>
            fetchInstituteFilter();
        <?php }else{ ?>
            fetchInstitute(0);
        <?php } ?>
    });


    function fetchInstitute(){
        var pageNo = $('#pageNo').val();

        var questionType = $('#questionDropdown').val();
        var answerName = "";

        if($('#answerName').val().length != 0){
            if($('#answerName').val().length > 3){
                answerName = $('#answerName').val();
            }
        }
        
        if(questionType == "Fees"){
            var answer = $('#answerDropdownMinimumFees').val()+'#'+$('#answerDropdownMaximumFees').val();
        }
        else{
            var answer = $('#answerDropdown').val();
        }

        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            async: 'false',
            url: "{{url('teenager/get-page-wise-institute')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo, 'questionType':questionType, 'answer':answer, 'answerName':answerName},
            success: function (response) {
                if(response.instituteCount != 5){
                    if(response.instituteCount > 0){
                        $('#loadMoreButton').removeClass('text-center');
                        $('#loadMoreButton').removeClass('load-more');
                        $('#loadMoreButton').addClass('notification-complete');
                        $('#loadMoreButton').html("");
                        if (response.pageNo == 1) {
                            $('#loadMoreButton').html("<p>No more institutes<p>");
                        }
                    }
                    else{
                        $('#loadMoreButton').html("");
                    }
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
        var questionTypeText = $('#questionDropdown option:selected').text();
        $("#userAnswer").html('<img src="{{Storage::url('img/loading.gif')}}">');
        if( questionType == 'State' || questionType == 'City' || questionType == 'Pincode'){
            $("#userAnswer").html('<div class="form-group search-bar clearfix"><input type="text" placeholder="Search By '+ questionTypeText +'" tabindex="1" class="form-control search-feild" id="answerDropdown" onkeyup="fetchInstituteFilter()"><button type="submit" class="btn-search"><i class="icon-search"></i></button></div>');

            if(questionType == 'State'){
                $('#answerDropdown').autocomplete({
                    lookup: stateData,
                    onSelect: function(suggestion) {
                        fetchInstituteFilter()
                    }
                });
            }
            else if(questionType == 'City'){
                $('#answerDropdown').autocomplete({
                    lookup: cityData,
                    onSelect: function(suggestion) {
                        fetchInstituteFilter()
                    }
                });
            }
        }
        else if(questionType == 'All_Institue'){
            $("#userAnswer").html('');
            fetchInstituteFilter();
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

        var answerName = $('#answerName').val();
        
        if(answerName.length != 0){
            if(answerName.length <= 3){
                return false;
            }
        }

        if( questionType == 'State' || questionType == 'City' || questionType == 'Pincode'){
            var answer = $('#answerDropdown').val();
            if(answer.length != 0 && answerName.length != 0){
                if(answer.length <= 3 && answerName.length <= 3){
                    return false;
                }
            }
            else if(answer.length != 0 && answerName.length == 0){
                if(answer.length <= 3){
                    return false;
                }
            }
            else if(answer.length == 0 && answerName.length != 0){
                if(answerName.length <= 3){
                    return false;
                }
            }
        }

        $('#loadMoreButton').addClass('text-center');
        $('#loadMoreButton').addClass('load-more');
        $('#loadMoreButton').removeClass('notification-complete');
        $('#loadMoreButton').html('<div id="loader_con"></div><p class="text-center"><input type="hidden" id="pageNo" value="1"><a href="javascript:void(0);" title="see more" class="btnLoad load-more" onclick="fetchInstitute()">see more</a></p>');
        $('#pageNo').val('0');
        $("#pageWiseInstitutes").html('');

        var pageNo = $('#pageNo').val();
        
        if(questionType == "Fees"){
            var answer = $('#answerDropdownMinimumFees').val()+'#'+$('#answerDropdownMaximumFees').val();
        }
        else{
            var answer = $('#answerDropdown').val();
        }

        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            async: 'false',
            url: "{{url('teenager/get-page-wise-institute')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo, 'questionType':questionType, 'answer':answer, 'answerName':answerName},
            success: function (response) {
                if(response.instituteCount != 5){
                    if(response.instituteCount > 0){
                        $('#loadMoreButton').removeClass('text-center');
                        $('#loadMoreButton').removeClass('load-more');
                        $('#loadMoreButton').addClass('notification-complete');
                        $('#loadMoreButton').html("");
                        if (response.pageNo == 1) {
                            $('#loadMoreButton').html("<p>No more institutes<p>");
                        }
                    }
                    else{
                        $('#loadMoreButton').html("");
                    }
                }
                else{
                    $('#pageNo').val(response.pageNo);
                }
                $("#pageWiseInstitutes").html(response.institutes);
                $("#loader_con").html('');
            }
        });
    }

</script>
@stop