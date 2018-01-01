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
                <p>You have <strong class="font-blue">216</strong> connections</p>
            </div>
            <div class="sec-filter network-filter">
                <div class="row">
                    <div class="col-md-4 col-xs-6 sort-feild">
                        <label>Sort by:</label>
                        <div class="form-group custom-select">
                            <select tabindex="1" class="form-control">
                                <option value="most recent">most recent</option>
                                <option value="frequently">frequently</option>
                                <option value="ocassionally">ocassionally</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6 sort-feild sort-filter">
                        <label>Filter by:</label>
                        <div class="form-group custom-select">
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
            <!-- network list-->
            <section class="connection-list">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Mike">Mike</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/ellen.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Maria">Maria</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Mike">Mike</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/ellen.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="ellen">ellen</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/mike.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="mike">mike</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/ellen.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="ellen">ellen</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/diana.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Sarah">Sarah</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
                        <div class="connection-block">
                            <figure>
                                <div class="connection-img" style="background-image: url('{{ Storage::url('img/alex.jpg') }} ')">
                                    <div class="overlay">
                                        <ul>
                                            <li><a href="#" title="user"><i class="icon-pro-user"></i></a></li>
                                            <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <figcaption><a href="#" title="Joe">Joe</a></figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </section>
            <!-- network list end-->
        </div>
        <!-- mid section end-->
    </div>
@stop

