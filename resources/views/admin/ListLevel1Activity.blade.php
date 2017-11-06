@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1activities')}}
        <a href="{{ url('admin/addLevel1Activity') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listLevel1Activity" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.activityblheadtext')}}</th>
                                <th>{{trans('labels.activityblheadpoints')}}</th>
                                <th>{{trans('labels.activityblheadoptions')}}</th>
                                <th>{{trans('labels.activityblheadstatus')}}</th>
                                <th>{{trans('labels.activityblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($level1activities as $level1activity)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$level1activity->l1ac_text}}
                                </td>
                                <td>
                                    {{$level1activity->l1ac_points}}
                                </td>
                                <td>
                                    <?php
                                       $option = $level1activity->l1op_option;
                                       $newOption = explode(",", $option);
                                       foreach($newOption as $option)
                                       {
                                            echo $option;
                                            echo '<br/>';
                                       }
                                    ?>
                                </td>
                                <td>
                                     @if ($level1activity->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editLevel1Activity') }}/{{$level1activity->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteLevel1Activity') }}/{{$level1activity->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listLevel1Activity').DataTable();
    });
</script>
@stop