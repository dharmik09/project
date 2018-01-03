@extends('layouts.teenager-master')

@push('script-header')
    <title>Community</title>
@endpush

@section('content')
    <!-- mid section starts-->
    <div class="bg-offwhite">
    <div class="container">
        <div class="top-heading text-center">
            <h1>community</h1>
            <p>You have <strong class="font-blue">23</strong> connections</p>
        </div>
        <div class="sec-filter network-filter">
            <div class="row">
                <div class="col-md-4 col-xs-6 sort-feild">
                    <label>Sort by:</label>
                    <div class="form-group custom-select">
                        <select tabindex="1" class="form-control">
                                <option value="high score">high score</option>
                                <option value="moderate">moderate</option>
                                <option value="low">low</option>
                            </select>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6 sort-feild sort-filter">
                    <label>Filter by:</label>
                    <div class="form-group custom-select w-cl">
                        <select tabindex="8" class="form-control">
                                  <option value="all interest">all interest</option>
                                  <option value="Strong match">Strong match</option>
                                  <option value="Potential match">Potential match</option>
                                  <option value="Unlikely match">Unlikely match</option>
                                </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 sort-filter">
                    <div class="form-group search-bar clearfix">
                        <input type="text" id="search_community" name="search_community" placeholder="search" tabindex="1" class="form-control search-feild">
                        <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--sec progress-->
    <section class="sec-progress sec-connection">
        <div class="container">
            <div class="bg-white my-progress existing-connection">
                <!--<ul class="nav nav-tabs progress-tab clearfix">
                    <li class="acheivement active col-md-6"><a data-toggle="tab" href="#menu1">Find New Connections </a></li>
                    <li class="career col-md-6"><a data-toggle="tab" href="#menu2">My Connections </a></li>
                </ul>-->
                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Find New Connections</span></span></a></li>
                    <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">My Connections</span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active search-new-connection">
                        @forelse($newConnections as $newConnection)
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <?php
                                            if(isset($newConnection->t_photo) && $newConnection->t_photo != '') {
                                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$newConnection->t_photo;
                                            } else {
                                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                            }
                                        ?>
                                        <img src="{{ Storage::url($teenPhoto) }}" alt="team">
                                    </div>
                                    <a href="{{ url('teenager/network-member') }}/{{$newConnection->id}}" title="{{ $newConnection->t_name }}"> {{ $newConnection->t_name }}</a>
                                </div>
                            </div>
                            <div class="flex-item">
                                <div class="team-point">
                                    {{ $newConnection->t_coins }} points
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        @empty
                            No Connections found.
                        @endforelse
                        @if (!empty($newConnections->toArray()) && $newConnectionsCount > 10)
                            <p id="remove-row" class="text-center remove-row"><a href="javascript:void(0)" id="load-more" title="load more" class="load-more" data-id="{{ $newConnection->id }}">load more</a></p>
                        @endif
                    </div>
                    <div id="menu2" class="tab-pane fade my-connection">
                       <div class="sec-popup">
                            <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom">
                                <i class="icon-question">
                                    <!-- -->
                                </i>
                            </a>
                            <div class="hide" id="pop1">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                </div>
                            </div>
                        </div>
                        @forelse($myConnections as $myConnection)
                        <div class="team-list">
                            <div class="flex-item">
                                <div class="team-detail">
                                    <div class="team-img">
                                        <?php
                                            if(isset($myConnection->t_photo) && $myConnection->t_photo != '') {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo;
                                            } else {
                                                $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                            }
                                        ?>
                                        <img src="{{ Storage::url($teenImage) }}" alt="team">
                                    </div>
                                    <a href="{{ url('teenager/network-member') }}/{{$myConnection->id}}" title="{{ $myConnection->t_name }}"> {{ $myConnection->t_name }}</a>
                                </div>
                            </div>
                            <div class="flex-item">
                                <div class="team-point">
                                    {{ $myConnection->t_coins }} points
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        @empty
                            No Connections found.
                        @endforelse
                        @if (!empty($myConnections->toArray()) && $myConnectionsCount > 10)
                            <p class="text-center remove-my-connection-row"><a id="load-more-connection" href="javascript:void(0)" title="load more" class="load-more" data-id="{{ $myConnection->id }}">load more</a></p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mySearch_area"></div>
        </div>
    </section>
    <!--sec progress end-->
    </div>
    <!-- mid section end-->
@stop

@section('script')
    <script>
        $( "#search_community" ).keyup(function() {
            search_keyword = $(this).val();
            searchConnections = (search_keyword).trim();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = 'searchConnections=' + searchConnections;
            $.ajax({
                type: 'POST',
                data: form_data,
                url: "{{ url('/teenager/search-community') }}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.existing-connection').hide();
                    $('.mySearch_area').show();
                    $('.mySearch_area').html(data);
                }
            });
        });
        $(document).on('click','#load-more',function(){
                var lastTeenId = $(this).data('id');
                //$("#btn-more").html("Loading....");
                search_keyword = $("#search_community").val();
                searchConnections = (search_keyword).trim();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'searchConnections=' + searchConnections + '&lastTeenId=' + lastTeenId;
                $.ajax({
                    url : '{{ url("teenager/load-more-new-connections") }}',
                    method : "POST",
                    data: form_data,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    dataType : "text",
                    success : function (data) {
                        if(data != '') {
                            //$('#remove-row').remove();
                            $('.remove-row').remove();
                            $('.search-new-connection').append(data);
                        } else {
                            //$('#btn-more').html("No Data");
                        }
                    }
                });
            });
        $(document).on('click','#load-more-connection',function(){
                var lastTeenId = $(this).data('id');
                //$("#btn-more").html("Loading....");
                search_keyword = $("#search_community").val();
                searchConnections = (search_keyword).trim();
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'searchConnections=' + searchConnections + '&lastTeenId=' + lastTeenId;
                $.ajax({
                    url : '{{ url("teenager/load-more-my-connections") }}',
                    method : "POST",
                    data: form_data,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    dataType : "text",
                    success : function (data) {
                        if(data != '') {
                            //$('#remove-row').remove();
                            $('.remove-my-connection-row').remove();
                            $('.my-connection').append(data);
                        } else {
                            //$('#btn-more').html("No Data");
                        }
                    }
                });
            });  
    </script>
@stop