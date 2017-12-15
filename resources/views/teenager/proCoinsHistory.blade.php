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
                                    <th>{{trans('labels.teentblheadname')}}</th>
                                    <th>{{trans('labels.emaillbl')}}</th>
                                    <th>{{trans('labels.transectionid')}}</th>
                                    <th>{{trans('labels.paidamount')}}</th>
                                    <th>{{trans('labels.formcurrency')}}</th>
                                    <th>{{trans('labels.formlblcoins')}}</th>
                                    <th>{{trans('labels.transectiondate')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($transactionDetail) && count($transactionDetail) > 0)
                                    @foreach($transactionDetail as $key=>$data)
                                    <tr>
                                        <td>{{$data->tn_billing_name}}</td>
                                        <td>{{$data->tn_email}}</td>
                                        <td>{{$data->tn_transaction_id}}</td>
                                        <td>{{$data->tn_amount}}</td>
                                        <td>{{$data->tn_currency}}</td>
                                        <td><?php echo number_format($data->tn_coins); ?></td>
                                        <td><?php echo date('d M Y', strtotime($data->tn_trans_date)); ?></td>
                                    </tr>
                                    @endforeach
                                @else
                                <div class="no-data">
                                    <div class="data-content">
                                        <div>
                                            <i class="icon-empty-folder"></i>
                                        </div>
                                    <p>No data found</p>
                                    </div>
                                </div>
                                @endif
                            </tbody>
                        </table>
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
                        <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">{{trans('labels.level4promiseplus')}}</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">{{trans('labels.learninguidance')}}</span></span></a></li>
                        <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">{{trans('labels.l4concept')}}</span></span></a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="menu1" class="tab-pane fade in active">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{trans('labels.component')}}</th>
                                            <th>{{trans('labels.profession')}}</th>
                                            <th>{{trans('labels.consumedcoins')}}</th>
                                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                                            <th>{{trans('labels.enddate')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($deductedCoinsDetail) && count($deductedCoinsDetail) > 0)
                                        @foreach($deductedCoinsDetail as $key=>$data)
                                        <tr>
                                            <td>
                                                {{$data->pc_element_name}}
                                            </td>
                                            <td>
                                                @if ($data->pf_name == '')
                                                    -
                                                @else
                                                    {{$data->pf_name}}
                                                @endif
                                            </td>
                                            <td>
                                                <?php echo number_format($data->dc_total_coins); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                            <div class="no-data">
                                                <div class="data-content">
                                                    <div>
                                                        <i class="icon-empty-folder"></i>
                                                    </div>
                                                    <p>No data found</p>
                                                </div>
                                                <div class="sec-bttm"></div>
                                            </div>
                                        @endif
                                        <tr>
                                            <td colspan="8">
                                                @if (isset($deductedCoinsDetail) && !empty($deductedCoinsDetail))
                                                      <?php echo $deductedCoinsDetail->render(); ?>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{trans('labels.component')}}</th>
                                            <th>{{trans('labels.consumedcoins')}}</th>
                                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                                            <th>{{trans('labels.enddate')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($deductedCoinsDetailLS) && count($deductedCoinsDetailLS) > 0)
                                        @foreach($deductedCoinsDetailLS as $key=>$data)
                                        <tr>
                                            <td>
                                                {{$data->pc_element_name}}
                                            </td>
                                            <td>
                                                <?php echo number_format($data->dc_total_coins); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <div class="no-data">
                                            <div class="data-content">
                                                <div>
                                                    <i class="icon-empty-folder"></i>
                                                </div>
                                                <p>No data found</p>
                                            </div>
                                            <div class="sec-bttm"></div>
                                        </div> 
                                        @endif
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                            <div class="gift-table table-responsive consumption-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{trans('labels.profession')}}</th>
                                            <th>{{trans('labels.concept')}}</th>
                                            <th>{{trans('labels.consumedcoins')}}</th>
                                            <th>{{trans('labels.consumedcoinsdate')}}</th>
                                            <th>{{trans('labels.enddate')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($deductedTemplateCoinsDetail) && count($deductedTemplateCoinsDetail) > 0)
                                        @foreach($deductedTemplateCoinsDetail as $key=>$value)
                                        <tr>
                                            <td>
                                                @if ($value->pf_name == '')
                                                    -
                                                @else
                                                    {{$value->pf_name}}
                                                @endif
                                            </td>
                                            <td>
                                                {{$value->gt_template_title}}
                                            </td>
                                            <td>
                                                <?php echo number_format($value->tdc_total_coins); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($value->tdc_start_date)); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d M Y', strtotime($value->tdc_end_date)); ?>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <div class="no-data">
                                            <div class="data-content">
                                                <div>
                                                    <i class="icon-empty-folder"></i>
                                                </div>
                                                <p>No data found</p>
                                            </div>
                                            <div class="sec-bttm"></div>
                                        </div>
                                        @endif
                                        <tr>
                                            <td colspan="5">
                                                @if (isset($deductedTemplateCoinsDetail) && !empty($deductedTemplateCoinsDetail))
                                                      <?php echo $deductedTemplateCoinsDetail->render(); ?>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
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