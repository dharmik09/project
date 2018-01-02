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
                        <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                        <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--sec progress-->
    <section class="sec-progress sec-connection">
        <div class="container">
            <div class="bg-white my-progress">
                <!--<ul class="nav nav-tabs progress-tab clearfix">
                    <li class="acheivement active col-md-6"><a data-toggle="tab" href="#menu1">Find New Connections </a></li>
                    <li class="career col-md-6"><a data-toggle="tab" href="#menu2">My Connections </a></li>
                </ul>-->
                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Find New Connections</span></span></a></li>
                    <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">My Connections</span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        <p class="text-center"><a href="#" title="load more" class="load-more">load more</a></p>
                    </div>
                    <div id="menu2" class="tab-pane fade">
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
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
                                    <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                                </div>
                            </div>
                        </div>
                        <p class="text-center"><a href="#" title="load more" class="load-more">load more</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--sec progress end-->
    </div>
    <!-- mid section end-->
@stop