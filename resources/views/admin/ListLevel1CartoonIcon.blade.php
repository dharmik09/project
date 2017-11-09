@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.level1cartoonicons')}}
        </div>
	<div class="col-md-2">
            <a href="{{ url('admin/viewUserImage') }}" class="btn btn-block btn-primary">{{trans('labels.viewuserimage')}}</a>
        </div>
	<div class="col-md-1">
            <a href="{{ url('admin/addCartoon') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/uploadCartoons') }}" class="btn btn-block btn-primary">{{trans('labels.bulkupload')}}</a>
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
                    <table id="listCartoonIcon" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.cartooniconheadname')}}</th>
                                <th>{{trans('labels.humaniconheadcategory')}}</th>
    <!--                            <th>{{trans('labels.humaniconheadprofession')}}</th>-->
                                <th>{{trans('labels.cartooniconheadimage')}}</th>
                                <th>{{trans('labels.cartooniconheadaction')}}</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($level1cartoonicon as $level1icon)
                            <tr>
                                <td>
                                    {{$level1icon->ci_name}}
                                </td>
                                <td>
                                    {{$level1icon->cic_name}}
                                </td>
                                
                                    <?php 
    //                                    $professionName = explode(',', $level1icon->pfname);
    //                                    foreach($professionName as $proName)
    //                                    {
    //                                        echo $proName."<br />";
    //                                    }
                                    ?>
                                
                                <td>
                                    <?php 
                                        $image = ($level1icon->ci_image != "" && Storage::disk('s3')->exists($cartoonThumbPath.$level1icon->ci_image)) ? Config::get('constant.DEFAULT_AWS').$cartoonThumbPath.$level1icon->ci_image : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editCartoon') }}/{{$level1icon->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteCartoon') }}/{{$level1icon->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listCartoonIcon').DataTable();
    });
</script>
@stop