@extends('layouts.parent-master')

@section('content')

<div>
    <div class="clearfix" id="errorGoneMsg"> </div>
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

    @if($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
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
    
    @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="centerlize">
    <div class="container">
        <div class="my_teens_content clearfix">
            <div class="coin_summary clearfix">
              <!-- <div class="col-md-6 col-sm-12 col-xs-12 left">
                  <span class="coin_img"><img src="{{Storage::url('frontend/images/available_coin.png')}}" alt=""></span>
                  <span>{{trans('labels.availablecoins')}}</span>
                  <span class="coin_count_ttl"><?php //echo number_format($parentData['p_coins']);?></span>
              </div> -->
              <div class="col-md-6 col-sm-12 col-xs-12 pull-right">
                <div class="clearfix"><a href="{{ url('parent/pair-with-teen') }}" class="btn primary_btn invite_teen_btn cst_pull_right">Invite Teen</a></div>
              </div>
            </div>
            <div class="my_teens_inner">
                <div class="login_form">
                    <h1><span class="title_border">My Teens</span></h1>
                </div><!-- login_form End -->
                <div class="table_container">
                    <table class="sponsor_table">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Image</th>
                            <th>Teen Points</th>
                            <th>Progress</th>
                            <!--<th>Status</th>-->
                            <th>Contact</th>
                            <th>ProCoins</th>
                            <th>Gift</th>
                        </tr>
                        @if(!empty($final))
                        @foreach($final as $key=>$data)
                        <?php $image = Helpers::getTeenagerImageUrl($data['detail']->t_photo, 'thumb'); ?>
                        <tr>
                            <td>{{$data['detail']->t_name}}</td>
                            <td>{{$data['detail']->t_email}}</td>
                            <td>
                                <div class="my_teens_profile_img">
                                    <span><img src="{{$image}}" alt="user_default" width="100px" height="100px"></span>
                                </div><!-- my_teens_profile_img End -->
                            </td>
                            <td>
                                <div class="outer_level_box bar_box">
                                    <div class="level_box">
                                        <div class="level_cst level-1"><span class="l-1 bar_logo"><span class="level_label">L-1</span></span><span class="center_detial">{{$data['booster']['Level1'] or 0}}</span></div>
                                        <div class="level_cst level-2"><span class="l-2 bar_logo"><span class="level_label">L-2</span></span><span class="center_detial">{{$data['booster']['Level2'] or 0}}</span></div>
                                        <div class="level_cst level-3"><span class="l-3 bar_logo"><span class="level_label">L-3</span></span><span class="center_detial">{{$data['booster']['Level3'] or 0}}</span></div>
                                        <div class="level_cst level-4"><span class="l-4 bar_logo"><span class="level_label">L-4</span></span><span class="center_detial">{{$data['booster']['Level4'] or 0}}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($data['pairdata']->ptp_is_verified == 1)
                                <a class="rlink" title="View teen progress" href="{{url('parent/progress/')}}/{{$data['detail']->t_uniqueid}}"><i class="fa fa-line-chart" aria-hidden="true"></i></a>
                                @else
                                -
                                @endif
                            </td>
                            <!--<td>{{($data['pairdata']->ptp_is_verified ==1)?'Verified':'Unverified'}}</td>-->
                            <td><a href="mailto:{{$data['detail']->t_email}}" class="rlink">Contact</a></td>
                            <!--<td><div class="coin_summary"><div class="left"><span class="coin_count_ttl">{{$parentData['p_coins']}}</span></div></div></td>-->
                            <td><?php echo number_format($data['detail']->t_coins); ?></td>
                            <td>
                                <div class="coupon_control">
                                    <button class="gift no_ani gift-btn" onclick="giftCoins({{$data['detail']->id}},'{{$parentData['p_coins']}}');">Gift</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="8">No data found</td></tr>
                        @endif
                    </table>
                </div>
            </div><!-- my_teens_inner End -->
        </div><!-- my_teens_content End -->

    </div><!-- container End -->
</div><!-- centerlize End -->
<!-- <div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div> -->
<div class="loading-screen loading-wrapper-sub loader-transparent" style="display:none;">
    <div class="loading-content"></div>
</div>
<div class="modal fade default_popup" id="gift">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="close close_next">
                <i class="icon-close" data-dismiss="modal"></i>
            </div>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Gift Procoins</h2></div>
            <div id="userDataView">

            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<script>

    function giftCoins(id,coins)
    {
        if (coins == 0) {
            window.scrollTo(0,0);
            if($("#useForClass").hasClass('r_after_click')){
                $("#errorGoneMsg").html('');
            }
            $("#errorGoneMsg").append("<div class='col-md-8 col-md-offset-2 r_after_click' id='useForClass'><div class='box-body'><div class='alert alert-error danger'><button aria-hidden='true' data-dismiss='alert' class='close' type='button'>X</button><span class='fontWeight'>You don't have enough ProCoins. Please Buy more.</span></div></div></div>");
            return false;
        }
        $('.loader-transparent').show();
        $.ajax({
            url: "{{ url('parent/gift-coins') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "teen_id": id
            },
            success: function(response) {
               $('.loader-transparent').hide();
               $('#userDataView').html(response);
               $('#gift').modal('show');
            }
        });
    }
    $(".table_container").mCustomScrollbar({
        axis:"x" // horizontal scrollbar
    });

</script>

@stop
