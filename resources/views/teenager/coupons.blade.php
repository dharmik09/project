@extends('layouts.teenager-master')

@push('script-header')
    <title>Coupons</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading coupons-heading">
            <div class="container">
                <div class="col-md-8 col-md-offset-2">
                    <div class="box-body" style="display: none;">
                        <div class="alert alert-success" style="margin-bottom: 25px;">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                            <div id="coupon_message"></div>
                        </div>
                    </div>
                </div>
                <h1 class="font-blue">coupons</h1>
                <p>You can avail coupons from your selected sponsors on ProTeen!</p>
            </div>
        </div>
        <!--coupons sec-->
        <section class="sec-coupons sec-procoins">
            <div class="container">
                <div class="list-procoins">
                    <div class="row flex-container">
                        @if(isset($couponsArr) && !empty($couponsArr))
                        @foreach($couponsArr as $key=>$val)
                        <?php $activeClass = ($val['type'] == 'active' && $val['is_consume'] == 0)?'':'deactive'; ?>
                        <div class="col-sm-4 col-xs-12 flex-items {{ $activeClass }} coupon_div_{{$val['id']}}">
                            <div class="block-procoins">
                                <div class="coin-info">
                                    <div class="icon">
                                        <img src="{{ $val['coupon_logo'] }}" alt="proteen-coupons">
                                    </div>
                                    <h4>{{ $val['code'] }}</h4>
                                    <p>{{ $val['description'] }}</p>
                                    @if($val['is_consume'] == 0)
                                        <button id="consume_coupon_{{$val['id']}}" onclick="consumeCoupon({{$val['id']}}, '{{Auth::guard('teenager')->user()->t_email}}', 'consume');" title="Consume" class="btn btn-consume btn-default">Consume</button>
                                        <a href="javascript:void(0)" id="gift_coupon_{{$val['id']}}" onclick="showModal({{$val['id']}});" title="Gift" class="btn btn-gift">Gift</a>
                                    @else
                                        <a href="javascript:void(0)" title="Consume" class="btn btn-consume">Consumed</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                            No coupons found..
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <!--coupons sec end-->
    </div>
    <!--mid content end-->
    <div class="modal fade" id="gift" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <h4 class="modal-title">Gift Coupon to</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="coupon_id" name="coupon_id" />
                    <input id="searchForUser" type="text" placeholder="search" tabindex="1" class="form-control search-feild" >
                    <div id="userData">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        function consumeCoupon(coupon_id, email, type)
        {
            $("#consume_coupon_"+coupon_id).toggleClass('sending').blur();
            $.ajax({
                url: "{{ url('teenager/consume-coupon') }}",
                type: 'post',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "coupon_id": coupon_id,
                    "user_email": email,
                    "usage_type": type
                },
                success: function(response) {
                    $("#consume_coupon_"+coupon_id).removeClass('sending').blur();
                    if(response == 'invalid') {
                        $(window).scrollTop(0);
                        $('.box-body').show();
                        $('#coupon_message').html('Invalid coupon');
                    } else if(response == 'success') {
                        $(window).scrollTop(0);
                        $('.box-body').show();
                        $('#coupon_message').html('We have just sent an email of coupon to your registered email');
                        $("#consume_coupon_"+coupon_id).html('Consumed');
                        $("#gift_coupon_"+coupon_id).hide();
                        $(".coupon_div_"+coupon_id).addClass('deactive');
                        $('#gift').modal('hide');
                    } else if(response == 'consumed') {
                        $('.box-body').show();
                        $('#coupon_message').html('You have already consumed this coupon');
                    } else if(response == 'limit') {
                        $('.box-body').show();
                        $('#coupon_message').html('Limit is reached of this coupon');
                    } else if(response == 'unauthorised') {
                        $('.box-body').show();
                        $('#coupon_message').html('Unauthorised coupon');
                    } else {
                        $('.box-body').show();
                        $('#coupon_message').html('Something went wrong, Try again...');
                    }
                }
            });
        }

        function showModal(coupon_id) {
            $('#coupon_id').val('');
            $('#coupon_id').val(coupon_id);
            $('#gift').modal('show');
            $('#userData').html('');
            $('#searchForUser').val('');
        }

        $(document).ready(function() {
            $(document).on('click', '.pagination a', function (e) {
                var couponid = $("#coupon_id").val();
                var search = $("#searchForUser").val();
                var teenid = <?php echo Auth::guard('teenager')->user()->id; ?>;
                var page = $(this).attr('href').split('page=')[1];
                getUsers(search, teenid, couponid, page);
                e.preventDefault();
            });

            $( "#searchForUser" ).keyup(function (e) {
                search_keyword = $(this).val();
                searchText = (search_keyword).trim();
                teenagerId = "{{Auth::guard('teenager')->user()->id}}";
                if ((e.which <= 90 && e.which >= 48) || e.which == 222) {
                    if (searchText.length == 1 || searchText.length == 2) {
                        return false;
                    } else {
                        getUsers(searchText, teenagerId, '', 1);
                    }
                } else {
                    return false;
                }
                e.preventDefault();
            });
        });

        function getUsers(search_keyword, teenager_id, coupon_id, page)
        {
            coupon_id = $("#coupon_id").val();
            $('#loading-wrapper-sub').parent().addClass('loading-screen-parent').blur();
            $('#loading-wrapper-sub').show();
            $.ajax({
                url: "{{ url('teenager/get-users?page=') }}"+page,
                type: 'post',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "teenager": teenager_id,
                    "coupon_id": coupon_id,
                    "search_keyword":search_keyword
                },
                success: function(response) {
                    $('#userData').html(response);
                    $('#loading-wrapper-sub').hide();
                    $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                }
            });
        }
    </script>
@stop