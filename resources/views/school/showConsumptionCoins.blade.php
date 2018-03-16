@extends('layouts.school-master') @section('content')

<div>
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-8 col-md-offset-2 invalid_pass_error">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable danger">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-8 col-md-offset-2 invalid_pass_error">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable success_msg">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container">
        <div class="pricing_title">
            <div class="my_teens_content ">
                <div class="btn_cont gift_modal_page">
                    <a href="{{ url('school/get-gift-coins') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-gift-coins') ? 'active' : '' }}">{{trans('labels.giftcoins')}}</a>
                    <a href="{{ url('school/get-consumption') }}" class="btn primary_btn gift_history tab_bttn {{ Request::is('school/get-consumption') ? 'active' : '' }}">{{trans('labels.consumption')}}</a>
                </div>
            </div>
            <h1><span class="title_border">{{trans('labels.consumedcoins')}}</span></h1>
        </div>
        <div class="my_teens_content clearfix">
            <div class="bg-white procoins-gift">
                <div class="gift-table table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{trans('labels.component')}}</th>
                                <th>{{trans('labels.consumedcoins')}}</th>
                                <th>{{trans('labels.consumedcoinsdate')}}</th>
                                <th>{{trans('labels.enddate')}}</th>
                            </tr>
                        </thead>
                        @if(!empty($deductedCoinsDetail) && count($deductedCoinsDetail) > 0)
                        <tbody>
                            @foreach($deductedCoinsDetail as $key=>$data)
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
                            <tr>
                                <td colspan="4">
                                    @if (isset($deductedCoinsDetail) && !empty($deductedCoinsDetail))
                                          <?php echo $deductedCoinsDetail->render(); ?>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
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
                    </table>
                </div>
                <div class="sec-bttm"><!-- --></div>
            </div>
        </div>

    </div>
</div>
@stop @section('script')
<script>
    $(".table_container").mCustomScrollbar({
        axis: "x"
    });

</script>

@stop
