@extends('developer.Master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1qualities')}}
        <a href="{{ url('developer/addlevel1quality') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
                    <form id="formSearchLevel1Quality" class="form-horizontal" method="post" action="{{ url('/developer/level1qualities') }}">
                        <div class="col-md-3">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select id="searchBy" name="searchBy" class="form-control">
                                <option value="">{{trans('labels.formlblsearchby')}}</option>
                                <option value="l1qa_name" <?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'l1qa_name'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblname')}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchText" name="searchText" placeholder="{{trans('labels.lblsearch')}}" value="<?php if(isset($searchParamArray['searchText']) && $searchParamArray['searchText'] != ''){ echo $searchParamArray['searchText'];}?>" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <select id="orderBy" name="orderBy" class="form-control">
                                <option value="">{{trans('labels.formlblorderby')}}</option>
                                <option value="l1qa_name" <?php if(isset($searchParamArray['orderBy']) && $searchParamArray['orderBy'] == 'l1qa_name'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblname')}}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="sortOrder" name="sortOrder" class="form-control">
                                <option value="">{{trans('labels.lblorder')}}</option>
                                <option value="ASC" <?php if(isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'ASC'){ echo 'selected = "selected"';}?> >Ascending</option>
                                <option value="DESC" <?php if(isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'DESC'){ echo 'selected = "selected"';}?> >Descending</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="searchLevel1Quality" id="searchLevel1Quality" value="{{trans('labels.lblsearch')}}"/>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="clearSearch" id="clearSearch" value="{{trans('labels.lblclear')}}"/>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                      <table class="table table-striped">
                        <tr>
                            <th>{{trans('labels.level1qualityblheadname')}}</th>
                            <th>{{trans('labels.level1qualityblheadstatus')}}</th>
                            <th>{{trans('labels.level1qualityblheadactions')}}</th>
                        </tr>
                        @forelse($level1qualities as $qualities)
                        <tr>
                            <td>
                                {{$qualities->l1qa_name}}
                            </td>

                            <td>
                                 @if ($qualities->deleted == 1)
                                <i class="s_active fa fa-square"></i>
                                @else
                                    <i class="s_inactive fa fa-square"></i>
                                @endif
                            </td>
                            <td>
                                <a target="_blank" href="{{ url('/developer/editlevel1quality') }}/{{$qualities->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deletelevel1quality') }}/{{$qualities->id}}"><i class="i_delete fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
                    </table>
                    @if (isset($level1qualities) && !empty($level1qualities))
                        <div class="pull-right">
                            <?php echo $level1qualities->render(); ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@stop