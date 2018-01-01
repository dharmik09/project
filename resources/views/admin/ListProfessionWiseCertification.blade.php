@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            {{trans('labels.professionwisecertification')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addProfessionWiseCertification') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/addProfessionWiseCertificationBulk') }}" class="btn btn-block btn-primary">{{trans('labels.bulkupload')}}</a>
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table id="listProfessionWiseCertification" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.lblprofession')}}</th>
                                <th>{{trans('labels.lblcertificates')}}</th>
                                <th>{{trans('labels.professioncertificationaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $value)
                            <tr>
                                <td>
                                    {{ucwords ($value->profession_name)}}
                                </td>
                                <td>
                                    {{ucwords ($value->certificate_name)}}
                                </td>
                                <td>
                                    <a href="{{ url('/admin/editProfessionWiseCertification') }}/{{$value->profession_id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteProfessionWiseCertification') }}/{{$value->profession_id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listProfessionWiseCertification').DataTable();
    });
</script>
@stop