@extends('layouts.admin-master')
@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            {{trans('labels.level4activities')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addLevel4Activity') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/addLevel4QuestionBulk') }}" class="btn btn-block btn-primary">{{trans('labels.bulkupload')}}</a>
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
                    <form id="formSearchActivity" class="form-horizontal" method="post" action="{{ url('/admin/level4Activity') }}">
                        <div class="col-md-3">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select id="searchBy" name="searchBy" class="form-control">
                                <option value="question_text" <?php
                                if (isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'question_text') {
                                    echo 'selected = "selected"';
                                }
                                ?> >{{trans('labels.formlbltext')}}</option>
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
                            <th>Professions</th>
                            <th>{{trans('labels.activityblheadtext')}}</th>
                            <th>{{trans('labels.activityblheadpoints')}}</th>
                            <th>{{trans('labels.activityblheadoptions')}}</th>
                            <th>{{trans('labels.activityblheadstatus')}}</th>
                            <th>{{trans('labels.activityblheadaction')}}</th>
                        </tr>
                        <?php  $serialno = 1; ?>

                        @forelse($leve4activities  as $leve4activitie)
                        <tr>
                            <td>
                                <?php  echo $serialno; ?>
                            </td>
                            <td>
                                {{$leve4activitie->pf_name}}
                            </td>
                            <td>
                                {{$leve4activitie->question_text}}
                            </td>
                            <td>
                                {{$leve4activitie->points}}
                            </td>

                            <td>
                                <?php $explode = explode('#',$leve4activitie->options_text);
                                       foreach($explode as $option_name)
                                       {
                                           echo $option_name."<br/>";
                                       }
                                ?>
                            </td>
                            <td>
                                @if ($leve4activitie->deleted == 1)
                                <i class="s_active fa fa-square"></i>
                                @else
                                <i class="s_inactive fa fa-square"></i>
                                @endif
                            </td>
                            <td>
                                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                <a href="{{ url('/admin/editLeve4Activity') }}/{{$leve4activitie->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteLevel4Activity') }}/{{$leve4activitie->id}}"><i class="i_delete fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php  $serialno++; ?>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse

                    </table>
                     
                    @if (isset($leve4activities) && !empty($leve4activities))
                        <div class="pull-right">
                           @if(isset($searchParamArray['searchText']))
                                <?php
                                    $text = $searchParamArray['searchText'];
                                ?>
                           @else
                                <?php $text = ''; ?>
                           @endif
                           @if(isset($searchParamArray['searchBy']))
                                <?php
                                    $searchBy = $searchParamArray['searchBy'];
                                ?>
                           @else
                                <?php $searchBy = ''; ?>
                           @endif
                            <?php echo $leve4activities->appends(['searchText' => $text, 'searchBy' => $searchBy ])->render(); ?>
                        </div>
                    @endif
                    </div>
               
            </div>
        </div>
    </div>
</section>
@stop