@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins gift</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading gift-heading">
            <div class="container">
                <h1 class="font-blue">gift</h1>
                <p>You have <strong class="font-blue">0</strong> gifts</p>
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input id="searchForUser" type="text" placeholder="search" tabindex="1" class="form-control search-feild" onkeyup="userSearch(this.value, {{Auth::guard('teenager')->user()->id}}, 1);">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins-gift">
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
                            @foreach($teenCoinsDetail as $key=>$data)
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
        var teenId = <?php echo Auth::guard('teenager')->user()->id; ?>;
        var page = $(this).attr('href').split('page=')[1];
        userSearch(search, teenId, page);
        e.preventDefault();
    });
    function userSearch(search_keyword, teenagerId, page) {
        //$('.loader_outer_container').show();
        search_keyword = (search_keyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'search_keyword=' + search_keyword + '&teenagerId=' +teenagerId;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/teenager/user-search-for-gifted-coins?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').show();
                $('.mySearch_area').html(data);
                $('#giftTable').hide();
            }
        });
    }
</script>
@endsection