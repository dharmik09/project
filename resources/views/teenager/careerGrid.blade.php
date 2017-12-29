@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="container">
            <div class="careers-container careers-map">
                <div class="top-heading text-center">
                    <h1>careers</h1>
                    <p>You have completed <strong class="font-blue">212 of 550</strong> careers</p>
                </div>
                <div class="sec-filter">
                    <div class="row">
                        <div class="col-md-2 text-right"><span>Filter by:</span></div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select"><select tabindex="8" class="form-control"><option value="all categories">all categories</option><option value="Strong match">Strong match</option><option value="Potential match">Potential match</option><option value="Unlikely match">Unlikely match</option></select></div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select bg-blue"><select tabindex="8" class="form-control"><option value="all careers">all careers</option><option value="agriculture">agriculture</option><option value="conservation">conservation</option><option value="Veterinarians">Veterinarians</option></select></div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group search-bar clearfix"><input type="text" placeholder="search" tabindex="1" class="form-control search-feild"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                        </div>
                    </div>
                </div>
                <!-- mid section-->
                <section class="career-content">
                        <div class="bg-white">
                            <div class="clearfix">
                                <h2>Agriculture, food & natural resources</h2>
                                <div class="pull-right sec-icon">
                                    <span><a href="#" title="List view"> <i class="icon-list"></i></a></span>
                                    <span><a href="#" title="Grid view"> <i class="icon-grid"></i></a></span>
                                </div>
                            </div>
                            <div class="banner-landing banner-career">
                                <div class="">
                                    <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video"><img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon"></a></div>
                                </div><iframe width="100%" height="100%" src="https://www.youtube.com/embed/NpEaa2P7qZI?autoplay=1" frameborder="0" allowfullscreen id="iframe-video"></iframe></div>
                            <section class="sec-category">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>You have completed <strong>4 of 12</strong> careers</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <ul class="match-list">
                                                <li><span class="number match-strong">4</span> Strong match</li>
                                                <li><span class="number match-potential">5</span> Potential match</li>
                                                <li><span class="number match-unlikely">4</span> Unlikely match</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="career-map">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-strong"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry<br> and Fish Cutters<br> and Trimmers
                                            </a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span></div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-potential"><a href="#" title="Purchasing Agents & Buyers">Purchasing<br> Agents & Buyers</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill<br> Operators, Oil<br> and Gas</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and<br> Food Science<br> Technicians</a><span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-strong"><a href="#" title="Agricultural Equipment Operators">Agricultural<br> Equipment<br> Operators</a><span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-strong"><a href="#" title="Conservation Scientists">Conservation<br> Scientists</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-potential"><a href="#" title="Environmental Engineers">Environmental<br> Engineers</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-unlikely"><a href="#" title="Food Scientists and Technologists">Food Scientists<br> and<br> Technologists</a><span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational<br> Health and Safety<br> Specialists</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-potential"><a href="#" title="Plant Scientists and Botanists">Plant Scientists<br> and Botanists</a> <span class="complete"><a href="#" title="Completed"><i class="icon-thumb"></i></a></span>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-strong"><a href="#" title="Soil Conservationists">Soil <br>Conservationists</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="category match-potential"><a href="#" title="Veterinarians">Veterinarians</a>
                                            <div class="overlay">
                                                <span class="salary">Salary: $32,500</span>
                                                <span class="assessment">Assessment: High Growth</span>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                </section>
                <!-- mid section end-->
            </div>
        </div>
    </div>
@stop
@section('script') 
    <script>
        $('.play-icon').click(function() {
            $(this).hide();
            $('iframe').show();
        })
    </script>
@stop