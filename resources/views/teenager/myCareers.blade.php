@extends('layouts.teenager-master')

@push('script-header')
    <title>My Careers</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="container">
            <div class="careers-container">
                <div class="top-heading text-center">
                    <h1>my careers</h1>
                    <p>You have completed <strong>16 of 29</strong> careers from your shortlist</p>
                </div>
                <div class="sec-filter">
                    <div class="row">
                        <div class="col-md-2 text-right">
                            <span>Filter by:</span>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select">
                                <select tabindex="8" class="form-control">
                                  <option value="all categories">all categories</option>
                                  <option value="Strong match">Strong match</option>
                                  <option value="Potential match">Potential match</option>
                                  <option value="Unlikely match">Unlikely match</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group custom-select bg-blue">
                                <select tabindex="8" class="form-control">
                                      <option value="all careers">all careers</option>
                                      <option value="agriculture">agriculture</option>
                                      <option value="conservation">conservation</option>
                                      <option value="Veterinarians">Veterinarians</option>
                                                                     </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group search-bar clearfix">
                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                                <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- category list-->
                <section class="sec-category">
                    <h2>Agriculture, Food &amp; natural Resources</h2>
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
                    <div class="category-list">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url('img/agriculture.jpg') }} ')"></div>
                                        <figcaption>
                                            <a href="#" title="Conservation Scientists">Conservation Scientists</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url('img/agriculture-2.jpg') }} ')"></div>
                                        <figcaption>
                                            <a href="#" title="Agricultural & Food Science Technicians">Agricultural & Food Science Technicians</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url('img/agriculture-equipment.jpg') }}')"></div>
                                        <figcaption>
                                            <a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="category-block match-potential">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url('img/vetenaries.jpg') }} ')"></div>
                                        <figcaption>
                                            <a href="#" title="Veterinarians">Veterinarians</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>

                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-unlikely">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url('img/conservation.jpg') }} ')"></div>
                                        <figcaption>
                                            <a href="#" title="Conservation Scientists">Conservation Scientists</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>

                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- category list end
                career category
                <section class="sec-category">
                    <h2>Career Category Title</h2>
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
                    <div class="category-list">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url(img/agriculture.jpg)"></div>
                                        <figcaption>
                                            <a href="#" title="Conservation Scientists">Conservation Scientists</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url(img/agriculture-2.jpg)"></div>
                                        <figcaption>
                                            <a href="#" title="Agricultural & Food Science Technicians">Agricultural & Food Science Technicians</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-strong">
                                    <figure>
                                        <div class="category-img" style="background-image: url(img/agriculture-equipment.jpg)"></div>
                                        <figcaption>
                                            <a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="category-block match-potential">
                                    <figure>
                                        <div class="category-img" style="background-image: url(img/vetenaries.jpg)"></div>
                                        <figcaption>
                                            <a href="#" title="Veterinarians">Veterinarians</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>

                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="category-block match-unlikely">
                                    <figure>
                                        <div class="category-img" style="background-image: url(img/conservation.jpg)"></div>
                                        <figcaption>
                                            <a href="#" title="Conservation Scientists">Conservation Scientists</a>
                                        </figcaption>
                                        <span class="complete">Complete</span>

                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                career category end
                mid section end-->
            </div>
        </div>
    </div>
@stop
