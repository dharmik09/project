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
            <h1><span class="title_border">Request By Teenagers</span></h1>
        </div>
        <div class="my_teens_content clearfix">
            <div class="my_teens_inner">
                <div class="table_container">
                    <table class="sponsor_table">
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Actions</th>
                        </tr>
                        @if(!empty($userDetail))
                        @foreach($userDetail as $key=>$data)
                        <tr>
                            <td>
                                {{$data->t_name}}
                            </td>
                            <td>
                                @if ($data->tpr_status == 1)
                                    Pending
                                @else
                                    Completed
                                @endif
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($data->created_at)); ?>
                            </td>
                            <td>
                                <a href="javascript:void(0);" title="" onclick="purchasedCoins({{$data->tpr_teen_id}});">View</a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="4">No data found</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
@section('script')

<script type="text/javascript">

    function purchasedCoins(id)
    {
        $.ajax({
            url: "{{ url('parent/accept-teen-request') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "teen_id": id
            },
            success: function(response) {
               var path = '<?php echo url('/parent/my-coins/'); ?>';
               location.href = path;
            }
        });
    }
</script>
@stop
