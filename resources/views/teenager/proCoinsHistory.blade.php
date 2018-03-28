@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins History</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading gift-heading history-heading">
            <div class="container">
                <h1 class="font-blue">history</h1>
                <p>Track your ProCoins Transactions and Consumption</p>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="procoins-history">
                <h2>Transactions</h2>
                <div class="bg-white procoins-gift">
                    <div class="gift-table table-responsive history-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{trans('labels.teentblheadname')}}</th>
                                    <th>{{trans('labels.emaillbl')}}</th>
                                    <th>{{trans('labels.transectionid')}}</th>
                                    <th>{{trans('labels.paidamount')}}</th>
                                    <th>{{trans('labels.formcurrency')}}</th>
                                    <th>{{trans('labels.formlblcoins')}}</th>
                                    <th>{{trans('labels.transectiondate')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($transactionDetail) && count($transactionDetail) > 0)
                                    @foreach($transactionDetail as $key=>$data)
                                    <tr>
                                        <td>{{$data->tn_billing_name}}</td>
                                        <td>{{$data->tn_email}}</td>
                                        <td>{{$data->tn_transaction_id}}</td>
                                        <td>{{$data->tn_amount}}</td>
                                        <td>{{$data->tn_currency}}</td>
                                        <td><?php echo number_format($data->tn_coins); ?></td>
                                        <td><?php echo date('d M Y', strtotime($data->tn_trans_date)); ?></td>
                                    </tr>
                                    @endforeach
                                @else
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                    <p>No data found</p>
                                    </div>
                                </div>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!--sec-consumption-->
        <section class="sec-progress sec-consumption">
            <div class="container">
                <h2>Consumption</h2>
                <div class="bg-white my-progress procoins-gift">
                    <!--<ul class="nav nav-tabs progress-tab">
                        <li class="acheivement active"><a data-toggle="tab" href="#menu1">Promise Plus</a></li>
                        <li class="career"><a data-toggle="tab" href="#menu2">Learning Guidance</a></li>
                        <li class="connection"><a data-toggle="tab" href="#menu3">L4 Concept Template</a></li>
                    </ul>-->
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
                                            <input id="searchPromisePlus" name="searchPromisePlus" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
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
                                @include('teenager/learningGuidanceData')
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                            <div class="gift-search">
                                <div class="procoin-form gift-form">
                                    <form>
                                        <div class="form-group search-bar clearfix">
                                            <input id="searchL4Concept" name="searchL4Concept" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="gift-table table-responsive consumption-table searched-l4concept-template">
                                @include('teenager/searchedL4ConceptTemplate')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--sec-consumption end-->
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
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
            url: "{{ url('/teenager/get-consumption-history-more-data') }}",
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
@endsection