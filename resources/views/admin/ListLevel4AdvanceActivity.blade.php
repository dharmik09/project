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
                 <div class="box-header">
            <form id="formSearchActivity" class="form-horizontal" method="post" action="{{ url('/admin/listlevel4advanceactivity') }}">
                        <div class="col-md-3">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select id="searchBy" name="searchBy" class="form-control">
                                <option value="l4aa_text" <?php
                                if (isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'l4aa_text') {
                                    echo 'selected = "selected"';
                                }
                                ?> >{{trans('labels.formlbltext')}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchText" name="searchText" placeholder="{{trans('labels.lblsearch')}}" value="<?php
                            if (isset($searchParamArray['searchText']) && $searchParamArray['searchText'] != '') {
                                echo $searchParamArray['searchText'];
                            }
                            ?>" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <select id="orderBy" name="orderBy" class="form-control">
                                <option value="">{{trans('labels.formlblorderby')}}</option>
                                <option value="l4aa_text" <?php
                                if (isset($searchParamArray['orderBy']) && $searchParamArray['orderBy'] == 'l4aa_text') {
                                    echo 'selected = "selected"';
                                }
                                ?> >{{trans('labels.formlbltext')}}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="sortOrder" name="sortOrder" class="form-control">
                                <option value="">{{trans('labels.lblorder')}}</option>
                                <option value="ASC" <?php
                                if (isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'ASC') {
                                    echo 'selected = "selected"';
                                }
                                ?> >Ascending</option>
                                <option value="DESC" <?php
                                if (isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'DESC') {
                                    echo 'selected = "selected"';
                                }
                                ?> >Descending</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="searchLevel4Activity" id="searchLevel4Activity" value="{{trans('labels.lblsearch')}}"/>
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
                           <th>Activity Type</th>
                           <th>Question</th>
                           <th>Status</th>
                           <th>{{trans('labels.basketblheadaction')}}</th>
                        </tr>
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
                    </table>
                    @if (isset($leve4advanceactivities) && !empty($leve4advanceactivities))
                        <div class="pull-right">
                            <?php echo $leve4advanceactivities->render(); ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@stop