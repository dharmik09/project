@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.systemlevels')}}
        <a href="{{ url('developer/addSystemLevel') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
            <div class="box box-primary">
                <div class="box-body">
                    <table id="listSystemLevel" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <th>{{trans('labels.systemblheadname')}}</th>
                            <th>{{trans('labels.systemblheadbooster')}}</th>
                            <th>{{trans('labels.systemblheadstatus')}}</th>
                            <th>{{trans('labels.systemblheadactions')}}</th>
                        </thead>
                        <tbody>
                        @forelse($systemlevels as $systemlevel)
                        <tr>
                            <td>
                                {{$systemlevel->sl_name}}
                            </td>
                            <td>
                                {{$systemlevel->sl_boosters}}
                            </td>
                            <td>
                                 @if ($systemlevel->deleted == 1)
                                <i class="s_active fa fa-square"></i>
                                @else
                                    <i class="s_inactive fa fa-square"></i>
                                @endif
                            </td>
                            <td>
                                <a target="_blank" href="{{ url('/developer/editSystemLevel') }}/{{$systemlevel->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deleteSystemLevel') }}/{{$systemlevel->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listSystemLevel').DataTable();
    });
</script>
@stop