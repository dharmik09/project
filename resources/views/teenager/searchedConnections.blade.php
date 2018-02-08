<div class="bg-white my-progress existing-connection">
                <!--<ul class="nav nav-tabs progress-tab clearfix">
                    <li class="acheivement active col-md-6"><a data-toggle="tab" href="#menu1">Find New Connections </a></li>
                    <li class="career col-md-6"><a data-toggle="tab" href="#menu2">My Connections </a></li>
                </ul>-->
                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Find New Connections</span></span></a></li>
                    <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu4"><span class="dt"><span class="dtc">My Connections</span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="loading-wrapper-sub" class="loading-screen">
                        <div id="loading-text">
                            <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img"></div>
                        <div id="loading-content">
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade in active search-new-connection">
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
                                    <a href="{{ url('teenager/network-member') }}/{{$newConnection->t_uniqueid}}" title="{{ $newConnection->t_name }}"> {{ $newConnection->t_name }}</a>
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
                            <center>
                                <h3>No Connections found.</h3>
                            </center>
                        @endforelse
                        @if (!empty($newConnections->toArray()) && $newConnectionsCount > 10)
                            <div id="menu1-loader-con" class="loader_con remove-row">
                                <img src="{{Storage::url('img/loading.gif')}}">
                            </div>
                            <p id="remove-row" class="text-center remove-row"><a href="javascript:void(0)" id="load-more" title="load more" class="load-more" data-id="{{ $newConnection->id }}">load more</a></p>
                        @endif
                    </div>
                    <div id="menu4" class="tab-pane fade my-connection">
                       <div class="sec-popup">
                            <a href="javascript:void(0);" onclick="getHelpText('community-my-connection')" data-toggle="clickover" data-popover-content="#community-my-connection" class="help-icon custompop" rel="popover" data-placement="bottom">
                                <i class="icon-question">
                                    <!-- -->
                                </i>
                            </a>
                            <div class="hide" id="community-my-connection">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="community-my-connection"></span>
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
                                    <a href="{{ url('teenager/network-member') }}/{{$myConnection->t_uniqueid}}" title="{{ $myConnection->t_name }}"> {{ $myConnection->t_name }}</a>
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
                            <center>
                                <h3>No Connections found.</h3>
                            </center>
                        @endforelse
                        @if (!empty($myConnections->toArray()) && $myConnectionsCount > 10)
                            <div id="menu2-loader-con" class="loader_con remove-my-connection-row">
                                <img src="{{Storage::url('img/loading.gif')}}">
                            </div>
                            <p class="text-center remove-my-connection-row"><a id="load-more-connection" href="javascript:void(0)" data-id="{{ $myConnection->id }}" title="load more" class="load-more">load more</a></p>
                        @endif
                    </div>
                </div>
            </div>