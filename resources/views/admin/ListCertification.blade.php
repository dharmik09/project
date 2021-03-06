@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.professioncertification')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addProfessionCertification') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listCertification" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.professioncertificationname')}}</th>
                                <th>{{trans('labels.professioncertificationimage')}}</th>
                                <th>{{trans('labels.professioncertificationaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($certifications as $certification)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{ucwords ($certification->pc_name)}}
                                </td>
                                <td>
                                    <?php 
                                        $image = ($certification->pc_image != "" && isset($certification->pc_image)) ? Storage::url($certificationThumbImageUploadPath.$certification->pc_image) : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editProfessionCertification') }}/{{$certification->id}}/{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteProfessionCertification') }}/{{$certification->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>{{trans('labels.norecordfound')}}</td>
                                <td></td><td></td><td></td>
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
        $('#listCertification').DataTable();
    });
</script>
@stop