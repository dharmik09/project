@extends('layouts.teenager-master')

@push('script-header')
    <title>Procoins History</title>
@endpush

@section('content')
    <!--mid content-->
    <div class="bg-offwhite">
        <div class="procoin-heading gift-heading history-heading">
            <div class="container">
                <h1 class="font-blue">history</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sed risus consequat, volutpat dui id, vestibulum turpis. </p>
            </div>
        </div>
        <!--procoins sec-->
        <div class="container">
            <div class="procoins-history">
                <h2>Transactions</h2>
                <div class="bg-white procoins-gift">
                    <div class="gift-table table-responsive history-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Transaction ID</th>
                                    <th>Paid Amount</th>
                                    <th>Currency</th>
                                    <th>ProCoins</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--<tr>
                                    <td>John</td>
                                    <td>john@example.com</td>
                                    <td>123456789</td>
                                    <td>750</td>
                                    <td>350</td>
                                    <td>250000</td>
                                    <td>15/11/2017</td>
                                </tr>
                                <tr>
                                    <td>Mary</td>
                                    <td>mary@example.com</td>
                                    <td>123456789</td>
                                    <td>750</td>
                                    <td>350</td>
                                    <td>250000</td>
                                    <td>15/11/2017</td>
                                </tr>
                                <tr>
                                    <td>July</td>
                                    <td>july@example.com</td>
                                    <td>123456789</td>
                                    <td>750</td>
                                    <td>350</td>
                                    <td>250000</td>
                                    <td>15/11/2017</td>
                                </tr>-->
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
                </div>
            </div>
        </div>

        <!--sec-consumption-->
        <section class="sec-progress sec-consumption">
            <div class="container">
                <h2>Consumption</h2>
                <div class="bg-white my-progress procoins-gift">
                    <!--<ul class="nav nav-tabs progress-tab">
                        <li class="acheivement active"><a data-toggle="tab" href="#menu1">Promise Plus</a></li>
                        <li class="career"><a data-toggle="tab" href="#menu2">Learning Guidance</a></li>
                        <li class="connection"><a data-toggle="tab" href="#menu3">L4 Concept Template</a></li>
                    </ul>-->
                    <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                        <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Promise Plus</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Learning Guidance</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">L4 Concept Template</span></span></a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="menu1" class="tab-pane fade in active">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr>
                                                <td>John</td>
                                                <td>john@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>Mary</td>
                                                <td>mary@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>July</td>
                                                <td>july@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>-->
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
                            </div>

                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--<tr>
                                                <td>John</td>
                                                <td>john@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>Mary</td>
                                                <td>mary@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>July</td>
                                                <td>july@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>-->
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Transaction ID</th>
                                            <th>Paid Amount</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr>
                                                <td>John</td>
                                                <td>john@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>Mary</td>
                                                <td>mary@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>
                                            <tr>
                                                <td>July</td>
                                                <td>july@example.com</td>
                                                <td>123456789</td>
                                                <td>750</td>
                                                <td>350</td>
                                            </tr>-->
                                    </tbody>
                                </table>
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                        <p>No data found</p>
                                    </div>
                                    <div class="sec-bttm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--sec-consumption end-->
        <!--procoins sec end-->
    </div>
    <!--mid content end-->
@stop