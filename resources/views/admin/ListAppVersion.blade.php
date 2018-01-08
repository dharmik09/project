@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-12">
            {{trans('labels.appversion')}}
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table id="listAppVersion" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.appversiondevicetype')}}</th>
                                <th>{{trans('labels.appversionversion')}}</th>
                                <th>{{trans('labels.appversionforceupdate')}}</th>
                                <th>{{trans('labels.appversionmessage')}}</th>
                                <th>{{trans('labels.tblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $value)
                            <tr>
                                <td>
                                    <?php echo ($value->device_type == 1) ? trans('labels.formblios') : trans('labels.formblandroid') ?>
                                </td>
                                <td>
                                    {{$value->app_version}}
                                </td>
                                <td>
                                    <?php echo ($value->force_update == 1) ? trans('labels.lbltrue') : trans('labels.lblfalse') ?>
                                </td>
                                <td>
                                    {{$value->message}}
                                </td>
                                <td>
                                    <a href="{{ url('/admin/editAppVersion') }}/{{$value->id}}"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#listAppVersion').DataTable();
    });
</script>
@stop