@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level4activityadvance')}}
        <a href="{{ url('admin/level4advanceactivity') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table class="table table-striped" id="list_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                               <th>{{trans('labels.serialnumber')}}</th>
                               <th>Activity Type</th>
                               <th>Question</th>
                               <th>Status</th>
                               <th>{{trans('labels.basketblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($leve4advanceactivities as $activity)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    @if($activity->l4aa_type == 1)
                                       Video                                 
                                    @elseif($activity->l4aa_type == 2)                                
                                       Document                                 
                                    @else                                
                                        Photo                                 
                                    @endif
                                </td>
                                <td>
                                    {{$activity->l4aa_text}}
                                </td>
                                <td>
                                    @if ($activity->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/admin/editlevel4advanceactivity') }}/{{$activity->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deletelevel4advanceactivity') }}/{{$activity->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#list_table').DataTable();
    </script>
@stop