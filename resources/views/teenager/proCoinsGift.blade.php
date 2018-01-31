@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins gift</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading gift-heading">
            <div class="container">
                <h1 class="font-blue">{{trans('labels.availablecoins')}}</h1>
                <p>You have <strong class="font-blue"><span class="coin_count_ttl">@if(!empty($coinDetail)) <?php echo number_format($coinDetail['t_coins']);?> @endif</span></strong> procoins</p>
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input id="searchForUser" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins-gift">
                <div id="gift-history-loader" class="loading-screen loading-wrapper-sub">
                    <div id="loading-text">
                        <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img"></div>
                    <div id="loading-content">
                    </div>
                </div>
                <div id="giftTable" class="gift-table table-responsive">
                    <table class="table table-hover previous-gift-coin">
                        <thead>
                            <tr>
                                <th>{{trans('labels.blheadgiftedto')}}</th>
                                <th>{{trans('labels.giftedcoins')}}</th>
                                <th>{{trans('labels.gifteddate')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($teenCoinsDetail) && count($teenCoinsDetail) > 0)
                            @foreach($teenCoinsDetail as $key => $data)
                            <tr>
                                <td>{{$data->t_name}}</td>
                                <td><?php echo number_format($data->tcg_total_coins); ?></td>
                                <td><?php echo date('d M Y', strtotime($data->tcg_gift_date)); ?></td>
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
                            <tr>
                            <td colspan="3">
                                @if (isset($teenCoinsDetail) && !empty($teenCoinsDetail))
                                      <?php echo $teenCoinsDetail->render(); ?>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="gift-table table-responsive mySearch_area"></div>
                <div class="sec-bttm"><!-- --></div>
            </div>
        </div>
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
@stop

@section('script')
<script>
    $( document ).ready(function() {
        $('.mySearch_area').hide();
    });
    $(document).on('click', '.pagination a', function (e) {
        var search = $("#searchForUser").val();
        var page = $(this).attr('href').split('page=')[1];
        if (search.length == 1 || search.length == 2) {
            searchText = '';
        } else {
            searchText = search;
        }
        userSearch(searchText, page);
        e.preventDefault();
    });

    $( "#searchForUser" ).keyup(function (e) {
        search_keyword = $(this).val();
        searchText = (search_keyword).trim();
        if ((e.which <= 90 && e.which >= 48) || e.which == 222) {
            if (searchText.length == 1 || searchText.length == 2) {
                return false;
            } else {
                userSearch(searchText, 1);
            }
        } else {
            return false;
        }
        e.preventDefault();
    });

    function userSearch(search_keyword, page) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'search_keyword=' + search_keyword;
        $('#gift-history-loader').parent().addClass('loading-screen-parent').blur();
        $('#gift-history-loader').show();
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/teenager/user-search-to-gift-coins?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').show();
                $('.mySearch_area').html(data);
                $('#gift-history-loader').hide();
                $('#gift-history-loader').parent().removeClass('loading-screen-parent');
                $('#giftTable').hide();
            }
        });
    }
    function saveGiftedCoins(teenager_id,coins)
    {
        $('#gift_'+teenager_id).toggleClass('sending').blur();
        var g_coins = $("#"+teenager_id).val();
        
        if (g_coins <= 0) {
            $('#gift_'+teenager_id).removeClass('sending').blur();
            //$('#send_'+teenager_id).addClass('send_error');
            //$('#send_'+teenager_id).removeClass('send_success');
            $('#send_'+teenager_id).text('Please enter valid ProCoins to gift');
            setTimeout(function(){$('#send_'+teenager_id).text(' ');},5000);
            return false;
        } else if (g_coins > coins && g_coins > 0) {
            $('#gift_'+teenager_id).removeClass('sending').blur();
            //$('#send_'+teenager_id).addClass('send_error');
            //$('#send_'+teenager_id).removeClass('send_success');
            $('#send_'+teenager_id).text('Hey! You can only gift from what you have!!');
            setTimeout(function(){$('#send_'+teenager_id).text(' ');},5000);
            return false;
        } else {
             $.ajax({
                url: "{{ url('/teenager/save-gifted-coins-data') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "gift_coins": g_coins,
                    "teenId": teenager_id
                },
                success: function(response) {
                    $.ajax({
                        url: "{{ url('/teenager/get-available-coins') }}",
                        type: 'POST',
                        data: {
                            "_token": '{{ csrf_token() }}',
                            "teenId": teenager_id
                        },
                        success: function(coins) {
                            $('#coin_'+teenager_id).html(format(coins));
                            $('#send_'+teenager_id).text('Coins gifted successfully');
                            setTimeout(function(){$('#send_'+teenager_id).text(' ');},5000);
                            $('.coin_count_ttl').html(response);
                            $("#"+teenager_id).val('');
                            $('#gift_'+teenager_id).removeClass('sending').blur();
                        }
                    });
                }
            });
        }
    }

    function format(x) {
        return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
@endsection