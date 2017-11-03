@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.generic')}}
        <a href="{{ url('admin/addGeneric') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listGeneric" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.genericname')}}</th>
                                <th>{{trans('labels.genericimage')}}</th>
                                <th>{{trans('labels.genericstartdate')}}</th>
                                <th>{{trans('labels.genericenddate')}}</th>
                                <th>{{trans('labels.genericstatus')}}</th>
                                <th>{{trans('labels.genericactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($generics as $generic)
                             <tr>
                                <td>
                                    {{$generic->ga_name}}
                                </td>
                                <td>
                                     <?php if(File::exists(public_path($genericThumbImagePath.$generic->ga_image)) && $generic->ga_image!='') { ?>
                                    <img src="{{asset($genericThumbImagePath.$generic->ga_image)}}" width="45px" height="45px" />
                                    <?php } else { ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" width="45px" height="45px">
                                    <?php } ?>
                                </td>
                                <td>
                                    {{date('d/m/Y', strtotime($generic->ga_start_date))}}
                                </td>
                                <td>
                                    {{date('d/m/Y', strtotime($generic->ga_end_date))}}
                                </td>
                                <td>
                                    @if ($generic->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/admin/editGeneric') }}/{{$generic->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteGeneric') }}/{{$generic->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7"><center>{{trans('labels.norecordfound')}}</center></td>
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
        $('#listGeneric').DataTable();
    });
</script>
@stop