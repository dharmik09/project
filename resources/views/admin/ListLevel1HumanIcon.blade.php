@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.level1humanicons')}}
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/viewHumanUserImage') }}" class="btn btn-block btn-primary">{{trans('labels.viewuserimage')}}</a>
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addHumanIcon') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/uploadHumanIcons') }}" class="btn btn-block btn-primary">{{trans('labels.bulkupload')}}</a>
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
                <div class="box-body">
                    <table id="listHumanIcon" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.humaniconheadname')}}</th>
                                <th>{{trans('labels.humaniconheadcategory')}}</th>
                                <th>{{trans('labels.humaniconheadimage')}}</th>
                                <th>{{trans('labels.humaniconheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($level1humanicon as $level1icon)
                            <tr>
                                <td>
                                    {{$level1icon->hi_name}}
                                </td>
                                <td>
                                    {{$level1icon->hic_name}}
                                </td>
                                <td>
                                    <?php 
                                        $image = ($level1icon->hi_image != "" && Storage::disk('s3')->exists($humanThumbPath.$level1icon->hi_image)) ? Config::get('constant.DEFAULT_AWS').$humanThumbPath.$level1icon->hi_image : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editHumanIcon') }}/{{$level1icon->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteHumanIcon') }}/{{$level1icon->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td><center>{{trans('labels.norecordfound')}}</center></td>
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
        $('#listHumanIcon').DataTable();
    });
</script>
@stop