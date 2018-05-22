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
            <form method="POST" action="{{ url('admin/searchSchoolLevel2Activity') }}">
                {{csrf_field()}}
                <div class="form-group"> 
                    <label for="chart" class="col-sm-1 control-label">School:</label>
                    <div class="col-sm-6">
                        <select id="school" name="school" class="form-control chosen-select">
                            <option value="">Select</option>     
                            @forelse ($schools as $school)
                                <option value="{{ $school->school_id }}" <?php if (isset($schoolId) && $schoolId != "" && $school->school_id == $schoolId) { ?> selected <?php } ?> >{{$school->sc_name}}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-">
                        <input type="submit" class="btn btn-warning btn-primary" value="{{trans('labels.lblsearch')}}">
                    </div>
                </div>
            </form>
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
<script src="{{ asset('backend/js/chosen.jquery.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#listLevel2Activity').DataTable();
    });
    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"},
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
</script>
@stop