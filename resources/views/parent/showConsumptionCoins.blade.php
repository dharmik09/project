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
        <!-- <div class="my_teens_content clearfix">
            <div class="button_container">
                <div class="submit_register">
                    <ul class="tab">
                      <li><a href="javascript:void(0);" data-targethref="consumed_coin1" class="btn primary_btn active tab_bttn">{{trans('labels.level4promiseplus')}}</a></li>
                      <li><a href="javascript:void(0);" data-targethref="consumed_coin3" class="btn primary_btn tab_bttn consume_coins_cst">{{trans('labels.learninguidance')}}</a></li>
                      <li><a href="javascript:void(0);"  data-targethref="consumed_coin2" class="btn primary_btn tab_bttn">{{trans('labels.l4concept')}}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="my_teens_content clearfix" id='consumedCoins'>
            <div class="my_teens_inner">
                 <div class="login_form avl_coin_form consumed_coin search_coin_cst clearfix">
                    <div class="col-md-push-3 col-sm-push-3 col-md-6 col-sm-6">
                        <div class="search_container desktop_search gift_coin_search">
                            <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search Profession here..." onkeyup="userSearch(this.value, {{Auth::guard('parent')->user()->id}},1)">
                            <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table_container consumed_coin1 consumed_coin_data cst_consumed_detail">
                    <table class="sponsor_table">
                        <tr>
                            <th>{{trans('labels.component')}}</th>
                            <th>{{trans('labels.profession')}}</th>
                            <th>{{trans('labels.consumedcoins')}}</th>
                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                            <th>{{trans('labels.enddate')}}</th>
                        </tr>
                        @if(!empty($deductedCoinsDetail) && count($deductedCoinsDetail) > 0)
                        @foreach($deductedCoinsDetail as $key=>$data)
                        <tr>
                            <td>
                                {{$data->pc_element_name}}
                            </td>
                            <td class="coin_pf_display">
                                @if ($data->pf_name == '')
                                    -
                                @else
                                    {{$data->pf_name}}
                                @endif
                            </td>
                            <td>
                                <?php echo number_format($data->dc_total_coins); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="5">No data found</td></tr>
                        @endif
                         <tr>
                            <td colspan="8">
                                @if (isset($deductedCoinsDetail) && !empty($deductedCoinsDetail))
                                      <?php echo $deductedCoinsDetail->render(); ?>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="table_container consumed_coin3 consumed_coin_data cst_consumed_detail">
                    <table class="sponsor_table">
                        <tr>
                            <th>{{trans('labels.component')}}</th>
                            <th>{{trans('labels.consumedcoins')}}</th>
                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                            <th>{{trans('labels.enddate')}}</th>
                        </tr>
                        @if(!empty($deductedCoinsDetailLS) && count($deductedCoinsDetailLS) > 0)
                        @foreach($deductedCoinsDetailLS as $key=>$data)
                        <tr>
                            <td>
                                {{$data->pc_element_name}}
                            </td>
                            <td>
                                <?php echo number_format($data->dc_total_coins); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="4">No data found</td></tr>
                        @endif
                    </table>
                </div>
                <div class="table_container consumed_coin2 consumed_coin_data cst_consumed_detail">
                    <table class="sponsor_table">
                        <tr>
                            <th>{{trans('labels.profession')}}</th>
                            <th>{{trans('labels.concept')}}</th>
                            <th>{{trans('labels.consumedcoins')}}</th>
                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                            <th>{{trans('labels.enddate')}}</th>
                        </tr>
                        @if(!empty($deductedTemplateCoinsDetail) && count($deductedTemplateCoinsDetail) > 0)
                        @foreach($deductedTemplateCoinsDetail as $key=>$value)
                        <tr>
                          <td class="coin_pf_display">
                              @if ($value->pf_name == '')
                                  -
                              @else
                                  {{$value->pf_name}}
                              @endif
                          </td>
                          <td>
                              {{$value->gt_template_title}}
                          </td>
                          <td>
                              <?php echo number_format($value->tdc_total_coins); ?>
                          </td>
                          <td>
                              <?php echo date('d M Y', strtotime($value->tdc_start_date)); ?>
                          </td>
                          <td>
                              <?php echo date('d M Y', strtotime($value->tdc_end_date)); ?>
                          </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="5">No data found</td></tr>
                        @endif
                        <tr>
                        <td colspan="5">
                            @if (isset($deductedTemplateCoinsDetail) && !empty($deductedTemplateCoinsDetail))
                                  <?php echo $deductedTemplateCoinsDetail->render(); ?>
                            @endif
                        </td>
                    </tr>
                    </table>
              </div>
                        <div class="mySearch_area"></div>
            </div>
        </div> -->
          <!--sec-consumption-->
        <section class="sec-progress sec-consumption">
            <div class="container">
                <div class="bg-white my-progress procoins-gift">
                    <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                        <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Promise Plus</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Learning Guidance</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">L4 Concept Template</span></span></a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="menu1" class="tab-pane fade in active">
                           <div class="gift-search">
                                    <div class="procoin-form gift-form">
                                        <form>
                                            <div class="form-group search-bar clearfix">
                                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
                            </div>

                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                           <div class="gift-search">
                                    <div class="procoin-form gift-form">
                                        <form>
                                            <div class="form-group search-bar clearfix">
                                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
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
    $(".table_container").mCustomScrollbar({axis:"x"});
    $(document).on('click', '.pagination a', function (e) {
        var search = $("#searchForUser").val();
        var teenid = <?php echo Auth::guard('parent')->user()->id; ?>;
        var page = $(this).attr('href').split('page=')[1];
        userSearch(search,teenid,page);
        e.preventDefault();
    });

    $(document).on('click', '.submit_register li a', function (e) {
     $(this).closest('.container').removeClass('consumed_coin1 consumed_coin3 consumed_coin2').addClass($(this).data('targethref'));
     $('.tab_bttn').removeClass('active');
     $(this).addClass('active');
     $('#searchForUser').val('');
     if($(this).hasClass('consume_coins_cst'))
     {
        $('.search_coin_cst').hide();
     }else{
       $('.search_coin_cst').show();
     }
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
            url: "{{ url('/parent/user-search-for-coins?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').html(data);
                $('.cst_consumed_detail').hide();
                $(".table_container").mCustomScrollbar({axis:"x"});
            }
        });
    }
</script>

@stop