@extends('layouts.school-master')

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
    <div class="container">
        <div class="pricing_title">
            <div class="my_teens_content ">
                <div class="btn_cont gift_modal_page">
                    <span class="tool-tip" <?php if($schoolData['sc_coins'] == 0) echo 'data-toggle="tooltip" data-placement="bottom" title="Register as Enterprise to avail ProCoins. If already registered please buy ProCoins package from your Enterprise login"';?>>
                        <a style="margin-bottom:0px;" href="javascript:void(0);" rel="tooltip" onclick="giftCoinsToAll();" class="btn primary_btn space_btm <?php if($schoolData['sc_coins'] == 0) echo 'disabled';?>">Gift ProCoins To All</a>
                            </span>
                    <!-- <a href="{{ url('school/get-gift-coins') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-gift-coins') ? 'active' : '' }}" >{{trans('labels.giftcoins')}}</a> -->
                    <a href="{{ url('school/get-consumption') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-consumption') ? 'active' : '' }}" >{{trans('labels.consumption')}}</a>
                </div>
            </div>

            <h1><span class="title_border">{{trans('labels.giftedcoins')}}</span></h1>
        </div>
        <div class="my_teens_content clearfix">
            <div class="procoin-heading gift-heading">
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input type="text" name="search_box" id="searchForUser" placeholder="search" tabindex="1" class="form-control search-feild" onkeyup="userSearch(this.value, {{Auth::guard('school')->user()->id}}, 1)">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bg-white procoins-gift">
                <div class="gift-table table-responsive mySearch_area">
                    @include('school/searchGiftedCoins')
                </div>
                <div class="sec-bttm"><!-- --></div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade default_popup" id="gift">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close close_next" data-dismiss="modal">Close</button> -->
            <div class="close close_next">
                <i class="icon-close"></i>
            </div>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Gift Procoins</h2></div>
            <div id="userDataGiftCoin">

            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<script>
    $(".table_container").mCustomScrollbar({axis:"x"});
    $(document).on('click', '.pagination a', function (e) {
        var search = $("#searchForUser").val();
        var schoolid = <?php echo Auth::guard('school')->user()->id; ?>;
        var page = $(this).attr('href').split('page=')[1];
        userSearch(search,schoolid,page);
        e.preventDefault();
    });

    function userSearch(search_keyword, schoolId,page) {
        $('.mySearch_area').append('<div id="giftcoin_search" style="display: none;" class="loading-screen-data loading-wrapper-sub"><div class="loading-content"><img src="{{ Storage::url('img/Bars.gif') }}"></div></div>');
        $('#giftcoin_search').parent().addClass('loading-screen-parent');
        $('#giftcoin_search').show();
        search_keyword = (search_keyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'search_keyword=' + search_keyword + '&schoolId=' +schoolId;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/school/user-search-for-show-gift-coins?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').html(data);
                $('#giftcoin_search').hide();
                $('#giftcoin_search').parent().removeClass('loading-screen-parent');
            }
        });
    }
    
    function giftCoinsToAll()
    {
        var coin = <?php echo $schoolData['sc_coins'];?>;
        if (coin == 0) {
          return false;
        }
        $('.loader-transparent').show();
        $.ajax({
            url: "{{ url('school/gift-coins-to-all-teen') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
               $('.loader-transparent').hide();
               $('#userDataGiftCoin').html(response);
               $('#gift').modal('show');
            }
        });
    }
    $('[data-toggle="tooltip"]').tooltip();
</script>

@stop