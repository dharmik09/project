@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins gift</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading gift-heading">
            <div class="container">
                <h1 class="font-blue">gift</h1>
                <p>You have <strong class="font-blue">0</strong> gifts</p>
                <div class="procoin-form gift-form">
                    <form>
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="bg-white procoins-gift">
                <div class="gift-table table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Gifted to</th>
                                <th>Gifted ProCoins</th>
                                <th>Gifted Date</th>
                            </tr>
                        </thead>
                        <tbody class="no-data">
                            <tr>
                                <td>John</td>
                                <td>Doe</td>
                                <td>john@example.com</td>
                            </tr>
                            <tr>
                                <td>Mary</td>
                                <td>Moe</td>
                                <td>mary@example.com</td>
                            </tr>
                            <tr>
                                <td>July</td>
                                <td>Dooley</td>
                                <td>july@example.com</td>
                            </tr>
                        </tbody>

                    </table>
                    <div class="no-data">
                        <div class="data-content">
                            <div>
                                <i class="icon-empty-folder"></i>
                            </div>
                            <p>No data found</p>
                        </div>
                    </div>
                </div>
                <div class="sec-bttm"><!-- --></div>
            </div>
        </div>
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
@stop