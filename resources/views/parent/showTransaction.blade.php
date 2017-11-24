@extends('layouts.parent-master')

@section('content')

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
    @endif
    @if ($message = Session::get('success'))
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
    @endif
    @if (count($errors) > 0)
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
                <a href="{{url('parent/my-coins')}}" class="back_me history_back"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;
                    <span>Back</span>
                </a>
            </div>
            <h1><span class="title_border">{{trans('labels.transactionhistory')}}</span></h1>
        </div>
        <div class="my_teens_content clearfix">
            <div class="my_teens_inner">
                <div class="table_container">
                    <table class="sponsor_table">
                        <tr>
                            <th>{{trans('labels.teentblheadname')}}</th>
                            <th>{{trans('labels.emaillbl')}}</th>
                            <th>{{trans('labels.transectionid')}}</th>
                            <th>{{trans('labels.paidamount')}}</th>
                            <th>{{trans('labels.formcurrency')}}</th>
                            <th>{{trans('labels.formlblcoins')}}</th>
                            <th>{{trans('labels.transectiondate')}}</th>
                        </tr>
                        @if(!empty($transactionDetail))
                        @foreach($transactionDetail as $key=>$data)
                        <tr>
                            <td>
                                {{$data->tn_billing_name}}
                            </td>
                            <td>
                                {{$data->tn_email}}
                            </td>
                            <td>
                                {{$data->tn_transaction_id}}
                            </td>
                            <td>
                                {{$data->tn_amount}}
                            </td>
                            <td>
                                {{$data->tn_currency}}
                            </td>
                            <td>
                                <?php echo number_format($data->tn_coins); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->tn_trans_date)); ?>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="8">No data found</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
@section('script')
<script>
    $(".table_container").mCustomScrollbar({axis:"x"});
</script>

@stop