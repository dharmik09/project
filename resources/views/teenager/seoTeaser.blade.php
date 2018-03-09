@extends('layouts.home-master')

@push('script-header')
    <title>Seo teasor</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <!-- mid section-->
        <div class="container">
            <section class="career-detail">
                <form>
                    <div class="col-sm-6">
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search career..." value="{{$slug or ''}}" tabindex="1" id="autocomplete" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </div>
                   
                    <div class="col-sm-6">
                        <div class="btn-seo">
                            <a class="btn btn-primary" href="{{ url('/teenager') }}">Did not find what you are looking for ? Sign-in to let us know and win ProCoins!</a>
                        </div>
                    </div>
                </form>
                <h1>{{$professionsData->pf_name}}</h1>
                <div class="career-banner banner-landing">
                    <img src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}">
                    <div>
                        <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video-click"><img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon"></a></div>
                    </div>
                    <?php $videoCode = Helpers::youtube_id_from_url($professionsData->pf_video);?>
                    @if($videoCode == '')

                    <video id="dropbox_video_player" poster="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}" oncontextmenu="return false;"  controls style="width: 100%;min-width: 100%;">
                        <!-- MP4 must be first for iPad! -->
                        <source src="{{$professionsData->pf_video}}" type="video/mp4"  /><!-- Safari / iOS, IE9 -->  
                        Your browser does not support HTML5 video.
                    </video>

                    @else
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{Helpers::youtube_id_from_url($professionsData->pf_video)}}?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
                    @endif   
                </div>
                <div class="detail-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="career-stat">
                                <div class="row">
                                    <div class="col-sm-6">
                                    <ul class="color-1">
                                        <li class="icon"><?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?></li>
                                        <?php
                                            $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'average_per_year_salary';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' ?></h4>
                                            <p>Average per year</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-2">
                                        <li class="icon"><i class="icon-clock"></i></li>
                                        <?php
                                            $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'work_hours_per_week';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?></h4>
                                            <p>Hours per week</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-3">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_current';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?></h4>
                                            <p>Industry Employment 2017</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-4">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_projected';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?></h4>
                                            <p>Projected for 2022</p>
                                        </li>
                                    </ul>
                                </div>
                                </div>
                            </div>
                            <div class="teaser-content">
                            <div class="description">
                                <div class="heading">
                                    <h4>{{$professionsData->pf_name}}</h4>
                                    <div class="list-icon"><span><a href="#" title="Like"><i class="icon-star"></i></a></span><span><a href="#" title="print"><i class="icon-print"></i></a></span></div>
                                </div>
                                <?php
                                $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                            ?>
                            <p><?php echo (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?></p>
                            </div>
                            <div class="career-detail-tab bg-white">
                                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Career Details</span></span></a></li>
                                    <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Explore <span>21% Complete</span></span></span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="menu1" class="tab-pane fade in active">
                                        <div class="block">
                                            <h4> Outlook</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi. Donec pellentesque vehicula nisi a eleifend. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                        </div>
                                        <div class="block">
                                            <h4>Education</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                            <div class="img-sec"><img src="{{ Storage::url('img/education-img.png') }}" alt="proteen education detail"></div>
                                        </div>
                                        <div class="block">
                                            <h4>Experience</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi. Donec pellentesque vehicula nisi a eleifend. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                        </div>
                                        <div class="block">
                                            <h4>Certifications</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi. Donec pellentesque vehicula nisi a eleifend. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            <div class="img-list">
                                                <ul>
                                                    <li><img src="{{ Storage::url('img/compatia-icon.png') }}" alt="compatia logo"></li>
                                                    <li><img src="{{ Storage::url('img/microsoft-icon.png') }}" alt="microsoft logo"></li>
                                                    <li><img src="{{ Storage::url('img/oracle-icon.png') }}" alt="oracle logo"></li>
                                                    <li><img src="{{ Storage::url('img/cisco-icon.png') }}" alt="Cisco logo"></li>
                                                    <li><img src="{{ Storage::url('img/pmp-icon.png') }}" alt="Pmp logo"></li>
                                                    <li><img src="{{ Storage::url('img/itil-icon.png') }}" alt="ITIL logo"></li>
                                                    <li><img src="{{ Storage::url('img/redhat-icon.png') }}" alt="Redhat logo"></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="block">
                                            <h4>Licensing</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi.</p>
                                        </div>
                                        <div class="block">
                                            <h4>Apprenticeships</h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan. Vestibulum non vulputate nibh, vel congue turpis. Mauris non tellus in mi commodo ornare et sodales mi.</p>
                                        </div>
                                        <div class="block">
                                            <h4>Activities</h4>
                                            <ul>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate</li>
                                                <li>Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi.</li>
                                                <li>Fusce quis tincidunt justo, at bibendum lorem.</li>
                                                <li>Fusce ut est id sem pellentesque viverra.</li>
                                                <li>Sed aliquam mi pellentesque suscipit dignissim bibendum turpis vel suscipit accumsan.</li>
                                                <li>Vestibulum non vulputate nibh, vel congue turpis.</li>
                                                <li>Mauris non tellus in mi commodo ornare et sodales mi.</li>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                                <li>Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor.</li>
                                            </ul>
                                        </div>
                                        <div class="block">
                                            <h4>Abilities</h4>
                                            <ul>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate</li>
                                                <li>Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi.</li>
                                                <li>Fusce quis tincidunt justo, at bibendum lorem.</li>
                                                <li>Fusce ut est id sem pellentesque viverra.</li>
                                                <li>Sed aliquam mi pellentesque suscipit dignissim bibendum turpis vel suscipit accumsan.</li>
                                                <li>Vestibulum non vulputate nibh, vel congue turpis.</li>
                                            </ul>
                                        </div>
                                        <div class="block">
                                            <h4>Knowledge</h4>
                                            <ul>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate</li>
                                                <li>Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi.</li>
                                                <li>Fusce quis tincidunt justo, at bibendum lorem.</li>
                                                <li>Fusce ut est id sem pellentesque viverra.</li>
                                            </ul>
                                        </div>
                                        <div class="block">
                                            <h4>Skills</h4>
                                            <ul>
                                                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate</li>
                                                <li>Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi.</li>
                                                <li>Fusce quis tincidunt justo, at bibendum lorem.</li>
                                                <li>Fusce ut est id sem pellentesque viverra.</li>
                                                <li>Sed aliquam mi pellentesque suscipit dignissim bibendum turpis vel suscipit accumsan.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id="menu2" class="tab-pane fade in ">
                                        <div class="explore-table table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Competitors</th>
                                                        <th>Score</th>
                                                        <th>Rank</th>
                                                        <th>Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>106</td>
                                                        <td>60</td>
                                                        <td>4</td>
                                                        <td>60/1600</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="promise-plus-outer">
                                            <div class="promise-plus front_page">
                                                <div class="heading">
                                                    <span><i class="icon-plus"></i></span>
                                                    <h3>Promise Plus</h3>
                                                </div>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate.</p>
                                                <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                <div class="modal fade" id="myModal" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content custom-modal">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                <h4 class="modal-title">Congratulations!</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>You have 42,000 ProCoins available.</p>
                                                                <p>Click OK to consume your 250 ProCoins and play on</p>
                                                            </div>
                                                            <div class="modal-footer"><button type="button" class="btn btn-primary btn-next" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="promise-plus-overlay">
                                                <div class="promise-plus">
                                                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                    <div class="heading">
                                                        <span class="emojis-img"><img class="emojis-icon-2" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAAB1AQMAAAC7wWdyAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGwZAEAAfFAAFzojyWAAAAAElFTkSuQmCC"></span>
                                                        <h3>Promise Plus</h3>
                                                    </div>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate.</p>
                                                    <button class="btn btn-primary" title="Submit">Sumbit</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="virtual-plus text-center">
                                            <h4><span>Virtual Role Play</span></h4>
                                            <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                        </div>
                                        <div class="quiz-sec ">
                                            <div class="row flex-container">
                                                <div class="col-sm-12">
                                                    <div class="quiz-box quiz-basic">
                                                        <div class="sec-show">
                                                            <h3>Quiz</h3>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                                            <span title="Play" class="btn-play btn btn-basic">Play</span>
                                                        </div>
                                                        <div class="basic-quiz-area sec-hide">
                                                            <div class="quiz_view">
                                                                <div class="clearfix time_noti_view"><span class="time_type pull-left"><i class="icon-alarm"></i><span class="time-tag">58:32</span></span><span class="help_noti pull-right"><span class="pull-right close"><i class="icon-close"></i></span></span></div>
                                                                <div class="quiz-que">
                                                                    <p class="que"><i class="icon-arrow-simple"></i>Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor?</p>
                                                                    <div class="quiz-ans">
                                                                        <div class="radio"><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label></div>
                                                                        <div class="clearfix"><a href="#" class="next-que pull-right"><i class="icon-hand"></i></a></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row flex-container">
                                                <div class="quiz-intermediate">
                                                    <div class="sec-show clearfix">
                                                        <div class="col-sm-6 flex-items">
                                                            <div class="quiz-box">
                                                                <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                                <h6>Lorem Ipsum</h6>
                                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                                <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal1"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                                <div class="modal fade" id="myModal1" role="dialog">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content custom-modal">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                                <h4 class="modal-title">Congratulations!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>You have 42,000 ProCoins available.</p>
                                                                                <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                            </div>
                                                                            <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 flex-items">
                                                            <div class="quiz-box">
                                                                <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                                <h6>Lorem Ipsum</h6>
                                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                                <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal2"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                                <div class="modal fade" id="myModal2" role="dialog">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content custom-modal">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                                <h4 class="modal-title">Congratulations!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>You have 42,000 ProCoins available.</p>
                                                                                <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                            </div>
                                                                            <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 flex-items">
                                                            <div class="quiz-box">
                                                                <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                                <h6>Lorem Ipsum</h6>
                                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                                <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal3"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                                <div class="modal fade" id="myModal3" role="dialog">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content custom-modal">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                                <h4 class="modal-title">Congratulations!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>You have 42,000 ProCoins available.</p>
                                                                                <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                            </div>
                                                                            <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 flex-items">
                                                            <div class="quiz-box">
                                                                <div class="img"><img src="{{ Storage::url('img/img-dummy.png') }}" alt="quiz image"></div>
                                                                <h6>Lorem Ipsum</h6>
                                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                                <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal4"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                                                                <div class="modal fade" id="myModal4" role="dialog">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content custom-modal">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                                                                <h4 class="modal-title">Congratulations!</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>You have 42,000 ProCoins available.</p>
                                                                                <p>Click OK to consume your 250 ProCoins and play on</p>
                                                                            </div>
                                                                            <div class="modal-footer"><button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="quiz-area sec-hide">
                                                        <div class="quiz_view">
                                                            <div class="clearfix time_noti_view"><span class="time_type pull-left"><i class="icon-alarm"></i><span class="time-tag">58:32</span></span><span class="help_noti pull-right"><span class="pull-right close"><i class="icon-close"></i></span></span></div>
                                                            <div class="quiz-que">
                                                                <p class="que"><i class="icon-arrow-simple"></i>Identify the correct terminology for the piece of furniture:</p>
                                                                <div class="quiz-ans">
                                                                    <div class="question-img">
                                                                        <img src="{{ Storage::url('img/question-img.jpg') }}" title="Click to enlarge image" class="pop-me">
                                                                    </div>
                                                                    <div class="radio"><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label><label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label></div>
                                                                    <div class="clearfix"><a href="#" class="next-que pull-right"><i class="icon-hand"></i></a></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="virtual-plus text-center real-world">
                                            <h4><span>Real-world role Play</span></h4>
                                            <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                        </div>
                                        <div class="quiz-advanced quiz-sec">
                                            <div class="sec-upload clearfix sec-show">
                                                <div class="row flex-container">
                                                    <div class="col-sm-4 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><i class="icon-image"></i></div>
                                                            <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                            <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><i class="icon-video"></i></div>
                                                            <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                            <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 flex-items">
                                                        <div class="quiz-box">
                                                            <div class="img"><i class="icon-document"></i></div>
                                                            <p>Image nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt.</p>
                                                            <span title="Upload" class="btn-play btn btn-advanced">Upload</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="upload-screen quiz-box sec-hide">
                                                <h4>Purchasing Agents & Buyers (Farm Products)</h4>
                                                <span class="pull-right close"><i class="icon-close"></i></span>
                                                <div class="upload-img" id="img-preview">
                                                    <span>photo upload</span>
                                                    <input type="file" name="pic" accept="image/*" onchange="readURL(this);">
                                                </div>
                                                <button class="btn-primary" type="submit" title="Submit">Submit</button>
                                                <div class="upload-content">
                                                    <div class="no-data">
                                                        <div class="nodata-middle">
                                                            No Image found
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="virtual-plus text-center competitive-role">
                                            <h4><span>competitive role Play</span></h4>
                                            <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                            <div class="competitive-list quiz-sec">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="quiz-box">
                                                            <div class="img">
                                                                <img src="{{ Storage::url('img/abl-logo.png') }}" alt="abl logo">
                                                            </div>
                                                            <h6>Company Name</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <ul class="btn-list">
                                                                <li><a href="#" title="learn more" class="btn">learn more</a></li>
                                                                <li><a href="#" title="Apply" class="btn btn-apply">Apply</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="quiz-box">
                                                            <div class="img">
                                                                <img src="{{ Storage::url('img/ryantec-logo.png') }}" alt="ryantec logo">
                                                            </div>
                                                            <h6>Company Name</h6>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit.</p>
                                                            <ul class="btn-list">
                                                                <li><a href="#" title="learn more" class="btn">learn more</a></li>
                                                                <li><a href="#" title="Apply" class="btn btn-apply">Apply</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="overlay">
                                                    <div class="overlay-inner">
                                                        <div class="icon"><i class="icon-lock"></i></div>
                                                        <p>Complete previous section<br> to unlock</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="virtual-plus text-center challenge-play">
                                            <h4><span>challenge Play</span></h4>
                                            <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                            <div class="form-challenge">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="form-group custom-select">
                                                                <select class="form-control">
                                                                    <option value="Select a parent or mentor">Select a parent or mentor</option>
                                                                    <option value="Parent">Parent</option>
                                                                    <option value="Mentor 1">Mentor 1</option>
                                                                    <option value="Mentor 2">Mentor 2</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <button class="btn btn-submit" type="submit" title="a=Add">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="connect-block sec-progress">
                                <h2>Connect</h2>
                                <div class="bg-white">
                                    <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                        <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Leaderboard</span></span></a></li>
                                        <li class="custom-tab col-xs-6 tab-color-3"><a data-toggle="tab" href="#menu4"><span class="dt"><span class="dtc">Fans of this career</span></span></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="menu3" class="tab-pane fade in active">
                                            <div class="team-list">
                                                <div class="flex-item">
                                                    <div class="team-detail">
                                                        <div class="team-img">
                                                            <img src="{{ Storage::url('img/ellen.jpg') }}" alt="team">
                                                        </div>
                                                        <a href="#" title="Ellen Ripley"> Ellen Ripley</a>
                                                    </div>
                                                </div>
                                                <div class="flex-item">
                                                    <div class="team-point">
                                                        520,000 points
                                                        <a href="#" title="Chat">
                                                            <i class="icon-chat">
                                                                <!-- -->
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="team-list">
                                                <div class="flex-item">
                                                    <div class="team-detail">
                                                        <div class="team-img">
                                                            <img src="{{ Storage::url('img/alex.jpg') }}" alt="team">
                                                        </div>
                                                        <a href="#" title="Alex Murphy">Alex Murphy</a>
                                                    </div>
                                                </div>
                                                <div class="flex-item">
                                                    <div class="team-point">
                                                        515,000 points
                                                        <a href="#" title="Chat">
                                                            <i class="icon-chat">
                                                                <!-- -->
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="menu4" class="tab-pane fade in">
                                            <div class="team-list">
                                                <div class="flex-item">
                                                    <div class="team-detail">
                                                        <div class="team-img">
                                                            <img src="{{ Storage::url('img/ellen.jpg') }}" alt="team">
                                                        </div>
                                                        <a href="#" title="Ellen Ripley"> Ellen Ripley</a>
                                                    </div>
                                                </div>
                                                <div class="flex-item">
                                                    <div class="team-point">
                                                        520,000 points
                                                        <a href="#" title="Chat">
                                                            <i class="icon-chat">
                                                                <!-- -->
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="team-list">
                                                <div class="flex-item">
                                                    <div class="team-detail">
                                                        <div class="team-img">
                                                            <img src="{{ Storage::url('img/alex.jpg') }}" alt="team">
                                                        </div>
                                                        <a href="#" title="Alex Murphy">Alex Murphy</a>
                                                    </div>
                                                </div>
                                                <div class="flex-item">
                                                    <div class="team-point">
                                                        515,000 points
                                                        <a href="#" title="Chat">
                                                            <i class="icon-chat">
                                                                <!-- -->
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-sec-h">
                                <div class="t-table">
                                    <div class="table-cell">
                                        Ad 850 x 90
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sec-match">
                                <!--<div id="match" class="progress-cl">
                                </div>
                                <h3>Match</h3>-->
                                <div class="progress-match">
                                  <div class="barOverflow">
                                    <div class="bar"></div>
                                  </div>
                                  <span>80%</span>
                                </div>
                                <h3>Match</h3>
                            </div>
                            <div class="advanced-sec">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="">Advanced View</a>
                                            </h4>
                                        </div>
                                        <div class="panel-collapse collapse in" id="accordion1">
                                            <div class="panel-body">
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 1</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="90">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 2</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 3</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 4</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="95">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 5</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="30">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 6</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="45">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 7</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="60">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 8</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="55">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 9</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 10</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 11</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 12</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="65">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 13</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 14</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="90">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 15</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 16</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 17</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="95">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 18</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="30">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 19</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="45">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 20</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="60">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 21</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="55">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 22</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 23</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="80">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-block">
                                                    <div class="skill-name">MI parameter 24</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="75">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sec-tags">
                                <h4>Tags</h4>
                                <div class="sec-popup">
                                    <a href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom">
                                        <i class="icon-question"></i>
                                    </a>
                                    <div class="hide" id="pop1">
                                        <div class="popover-data">
                                            <a class="close popover-closer"><i class="icon-close"></i></a>
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                        </div>
                                    </div>
                                </div>
                                <ul class="tag-list">
                                    <li>Lorem ipsum</li>
                                    <li>Dolore</li>
                                    <li>Curabitur</li>
                                    <li>Vulputate</li>
                                    <li>Dignissim</li>
                                    <li>Turpis</li>
                                    <li>Lobortis</li>
                                    <li>Placerat</li>
                                    <li>Commodo</li>
                                    <li>Lorem ipsum</li>
                                    <li>Dolore</li>
                                    <li>Curabitur</li>
                                    <li>Vulputate</li>
                                    <li>Dignissim</li>
                                    <li>Turpis</li>
                                    <li>Lobortis</li>
                                    <li>Placerat</li>
                                    <li>Commodo</li>
                                </ul>
                            </div>
                            <div class="ad-v">
                                <div class="t-table">
                                    <div class="table-cell">
                                        Ad 343 x 400
                                    </div>
                                </div>
                            </div>
                            <div class="ad-v-2">
                                <div class="t-table">
                                    <div class="table-cell">
                                        Ad 343 x 800
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="teaser-overlay">
                        <p><a href="{{ url('/teenager/login') }}" title="Know More" class="btn btn-border">Know More <span><i class="icon-hand-simple"></i></span></a></p>
                    </div>
                </div>
            </section>
        </div>
        <!-- mid section end-->
    </div>
@stop
@section('script')

<script src="{{ asset('frontend/js/jquery.autocomplete.min.js') }}"></script>

<?php
$finalSearchArray = '';
$suggestion = '';
if (!empty($allProfessions)) {
    foreach ($allProfessions as $value) {
        $searchArray[] = array('value' => $value->pf_name, 'slug' => $value->pf_slug);
    }
    $finalSearchArray = json_encode($searchArray);
}
?>

<script>
    $(document).ready(function() {
        $('.play-icon').click(function() {
            $(this).hide();
            $('video').show();
            $('img').hide();
        });

        $('#iframe-video-click').on('click', function(ev) {
            var youtubeVideo = '{{$videoCode}}';
            if(youtubeVideo == '') {
                $("#dropbox_video_player")[0].play();
            } else {
                $('img').hide();
                $('iframe').show();
                $("#iframe-video")[0].src += "&autoplay=1";
                ev.preventDefault();
            }
        });

        $('.btn-next').click(function() {
            $('.front_page').hide();
            $('.promise-plus-overlay').show(500);
        })
        $('.promise-plus-overlay .close').click(function() {
            $('.promise-plus-overlay').hide();
            $('.front_page').show(500);
        })
        $('.btn-basic').click(function() {
            $('.quiz-basic .sec-show').addClass('hide');
            $('.quiz-basic .basic-quiz-area').addClass('active');
        })
        $('.quiz-box .close').click(function() {
            $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-intermediate').click(function(){
            $('.quiz-intermediate .sec-show').addClass('hide');
            $('.quiz-intermediate .sec-hide').addClass('active');
        })
        $('.quiz-area .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-advanced').click(function(){
            $('.quiz-advanced .sec-show').addClass('hide');
            $('.quiz-advanced .sec-hide').addClass('active');
        })
        $('.upload-screen .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });

        $(".progress-match").each(function(){

          var $bar = $(this).find(".bar");
          var $val = $(this).find("span");
          var perc = parseInt( $val.text(), 10);

          $({p:0}).animate({p:perc}, {
            duration: 3000,
            easing: "swing",
            step: function(p) {
              $bar.css({
                transform: "rotate("+ (45+(p*1.8)) +"deg)", // 100%=180° so: ° = % * 1.8
                // 45 is to add the needed rotation to have the green borders at the bottom
              });
              $val.text(p|0);
            }
          });
        });
    });

    $(window).bind("load", function() {
    var currencies = <?php echo $finalSearchArray ?>
        // setup autocomplete function pulling from currencies[] array
        $('#autocomplete').autocomplete({
            lookup: currencies,
            onSelect: function(suggestion) {
                window.location.href = "<?php echo url('career-detail/') ?>/" + suggestion.slug;
            }
        });

    });
</script>
@stop