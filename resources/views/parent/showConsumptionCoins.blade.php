@extends('layouts.parent-master')

@section('content')

<div>
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
    @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-8 col-md-offset-2 invalid_pass_error">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable success_msg">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container consumed_coin1">
        <div class="pricing_title">
            <div class="my_teens_content ">
                <a href="{{url('parent/my-coins')}}" class="back_me history_back"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;
                    <span>Back</span>
                </a>
            </div>
            <h1><span class="title_border">{{trans('labels.consumedcoins')}}</span></h1>

        </div>
        <!--sec-consumption-->
        <section class="sec-progress sec-consumption">
            <div class="container">
                <div class="bg-white my-progress procoins-gift">
                    <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                        <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">{{trans('labels.level4promiseplus')}}</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">{{trans('labels.learninguidance')}}</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">{{trans('labels.l4concept')}}</span></span></a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="menu1" class="tab-pane fade in active">
                           <div class="gift-search">
                                    <div class="procoin-form gift-form">
                                        <form>
                                            <div class="form-group search-bar clearfix">
                                                <input id="searchPromisePlus" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <div class="gift-table table-responsive consumption-table searched-promise-plus">
                                @include('teenager/searchedPromisePlus')    
                            </div>
                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table learning-guidance-data">
                                @include('parent/learningGuidanceData')
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                           <div class="gift-search">
                                    <div class="procoin-form gift-form">
                                        <form>
                                            <div class="form-group search-bar clearfix">
                                                <input id="searchL4Concept" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <div class="gift-table table-responsive consumption-table searched-l4concept-template">
                                @include('parent/searchedL4ConceptTemplate')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--sec-consumption end-->
    </div>
</div>
@stop
@section('script')
<script>
    $(function() {
        $('body').on('click', '.pagination a', function(e) {
            e.preventDefault();
            var url = new URL($(this).attr('href'));
            var page = url.searchParams.get("page");
            var tab = url.searchParams.get("tab");
            searchText = '';
            if (tab == "promise_plus") {
                search_keyword = $("#searchPromisePlus").val();
                searchText = (search_keyword).trim();
                if (searchText.length == 1 || searchText.length == 2) {
                    searchText = '';
                }
            } 
            if (tab == "l4_concept_template") {
                search_keyword = $("#searchL4Concept").val();
                searchText = (search_keyword).trim();
                if (searchText.length == 1 || searchText.length == 2) {
                    searchText = '';
                }
            }
            getConsumptionHistory(searchText, page, tab);
        });
    });

    $( "#searchPromisePlus" ).keyup(function (e) {
        search_keyword = $(this).val();
        searchText = (search_keyword).trim();
        if ((e.which <= 90 && e.which >= 48) || e.which == 222) {
            if (searchText.length == 1 || searchText.length == 2) {
                return false;
            } else {
                getConsumptionHistory(searchText, 1, 'promise_plus');
            }
        } else {
            return false;
        }
        e.preventDefault();
    });

    $( "#searchL4Concept" ).keyup(function (e) {
        search_keyword = $(this).val();
        searchText = (search_keyword).trim();
        if ((e.which <= 90 && e.which >= 48) || e.which == 222) {
            if (searchText.length == 1 || searchText.length == 2) {
                return false;
            } else {
                getConsumptionHistory(searchText, 1, 'l4_concept_template');
            }
        } else {
            return false;
        }
        e.preventDefault();
    });

    function getConsumptionHistory(search, page, tab) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'searchText=' + search + '&page=' + page + '&tab=' + tab;
        if (tab == "promise_plus") {
            $('#loader-promise-plus').parent().toggleClass('loading-screen-parent');
            $('#loader-promise-plus').show();
        } else if (tab == "l4_concept_template") {
            $('#loader-l4concept-template').parent().toggleClass('loading-screen-parent');
            $('#loader-l4concept-template').show();
        } else {
            $('#loader-learning-guidance').parent().toggleClass('loading-screen-parent');
            $('#loader-learning-guidance').show();
        }
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/parent/user-search-for-coins') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                if (tab == 'promise_plus') {
                    $('.searched-promise-plus').html(data);
                    $('#loader-promise-plus').hide();
                    $('#loader-promise-plus').parent().removeClass('loading-screen-parent');
                } else if (tab == 'l4_concept_template') {
                    $('.searched-l4concept-template').html(data);
                    $('#loader-l4concept-template').hide();
                    $('#loader-l4concept-template').parent().removeClass('loading-screen-parent');  
                } else {
                    $('.learning-guidance-data').html(data);
                    $('#loader-learning-guidance').hide();
                    $('#loader-learning-guidance').parent().removeClass('loading-screen-parent');  
                }
                
            }
        });
    }
</script>

@stop