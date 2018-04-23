@extends('layouts.school-master')

@section('content')
@if($message = Session::get('success'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-success alert-succ-msg alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
            {{ $message }}
        </div>
    </div>
</div>
@endif
@if($message = Session::get('error'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            {{ $message }}
        </div>
    </div>
</div>
@endif
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ask Your Students
    </h1>
</section>

<div class="centerlize">
    <div class="container">
        <div class="container_padd list-question">
            <div class="button_container coins_button_container">
                <div class="coin_summary cst_dsh clearfix">
                    <div class="dashboard_page pull-right col-md-3 col-sm-4 col-xs-12">
                        <a href="{{ url('school/add-questions') }}" class="btn primary_btn space_btm">Add Question</a>
                    </div>
                </div>
            </div>
            <div class="table_title">
                <div class="row">
                    <div class="dashboard_page_title">
                    </div>
                    <div class="dashboard_page_title clearfix">
                        <div class="search_container desktop_search gift_coin_search pull-right">
                            <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search here..." onkeyup="userSearch(this.value, {{Auth::guard('school')->user()->id}},1)">
                            <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table_container show_data">
            <table class="sponsor_table table_ckbx nobopd" id="table1">
                <tr class="cst_status">
                    <th>Sr. No</th>
                    <th>Question</th>
                    <th>Question Type</th>
                    <th>{{trans('labels.activityblheadpoints')}}</th>
                    <th>Answer Choices</th>
                    <th>{{trans('labels.activityblheadsection')}}</th>
                    <th>{{trans('labels.activityblheadaction')}}</th>
                </tr>
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
                        <?php 
                        $explodeOption = explode(',', $level2activity->l2op_option);
                        $explodeFraction = explode(',', $level2activity->l2op_fraction);
                        foreach($explodeOption as $key => $option_name)
                        {
                            if (count($explodeFraction) > 0 && $explodeFraction[$key] == 1) { ?> 
                                <strong><span class="font-blue"> 
                                <?php
                                    echo $option_name."<br/>"; ?>
                                </span></strong>
                            <?php } else { 
                                echo $option_name."<br/>";
                            }
                        }
                        ?>
                    </td>
                    <td>
                         {{trans('labels.activityblheadsection')}}-{{$level2activity->section_type}}
                    </td>
                    <td>
                        <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                        <a href="{{ url('/school/edit-level2-questions') }}/{{$level2activity->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/school/delete-level2-questions') }}/{{$level2activity->id}}"><i class="i_delete fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php $serialno++; ?>
                @empty
                <tr>
                    <td colspan="7"><center>{{trans('labels.norecordfound')}}</center></td>
                </tr>
                @endforelse
                <tr>
                    <td colspan="7" class="sub-button">
                        @if (isset($level2activities) && count($level2activities) > 0)
                        <div class="pull-right">
                            <?php echo $level2activities->render(); ?>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="mySearch_area">
            
        </div>
    </div>
</div>

@stop
