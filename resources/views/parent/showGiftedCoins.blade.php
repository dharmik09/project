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
       <!--  <div class="my_teens_content clearfix">
           <div class="my_teens_inner">
               <div class="login_form avl_coin_form consumed_coin search_coin_cst clearfix">
                   <div class="col-md-push-3 col-sm-push-3 col-md-6 col-sm-6">
                       <div class="search_container desktop_search gift_coin_search">
                           <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search here..." onkeyup="userSearch(this.value, {{Auth::guard('parent')->user()->id}},1)">
                           <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                       </div>
                   </div>
               </div>
               <div class="table_container cst_consumed_detail">
                   <table class="sponsor_table">
                       <tr>
                           <th>{{trans('labels.blheadgiftedto')}}</th>
                           <th>{{trans('labels.giftedcoins')}}</th>
                           <th>{{trans('labels.gifteddate')}}</th>
                       </tr>
                       @if(!empty($parentCoinsDetail) && count($parentCoinsDetail) > 0)
                       @foreach($parentCoinsDetail as $key=>$data)
                       <tr>
                           <td>
                               {{$data->t_name}}
                           </td>
                           <td>
                               <?php echo number_format($data->tcg_total_coins); ?>
                           </td>
                           <td>
                               @if($data->tcg_gift_date != '')
                               <?php echo date('d M Y', strtotime($data->tcg_gift_date)); ?>
                               @else
                               -
                               @endif
                           </td>
                       </tr>
                       @endforeach
                       @else
                       <tr><td colspan="4">No data found</td></tr>
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
       </div> -->
        <div class="procoin-heading gift-heading">
            <div class="container">
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
         <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins-gift">
                <div class="gift-table table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gifted To</th>
                                <th>Gifted ProCoins</th>
                                <th>Gifted Date</th>
                           </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>1</td>
                                <td>07 Mar 2018</td>
                            </tr>
                            <tr>
                                <td>John</td>
                                <td>1</td>
                                <td>07 Mar 2018</td>
                            </tr>
                            <tr>
                                <td>John</td>
                                <td>1</td>
                                <td>07 Mar 2018</td>
                            </tr>
                        </tbody>

                    </table>
                    <!--<div class="no-data">
                        <div class="data-content">
                            <div>
                                <i class="icon-empty-folder"></i>
                            </div>
                            <p>No data found</p>
                        </div>
                    </div>-->
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
                $('.cst_consumed_detail').hide();
            }
        });
    }
</script>

@stop