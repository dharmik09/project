@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level2activities')}}
        <a href="{{ url('admin/addLevel2Activity') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listLevel2Activity" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.activityblheadtext')}}</th>
                                <th>{{trans('labels.promiseparameters')}}</th>
                                <th>{{trans('labels.activityblheadpoints')}}</th>
                                <th>{{trans('labels.activityblheadoptions')}}</th>
                                <th>{{trans('labels.activityblheadstatus')}}</th>
                                <th>{{trans('labels.activityblheadsection')}}</th>
                                <th>{{trans('labels.activityblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 1; ?>
                            @forelse($level2activities as $level2activity)
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$level2activity->l2ac_text}}
                                </td>
                                <td>
                                    <?php
                                        if(isset($level2activity->l2ac_apptitude_type) && !empty($level2activity->l2ac_apptitude_type) && $level2activity->l2ac_apptitude_type != '' )
                                        {
                                            ?> <div>{{$level2activity->apt_name}}</div> <?php
                                        }
                                        
                                        if(isset($level2activity->l2ac_personality_type) && !empty($level2activity->l2ac_personality_type) && $level2activity->l2ac_personality_type != '' )
                                        {
                                            ?> <div>{{$level2activity->pt_name}}</div> <?php
                                        }
                                        
                                        if(isset($level2activity->l2ac_mi_type) && !empty($level2activity->l2ac_mi_type) && $level2activity->l2ac_mi_type != '' )
                                        {
                                            ?> <div>{{$level2activity->mit_name}}</div> <?php
                                        }
                                        
                                        if(isset($level2activity->l2ac_interest) && !empty($level2activity->l2ac_interest) && $level2activity->l2ac_interest != '' )
                                        {
                                           ?> <div>{{$level2activity->it_name}}</div> <?php
                                        }
                                        ?>
                                </td>
                                <td>
                                    {{$level2activity->l2ac_points}}
                                </td>
                                <td>
                                    <?php $explode=explode(',',$level2activity->l2op_option);
                                           foreach($explode as $option_name)
                                           {
                                               echo $option_name."<br/>";
                                           }
                                    ?>
                                </td>
                                <td>
                                     @if ($level2activity->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                     {{trans('labels.activityblheadsection')}}-{{$level2activity->section_type}}
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editLevel2Activity') }}/{{$level2activity->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteLevel2Activity') }}/{{$level2activity->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php $serialno++; ?>
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
        $('#listLevel2Activity').DataTable();
    });
</script>
@stop