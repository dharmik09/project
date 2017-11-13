@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            Parent Advance Level Tasks
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
                <div class="box-header">
                    <form id="formSearchActivity" class="form-horizontal" method="post" action="{{ url('/admin/level4AdvanceActivityParentTask') }}">
                        <div class="col-md-3">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select id="searchBy" name="searchBy" class="form-control">
                                <option value="p_first_name" <?php
                                if (isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 't_name') {
                                    echo 'selected = "selected"';
                                }
                                ?> >User</option>
                                <option value="pf_name" <?php
                                if (isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'pf_name') {
                                    echo 'selected = "selected"';
                                }
                                ?> >{{trans('labels.profession')}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchText" name="searchText" placeholder="{{trans('labels.lblsearch')}}" value="<?php
                            if (isset($searchParamArray['searchText']) && $searchParamArray['searchText'] != '') {
                                echo $searchParamArray['searchText'];
                            }
                            ?>" class="form-control" />
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="searchLevel4AdvanceActivity" id="searchLevel4AdvanceActivity" value="{{trans('labels.lblsearch')}}"/>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="clearSearch" id="clearSearch" value="{{trans('labels.lblclear')}}"/>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <th>{{trans('labels.serialnumber')}}</th>
                            <th>UserName</th>
                            <th>Professions</th>
                            <th>View More</th>
                        </tr>
                        <?php  $serialno = 1; ?>

                        @forelse($userTasks  as $task)
                        <tr>
                            <td>
                                <?php  echo $serialno; ?>
                            </td>
                            <td>
                                {{$task->p_first_name}}
                            </td>
                            <td>
                                {{$task->pf_name}}
                            </td>

                            <td>
                                <?php $type = (isset($_GET['type']) && $_GET['type'] > 0 )? "?".$_GET['type']."": 3;?>
                                <a href="{{ url('/admin/viewParentAllAdvanceActivities') }}/{{$task->l4aapa_parent_id}}/{{$task->l4aapa_profession_id}}/{{$type}}"><i class="fa fa-eye"></i> &nbsp;&nbsp;</a>
                            </td>
                        </tr>
                        <?php  $serialno++; ?>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
                    </table>
                    @if (isset($userTasks) && !empty($userTasks))
                        <div class="pull-right">
                            <?php echo $userTasks->render(); ?>
                        </div>
                    @endif
                    </div>
            </div>
        </div>
    </div>
</section>
@stop