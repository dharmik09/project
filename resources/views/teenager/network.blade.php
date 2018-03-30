@extends('layouts.teenager-master')

@push('script-header')
    <title>My Networks</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="container">
            <div class="top-heading text-center">
                <h1>my network</h1>
                <p>You have <strong class="font-blue"><?php echo (isset($connectionsCount)) ? $connectionsCount : "0"; ?> </strong> <?php echo (isset($connectionsCount) && $connectionsCount == 1) ? "connection" : "connections"; ?></p>
            </div>
            <div class="sec-filter network-filter">
                <div class="row">
                    <div class="col-md-4 col-xs-6 sort-feild sort-filter">
                        <label>Sort by:</label>
                        <div class="form-group custom-select">
                            <select tabindex="1" class="form-control" id="filter_by" name="filter_by">
                                <option value="">Select</option>
                                <option value="t_school">School</option>
                                <option value="t_gender">Gender</option>
                                <option value="t_age">Age</option>
                                <option value="t_pincode">Pincode</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6 sort-feild sub-filter">
                        
                    </div>
                    <div class="col-md-4 col-sm-12 sort-filter">
                        <div class="form-group search-bar clearfix">
                            <input id="search_member" name="search_member" type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- network list-->
            <section class="connection-list">
                <div id="loading-wrapper-sub" class="loading-screen bg-offwhite">                    
                    <div id="loading-content">
                    </div>
                </div>
                <div class="row existing-connection">
                    @forelse ($memberDetails as $memberDetail)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <?php if (isset($memberDetail->t_photo) && $memberDetail->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $memberDetail->t_photo) > 0) {
                                    $memberImage = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $memberDetail->t_photo);
                                } else {
                                    $memberImage = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                } ?>
                                <div class="connection-img" style="background-image: url('{{ $memberImage }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="{{ url('teenager/network-member') }}/{{$memberDetail->t_uniqueid }}" title="{{ $memberDetail->t_name }}"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="{{ url('teenager/chat') }}" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="{{ url('teenager/network-member') }}/{{$memberDetail->t_uniqueid }}" title="{{ $memberDetail->t_name }}" >{{ $memberDetail->t_name }}</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    @empty
                        <div class="col-sm-12 col-md-12 sec-forum no_selected_category">
                            <span>No Records Found</span>
                        </div>
                    @endforelse
                </div>
                <div class="row mySearch_area">
                </div>
            </section>
            <!-- network list end-->
        </div>
        <!-- mid section end-->
    </div>
@stop

@section('script')
<script>
    $(document).on('change','#filter_by',function(){
        //$('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        //$('#loading-wrapper-sub').show();
        var filter_option = this.value;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'filter_option=' + filter_option;
        if (filter_option != '') {
            $.ajax({
                url : '{{ url("teenager/get-sub-filter") }}',
                method : "POST",
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                dataType : "text",
                success : function (data) {
                    if(data != '') {
                        // $('#loading-wrapper-sub').hide();
                        // $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                        $('.remove-sub-filter').remove();
                        $('.sub-filter').append(data);
                    } else {
                        // $('#loading-wrapper-sub').hide();
                        // $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                        $('.remove-sub-filter').remove();
                    }
                }
            });
        } else {
            // $('#loading-wrapper-sub').hide();
            // $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            $('.remove-sub-filter').remove();
        }
    });

    $(document).on('change','#sub_filter',function() {
        $('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        $('#loading-wrapper-sub').show();
        var filter_by = $("#filter_by").val();
        var filter_option = this.value;
        var search_keyword = $("#search_member").val();
        var searchConnections = (search_keyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'filter_by=' + filter_by + '&filter_option=' + filter_option + '&searchConnections=' + searchConnections;
        if (filter_option != '' && filter_by != '') {
            $.ajax({
                url : '{{ url("teenager/get-network-members-by-filter") }}',
                method : "POST",
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                dataType : "text",
                success : function (data) {
                    if(data != '') {
                        $('#loading-wrapper-sub').hide();
                        $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                        $('.existing-connection').hide();
                        $('.mySearch_area').show();
                        $('.mySearch_area').html(data);
                    } else {
                        $('#loading-wrapper-sub').hide();
                        $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                        //$('.existing-connection').show();
                    }
                }
            });
        } else {
            $('#loading-wrapper-sub').hide();
            $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            //$('.existing-connection').show();
        }
    });

    $(document).on('keyup','#search_pincode',function() {
                $('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
                $('#loading-wrapper-sub').show();
                var filter_by = $("#filter_by").val();
                var filter_option = this.value;
                var search_keyword = $("#search_member").val();
                var searchConnections = (search_keyword).trim();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'filter_by=' + filter_by + '&filter_option=' + filter_option + '&searchConnections=' + searchConnections;
                if (filter_option != '' && filter_by != '' && filter_option.length > 3) {
                    $.ajax({
                        url : '{{ url("teenager/get-network-members-by-filter") }}',
                        method : "POST",
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        dataType : "text",
                        success : function (data) {
                            if(data != '') {
                                $('#loading-wrapper-sub').hide();
                                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                                $('.existing-connection').hide();
                                $('.mySearch_area').show();
                                $('.mySearch_area').html(data);
                            } else {
                                $('#loading-wrapper-sub').hide();
                                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                                //$('.existing-connection').show();
                            }
                        }
                    });
                } else { 
                    $('#loading-wrapper-sub').hide();
                    $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                }
    });

    $("#search_member").keyup(function() {
            $('#loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
            $('#loading-wrapper-sub').show();
            search_keyword = $(this).val();
            searchConnections = (search_keyword).trim();
            var filter_by = $("#filter_by").val();
            if (filter_by == 't_pincode') {
                var filter_option = $("#search_pincode").val();
            } else {
                var filter_option = $("#sub_filter").val();
            }
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = 'searchConnections=' + searchConnections + '&filter_by=' + filter_by + '&filter_option=' + filter_option;
            if (searchConnections.length > 3) {
                $.ajax({
                    type: 'POST',
                    data: form_data,
                    url: "{{ url('/teenager/search-network') }}",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    cache: false,
                    success: function(data) {
                        if (data != '') {
                            $('.existing-connection').hide();
                            $('#loading-wrapper-sub').hide();
                            $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                            $('.mySearch_area').show();
                            $('.mySearch_area').html(data);
                        } else {
                            $('#loading-wrapper-sub').hide();
                            $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                        }
                    }
                });
            } else {
                if (searchConnections.length == 0) {
                    $('.mySearch_area').html("");
                    $('.mySearch_area').hide();
                    $('.existing-connection').show();
                }
                $('#loading-wrapper-sub').hide();
                $('#loading-wrapper-sub').parent().removeClass('loading-screen-parent');
            }
    });
</script>
@stop

