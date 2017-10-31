@extends('developer.Master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.apptitudetypescale')}}
        <?php if(isset($apptitudetypescales) && empty($apptitudetypescales)){ ?>
            <a href="{{ url('developer/addapptitudetypescale') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        <?php } else {?>
            <a href="{{ url('/developer/editapptitudetypescale') }}" class="btn btn-block btn-primary add-btn-primary pull-right"> {{trans('labels.edit')}} </a>
        <?php } ?>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                      <table class="table table-striped">
                        <tr>
                            <th>{{trans('labels.apptitudeblheadname')}}</th>
                            <th>{{trans('labels.apptitudeblheadhighscore')}}</th>
                            <th>{{trans('labels.apptitudeblheadmoderatescore')}}</th>
                            <th>{{trans('labels.apptitudeblheadlowscore')}}</th>
                        </tr>
                        @forelse($apptitudetypescales as $scales)
                        <tr>
                            <td>
                                {{$scales->apt_name}}
                            </td>
                            <td>
                            <?php
                                if($scales->ats_high_min_score == $scales->ats_high_max_score)
                                {
                                ?>
                                    {{$scales->ats_high_min_score}}
                                <?php
                                }
                                else
                                {
                                ?>
                                    {{$scales->ats_high_min_score}} - {{$scales->ats_high_max_score}}
                                <?php
                                }
                                ?>
                            </td>

                            <td>
                            <?php
                                if($scales->ats_moderate_min_score == $scales->ats_moderate_max_score)
                                {
                                ?>
                                    {{$scales->ats_moderate_min_score}}
                                <?php
                                }
                                else
                                {
                                ?>
                                    {{$scales->ats_moderate_min_score}} - {{$scales->ats_moderate_max_score}}
                                <?php
                                }
                                ?>
                            </td>

                            <td>
                            <?php
                                if($scales->ats_low_min_score == $scales->ats_low_max_score)
                                {
                                ?>
                                    {{$scales->ats_low_min_score}}
                                <?php
                                }
                                else
                                {
                                ?>
                                    {{$scales->ats_low_min_score}} - {{$scales->ats_low_max_score}}
                                <?php
                                }
                                ?>
                            </td>

                            <!--<td>
                                <a href="{{ url('/developer/editapptitudetypescale') }}/{{$scales->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                               <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deleteapptitudetypescale') }}/{{$scales->id}}"><i class="i_delete fa fa-trash"></i></a>
                            </td>-->
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@stop