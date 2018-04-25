@extends('layouts.home-master')

@push('script-header')
    <title>{{ $multipleIntelligence->title }}</title>
@endpush

@section('content')
    <div class="bg-offwhite ">
        <!-- mid section starts-->
        <div class="inner-banner">
            <div class="container">
                <div class="sec-banner multiple-banner" style="background-image: url('{{ Storage::url($miThumbImageUploadPath . $multipleIntelligence->logo) }}');">
                    <!-- -->
                </div>
            </div>
        </div>
        <!--introduction text-->
        <div class="seo-teaser">
        <div class="container">
            <section class="introduction-text">
                <div class="heading-sec clearfix">
                    <h1>{{ $multipleIntelligence->title }}</h1>
                    <div class="sec-popup">
                        <a href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                        <div class="hide" id="pop1">
                            <div class="popover-data">
                                <a class="close popover-closer"><i class="icon-close"></i></a>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="custompop" rel="popover" data-popover-content="#pop2" data-placement="bottom"><i class="icon-share"></i></a>
                        <div class="hide" id="pop2">
                            <div class="socialmedia-icon">
                                <p>Share  on:</p>
                                <ul class="social-icon clearfix">
                                    <li><a href="#" title="facebook" class="facebook"><i class="icon-facebook"></i></a></li>
                                    <li><a href="#" title="Twitter" class="twitter"><i class="icon-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <p>{{ $multipleIntelligence->description }}</p>
            </section>
        </div>
        <!--introduction text end-->
        <div class="teaser-content">
        <!--related careers section-->
        <div class="related-careers">
            <div class="container">
                <div class="bg-white">
                    <div class="career-heading clearfix">
                        <h4>Related careers:</h4>
                        <div class="pull-right">
                            <div class="sec-popup">
                                 <a href="javascript:void(0);" class="custompop" rel="popover" data-popover-content="#pop3" data-placement="bottom"><i class="icon-share"></i></a>
                                <div class="hide" id="pop3">
                                    <div class="socialmedia-icon">
                                        <p>Share  on:</p>
                                        <ul class="social-icon clearfix">
                                            <li><a href="#" title="facebook" class="facebook"><i class="icon-facebook"></i></a></li>
                                            <li><a href="#" title="Twitter" class="twitter"><i class="icon-twitter"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="match-list paddng-right">
                            <li><span class="number match-strong">4</span> Strong match</li>
                            <li><span class="number match-potential">5</span> Potential match</li>
                            <li><span class="number match-unlikely">4</span> Unlikely match</li>
                        </ul>
                    </div>
                    <ul class="career-list">
                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a>
                            <a href="#" class="complete"><span>Complete</span></a>
                        </li>
                        <li class="match-potential"><a href="#" title="Purchasing Agents & Buyers">Purchasing Agents &amp; Buyers</a></li>
                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a>
                            <a href="#" class="complete"><span>Complete</span></a>
                        </li>
                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                        <li class="match-potential"><a href="#" title="Environmental Engineers">Environmental Engineers</a></li>
                    </ul>
                    <p class="text-center"><a href="#" title="see more">see more</a></p>
                </div>
            </div>

        </div>
        <!--</div>-->
        <!--related careers section end-->
        <!--team section-->
        <section class="sec-team">
            <div class="container">
                <div class="bg-white">
                    <h4>Meet the (Interest name) gurus:</h4>
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
                    <div class="team-list">
                        <div class="flex-item">
                            <div class="team-detail">
                                <div class="team-img">
                                    <img src="{{ Storage::url('img/diana.jpg') }}" alt="team">
                                </div>
                                <a href="#" title=" Diana Prince"> Diana Prince</a>
                            </div>
                        </div>
                        <div class="flex-item">
                            <div class="team-point">
                                511,000 points
                                <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-list">
                        <div class="flex-item">
                            <div class="team-detail">
                                <div class="team-img">
                                    <img src="{{ Storage::url('img/peter.jpg') }}" alt="team">
                                </div>
                                <a href="#" title="Peter Parker">Peter Parker</a>
                            </div>
                        </div>
                        <div class="flex-item">
                            <div class="team-point">
                                509,000 points
                                <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-list">
                        <div class="flex-item">
                            <div class="team-detail">
                                <div class="team-img">
                                    <img src="{{ Storage::url('img/rico.jpg') }}" alt="team">
                                </div>
                                <a href="#" title="Rico Frost">Rico Frost</a>
                            </div>
                        </div>
                        <div class="flex-item">
                            <div class="team-point">
                                508,000 points
                                <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
                            </div>
                        </div>
                    </div>
                    <p class="text-center"><a href="#" title="see more">see more</a></p>
                </div>
            </div>
        </section>
        <!--team section end-->
        </div>
        <!-- mid section end-->
        <!-- <div class="teaser-overlay">
            <p><a href="#" title="Read More" class="btn btn-border">Read More</a></p>
        </div> -->
        <div class="teaser-overlay">
            <p><a href="{{ url('/teenager/login') }}" title="Know More" class="btn btn-border">Know More <span><i class="icon-hand-simple"></i></span></a></p>
        </div>
        </div>
    </div>
@stop