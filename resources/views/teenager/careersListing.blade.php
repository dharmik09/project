@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')

<div class="bg-offwhite">
<!-- mid section starts-->
<div class="container">
    <div class="careers-list">
        <div class="top-heading text-center listing-heading">
            <h1>careers</h1>
            <p>You have completed <strong class="font-blue">212 of 550</strong> careers</p>
        </div>
        <div class="sec-filter listing-filter">
            <div class="row">
                <div class="col-md-2 text-right"><span>Filter by:</span></div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group custom-select"><select tabindex="8" class="form-control"><option value="all categories">all categories</option><option value="Strong match">Strong match</option><option value="Potential match">Potential match</option><option value="Unlikely match">Unlikely match</option></select></div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group custom-select bg-blue"><select tabindex="8" class="form-control"><option value="all careers">all careers</option><option value="agriculture">agriculture</option><option value="conservation">conservation</option><option value="Veterinarians">Veterinarians</option></select></div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group search-bar clearfix"><input type="text" placeholder="search" tabindex="1" class="form-control search-feild"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                </div>
            </div>
        </div>
        <!-- mid section-->
        <section class="career-content listing-content">
            <div class="bg-white">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="collapsed">Agriculture, food & natural resources</a> <a href="{{ url('teenager/career-grid') }}" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse in" id="accordion1">
                            <div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
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
                                    </div>
                                    <ul class="career-list">
                                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a><a href="#" class="complete"><span>Complete <i class="icon-thumb"></i></span></a></li>
                                        <li class="match-potential"><a href="#" title="Purchasing Agents &amp; Buyers">Purchasing Agents &amp; Buyers</a></li>
                                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Environmental Engineers">Environmental Engineers</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely complete-feild"><a href="#" title="Food Scientists and Technologists">Food Scientists and Technologists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational Health and Safety Specialists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Plant Scientists and Botanists">Plant Scientists and Botanists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Soil Conservationists">Soil Conservationists</a></li>
                                        <li class="match-potential"><a href="#" title="Environmental EngineersVeterinarians">Veterinarians</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion2" class="collapsed">Career Category</a> <a href="#" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse" id="accordion2">
                            <div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
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
                                    </div>
                                    <ul class="career-list">
                                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a><a href="#" class="complete"><span>Complete <i class="icon-thumb"></i></span></a></li>
                                        <li class="match-potential"><a href="#" title="Purchasing Agents &amp; Buyers">Purchasing Agents &amp; Buyers</a></li>
                                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Environmental Engineers">Environmental Engineers</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely complete-feild"><a href="#" title="Food Scientists and Technologists">Food Scientists and Technologists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational Health and Safety Specialists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Plant Scientists and Botanists">Plant Scientists and Botanists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Soil Conservationists">Soil Conservationists</a></li>
                                        <li class="match-potential"><a href="#" title="Environmental EngineersVeterinarians">Veterinarians</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion3" class="collapsed">Career Category</a> <a href="#" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse" id="accordion3">
                            <div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
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
                                    </div>
                                    <ul class="career-list">
                                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a><a href="#" class="complete"><span>Complete <i class="icon-thumb"></i></span></a></li>
                                        <li class="match-potential"><a href="#" title="Purchasing Agents &amp; Buyers">Purchasing Agents &amp; Buyers</a></li>
                                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Environmental Engineers">Environmental Engineers</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely complete-feild"><a href="#" title="Food Scientists and Technologists">Food Scientists and Technologists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational Health and Safety Specialists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Plant Scientists and Botanists">Plant Scientists and Botanists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Soil Conservationists">Soil Conservationists</a></li>
                                        <li class="match-potential"><a href="#" title="Environmental EngineersVeterinarians">Veterinarians</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion4" class="collapsed">Career Category</a> <a href="#" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse" id="accordion4">
                            <div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
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
                                    </div>
                                    <ul class="career-list">
                                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a><a href="#" class="complete"><span>Complete <i class="icon-thumb"></i></span></a></li>
                                        <li class="match-potential"><a href="#" title="Purchasing Agents &amp; Buyers">Purchasing Agents &amp; Buyers</a></li>
                                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Environmental Engineers">Environmental Engineers</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely complete-feild"><a href="#" title="Food Scientists and Technologists">Food Scientists and Technologists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational Health and Safety Specialists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Plant Scientists and Botanists">Plant Scientists and Botanists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Soil Conservationists">Soil Conservationists</a></li>
                                        <li class="match-potential"><a href="#" title="Environmental EngineersVeterinarians">Veterinarians</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion5" class="collapsed">Career Category</a> <a href="#" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse" id="accordion5">
                            <div class="panel-body">
                                <div class="related-careers careers-tag">
                                    <div class="career-heading clearfix">
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
                                    </div>
                                    <ul class="career-list">
                                        <li class="match-strong complete-feild"><a href="#" title="Meat, Poultry and Fish Cutters and Trimmers">Meat, Poultry and Fish Cutters and Trimmers</a><a href="#" class="complete"><span>Complete <i class="icon-thumb"></i></span></a></li>
                                        <li class="match-potential"><a href="#" title="Purchasing Agents &amp; Buyers">Purchasing Agents &amp; Buyers</a></li>
                                        <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
                                        <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
                                        <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Environmental Engineers">Environmental Engineers</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely complete-feild"><a href="#" title="Food Scientists and Technologists">Food Scientists and Technologists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-unlikely"><a href="#" title="Occupational Health and Safety Specialists">Occupational Health and Safety Specialists</a></li>
                                        <li class="match-potential complete-feild"><a href="#" title="Plant Scientists and Botanists">Plant Scientists and Botanists</a><a href="#" class="complete thumb"><span><i class="icon-thumb"></i></span></a></li>
                                        <li class="match-strong"><a href="#" title="Soil Conservationists">Soil Conservationists</a></li>
                                        <li class="match-potential"><a href="#" title="Environmental EngineersVeterinarians">Veterinarians</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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