@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.invoice')}}
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
         <div class="col-md-12">
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="box box-info">
                <ul class="nav nav-tabs">
                    <li class="<?php if(empty($searchParamArray)) {echo 'active';} if(isset($searchParamArray['type']) && $searchParamArray['type'] == 1){ echo 'active';}?>"><a data-toggle="tab" href="#teenager">Teenager</a></li>
                    <li class="<?php if(isset($searchParamArray['type']) && $searchParamArray['type'] == 2){ echo 'active';}?>"><a data-toggle="tab" href="#parent">Parent</a></li>
                    <li class="<?php if(isset($searchParamArray['type']) && $searchParamArray['type'] == 4){ echo 'active';}?>"><a data-toggle="tab" href="#sponsor">Sponsor</a></li>
                </ul>

                <div class="tab-content">
                    <div id="teenager" class="tab-pane fade <?php if(empty($searchParamArray) or $searchParamArray['type'] == 1) {echo 'in active';}?>">
                        <div class="box-body">
                            <table id="listTeenager" class="table table-striped display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('labels.serialnumber')}}</th>
                                        <th>{{trans('labels.formlblid')}}</th>
                                        <th>{{trans('labels.formlblname')}}</th>
                                        <th>{{trans('labels.formlblbillingname')}}</th>
                                        <th>{{trans('labels.formlblbillingemail')}}</th>
                                        <th>{{trans('labels.paidamount')}}</th>
                                        <th>{{trans('labels.consumedcoins')}}</th>
                                        <th>{{trans('labels.formlbltransid')}}</th>
                                        <th>{{trans('labels.formlblcreatedate')}}</th>
                                        <th>{{trans('labels.formlblupdatedate')}}</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $serialno = 0; ?>
                                    @forelse($invoiceDetailForTeenager as $invoice)
                                    <?php $serialno++; ?>
                                    <tr>
                                        <td>
                                            <?php echo $serialno; ?>
                                        </td>
                                        <td>
                                            <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                            {{$invoice->i_invoice_id}}
                                        </td>
                                        <td>
                                            {{$invoice->t_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_billing_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_email}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_amount}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_coins}}
                                        </td>
                                        <td>
                                            {{$invoice->i_transaction_id}}
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->created_at)); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->updated_at)); ?>
                                        </td>
                                        <td>
                                            <a href="{{url('/admin/viewInvoice')}}/{{$invoice->i_transaction_id}}" target="_blank" id="report">View Invoice</a>&nbsp;&nbsp;
                                            <a href="{{url('/admin/sendEmailForInvoice')}}/{{$invoice->i_transaction_id}}">Email</a>&nbsp;&nbsp;
                                            <a href="javascript:void(0);" onclick="getPrint('{{$invoice->i_transaction_id}}')" target="_blank" class="print">Print</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td><center>{{trans('labels.norecordfound')}}</center></td>
                                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="parent" class="tab-pane fade <?php if(isset($searchParamArray['type']) && $searchParamArray['type'] == 2) {echo 'in active';}?>">
                        <div class="box-body">
                            <table id="listParent" class="table table-striped display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('labels.serialnumber')}}</th>
                                        <th>{{trans('labels.formlblid')}}</th>
                                        <th>{{trans('labels.formlblname')}}</th>
                                        <th>{{trans('labels.formlblbillingname')}}</th>
                                        <th>{{trans('labels.formlblbillingemail')}}</th>
                                        <th>{{trans('labels.paidamount')}}</th>
                                        <th>{{trans('labels.consumedcoins')}}</th>
                                        <th>{{trans('labels.formlbltransid')}}</th>
                                        <th>{{trans('labels.formlblcreatedate')}}</th>
                                        <th>{{trans('labels.formlblupdatedate')}}</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $serialno = 0; ?>
                                    @forelse($invoiceDetailForParent as $invoice)
                                    <?php $serialno++; ?>
                                    <tr>
                                        <td>
                                            <?php echo $serialno; ?>
                                        </td>
                                        <td>
                                            <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                            {{$invoice->i_invoice_id}}
                                        </td>
                                        <td>
                                            {{$invoice->p_first_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_billing_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_email}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_amount}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_coins}}
                                        </td>
                                        <td>
                                            {{$invoice->i_transaction_id}}
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->created_at)); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->updated_at)); ?>
                                        </td>
                                        <td>
                                            <a href="{{url('/admin/viewInvoice')}}/{{$invoice->i_transaction_id}}" target="_blank" id="report">View Invoice</a>&nbsp;&nbsp;
                                            <a href="{{url('/admin/sendEmailForInvoice')}}/{{$invoice->i_transaction_id}}">Email</a>&nbsp;&nbsp;
                                            <a href="javascript:void(0);" onclick="getPrint('{{$invoice->i_transaction_id}}')" target="_blank" class="print">Print</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td><center>{{trans('labels.norecordfound')}}</center></td>
                                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="sponsor" class="tab-pane fade <?php if(isset($searchParamArray['type']) && $searchParamArray['type'] == 4) {echo 'in active';}?>">
                        <div class="box-body">
                            <table id="listSponsor" class="table table-striped display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{trans('labels.serialnumber')}}</th>
                                        <th>{{trans('labels.formlblid')}}</th>
                                        <th>{{trans('labels.formlblname')}}</th>
                                        <th>{{trans('labels.formlblbillingname')}}</th>
                                        <th>{{trans('labels.formlblbillingemail')}}</th>
                                        <th>{{trans('labels.paidamount')}}</th>
                                        <th>{{trans('labels.consumedcoins')}}</th>
                                        <th>{{trans('labels.formlbltransid')}}</th>
                                        <th>{{trans('labels.formlblcreatedate')}}</th>
                                        <th>{{trans('labels.formlblupdatedate')}}</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $serialno = 0; ?>
                                    @forelse($invoiceDetailForSponsor as $invoice)
                                    <?php $serialno++; ?>
                                    <tr>
                                        <td>
                                            <?php echo $serialno; ?>
                                        </td>
                                        <td>
                                            <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                            {{$invoice->i_invoice_id}}
                                        </td>
                                        <td>
                                            {{$invoice->sp_admin_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_billing_name}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_email}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_amount}}
                                        </td>
                                        <td>
                                            {{$invoice->tn_coins}}
                                        </td>
                                        <td>
                                            {{$invoice->i_transaction_id}}
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->created_at)); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($invoice->updated_at)); ?>
                                        </td>
                                        <td>
                                            <a href="{{url('/admin/viewInvoice')}}/{{$invoice->i_transaction_id}}" target="_blank" id="report">View Invoice</a>&nbsp;&nbsp;
                                            <a href="{{url('/admin/sendEmailForInvoice')}}/{{$invoice->i_transaction_id}}">Email</a>&nbsp;&nbsp;
                                            <a href="javascript:void(0);" onclick="getPrint('{{$invoice->i_transaction_id}}')" target="_blank" class="print">Print</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td>{{trans('labels.norecordfound')}}</td>
                                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('script')
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
    jQuery.noConflict();
        
    function getPrint(id) {
        $.ajax({
            url: "{{ url('/admin/printInvoice') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "id": id
            },
            success: function(response) {
                if(response != '') {
                    var url = "{{url('uploads/invoice')}}/" + response;
                    var w = window.open(url);
                    w.print();
                } else {
                    alert("Invoice does not exist.");
                }
            }
        });
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('table.display').DataTable();
    });
</script>
@stop