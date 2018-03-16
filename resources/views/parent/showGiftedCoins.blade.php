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
    <div class="container">
        <div class="pricing_title">
            <div class="my_teens_content ">
                <a href="{{url('parent/my-coins')}}" class="back_me history_back"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;
                    <span>Back</span>
                </a>
            </div>

            <h1><span class="title_border">{{trans('labels.giftedcoins')}}</span></h1>
        </div>
        <div class="procoin-heading gift-heading">
            <div class="container">
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input type="text" id="searchForUser" name="search_box" placeholder="search" tabindex="1" class="form-control search-feild" onkeyup="userSearch(this.value, {{Auth::guard('parent')->user()->id}},1)">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins-gift">
                <div class="gift-table table-responsive mySearch_area">
                    @include('parent/searchGiftedCoins')
                </div>
                <div class="sec-bttm"><!-- --></div>
            </div>
        </div>
        <!--procoins sec end-->
    </div>
</div>
@stop
@section('script')
<script>
    $(".table_container").mCustomScrollbar({axis:"x"});
    $(document).on('click', '.pagination a', function (e) {
        var search = $("#searchForUser").val();
        var parentid = <?php echo Auth::guard('parent')->user()->id; ?>;
        var page = $(this).attr('href').split('page=')[1];
        userSearch(search,parentid,page);
        e.preventDefault();
    });

    function userSearch(search_keyword, parentId,page) {
        search_keyword = (search_keyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'search_keyword=' + search_keyword + '&parentId=' +parentId;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/parent/user-search-for-show-gift-coins?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').html(data);
            }
        });
    }
</script>

@stop