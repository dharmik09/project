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
                    <a href="{{ url('school/get-gift-coins') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-gift-coins') ? 'active' : '' }}" >{{trans('labels.giftcoins')}}</a>
                    <a href="{{ url('school/get-consumption') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-consumption') ? 'active' : '' }}" >{{trans('labels.consumption')}}</a>
                </div>
            </div>

            <h1><span class="title_border">{{trans('labels.giftedcoins')}}</span></h1>
        </div>
        <div class="my_teens_content clearfix">
            <div class="my_teens_inner">
                <div class="login_form avl_coin_form consumed_coin search_coin_cst clearfix">
                    <div class="col-md-push-3 col-sm-push-3 col-md-6 col-sm-6">
                        <div class="search_container desktop_search gift_coin_search">
                            <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search here..." onkeyup="userSearch(this.value, {{Auth::guard('school')->user()->id}},1)">
                            <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table_container cst_consumed_detail" >
                    <table class="sponsor_table">
                        <tr>
                            <th>{{trans('labels.blheadgiftedto')}}</th>
                            <th>{{trans('labels.giftedcoins')}}</th>
                            <th>{{trans('labels.gifteddate')}}</th>
                        </tr>
                        @if(!empty($teenCoinsDetail) && count($teenCoinsDetail) > 0)
                        @foreach($teenCoinsDetail as $key=>$data)
                        <tr>
                            <td>
                                {{$data->t_name}}
                            </td>
                            <td>
                                <?php echo number_format($data->tcg_total_coins); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->tcg_gift_date)); ?>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="3">No data found</td></tr>
                        @endif
                        <tr>
                            <td colspan="3">
                                @if (isset($teenCoinsDetail) && !empty($teenCoinsDetail))
                                      <?php echo $teenCoinsDetail->render(); ?>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="mySearch_area"></div>
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
                $('.cst_consumed_detail').hide();
            }
        });
    }
</script>

@stop