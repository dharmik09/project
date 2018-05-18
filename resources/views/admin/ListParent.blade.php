@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
        {{($type == 1)?trans('labels.parents'):trans('labels.counselors')}}  
        </div>
        <div class="col-md-1">
        <a href="{{ url('admin/add-parent') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            @if($type == 1)
            <a href="{{ url('admin/export-parent/1') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
            @else
            <a href="{{ url('admin/export-parent/2') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
            @endif
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
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-striped" id="parents_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.parentblheadname')}}</th>
                                <th>{{trans('labels.parentblheademail')}}</th>
                                <th>{{trans('labels.formlblcoins')}}</th>
                                <th>{{trans('labels.parentblheadstatus')}}</th>
                                <th>View Teens</th>
                                <th>{{trans('labels.parentblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($parents as $parent)
                            <?php $serialno++; ?>
                                <tr>
                                    <td>
                                    <?php echo $serialno; ?>
                                    </td>
                                    <td>
                                        {{$parent->p_first_name}} {{$parent->p_last_name}}
                                    </td>
                                    <td>
                                        {{$parent->p_email}}
                                    </td>
                                    <td>
                                        {{$parent->p_coins}}
                                    </td>
                                    <td>
                                        @if ($parent->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                        @else
                                        <i class="s_inactive fa fa-square"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/admin/view-parentteen') }}/{{$parent->id}}"><i class="fa fa-eye"></i> &nbsp;&nbsp;</a>                                
                                    </td>
                                    <td>
                                        <?php if ($type == 1) {$typeData = 1; } else { $typeData = 2;} ?>
                                        <a href="{{ url('/admin/edit-parent') }}/{{$parent->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/delete-parent') }}/{{$parent->id}}/{{$type}}"><i class="i_delete fa fa-trash"></i>&nbsp;&nbsp;</a>
                                        <a href="" onClick="add_coins_details({{$parent->id}},{{$typeData}});" data-toggle="modal" id="#parentCoinsData" data-target="#parentCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>
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
    <div id="parentCoinsData" class="modal fade" role="dialog">

    </div>
</section>
@stop
@section('script')
<script type="text/javascript">
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    //Add coin details    
    function add_coins_details($id,$type)
    {
       $.ajax({
         type: 'post',
         url: '{{ url("admin/add-coins-data-for-parent") }}',
         data: {
           parentid:$id,
           typeData:$type
         },
         success: function (response)
         {
            $('#parentCoinsData').html(response);
         }
       });
    }

    $('#parents_table').DataTable({
        "lengthMenu": [ 10, 25, 50, 100, 200, 500, 1000 ]
    });
    
</script>
@stop