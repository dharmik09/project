@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.configuration')}}
        </div>

        <div class="col-md-5">
            <a href="{{ url('admin/addConfiguration') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>


    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table id="listConfigurations" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.cfg_key')}}</th>
                                <th>{{trans('labels.cfg_value')}}</th>
                                <th>{{trans('labels.sponsorblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configurations as $configure)
                            <tr>
                                <td>
                                    {{$configure->cfg_key}}
                                </td>
                                <td>
                                    {{$configure->cfg_value}}
                                </td>
                                <td>
                                <a href="{{ url('admin/editConfiguration') }}/{{$configure->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                            </tr>
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
        $('#listConfigurations').DataTable();
    });
</script>
@stop