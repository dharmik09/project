@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) --> 
<section class="content-header">
    <h1>
        <div class="col-md-9">
            {{trans('labels.sponsors')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/add-sponsor') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/export-sponsor') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>            
        </div>
    </h1>
</section>
           
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                     <table class="table table-striped" id="enterprise_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.sponsorblheadcompanyname')}}</th>
                                <th>{{trans('labels.sponsorblheademail')}}</th>
                                <th>{{trans('labels.sponsorblheadadminname')}}</th>
                                <th>{{trans('labels.formlblcoins')}}</th>
                                <th>{{trans('labels.sponsorblheadapproved')}}</th>
                                <th>{{trans('labels.sponsorblheadstatus')}}</th>
                                <th>{{trans('labels.sponsorblheadlogo')}}</th>
                                <th>{{trans('labels.sponsorblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($sponsors as $sponsor)
                                <?php $serialno++; ?>
                                <tr>
                                    <td>
                                        <?php echo $serialno; ?>
                                    </td>
                                    <td>
                                        {{$sponsor->sp_company_name}}
                                    </td>
                                    <td>
                                        {{$sponsor->sp_email}}
                                    </td>
                                    <td>
                                        {{$sponsor->sp_admin_name}}
                                    </td>
                                    <td>
                                        {{$sponsor->sp_credit}}
                                    </td>
                                    <td>
                                        @if ($sponsor->sp_isapproved == 1)
                                            Yes
                                        @else
                                        <a onclick="return confirm('<?php echo trans('labels.confirmapprove');  ?>')" href="{{ url('/admin/edit-approved') }}/{{$sponsor->id}}" class="btn btn-primary btn-xs">
                                          <?php echo trans('labels.approve'); ?>
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($sponsor->deleted == 1)
                                            <i class="s_active fa fa-square"></i>
                                        @else
                                            <i class="s_inactive fa fa-square"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <?php
                                            $image = ($sponsor->sp_logo != "" && Storage::disk('s3')->exists($uploadSponsorThumbPath.$sponsor->sp_logo) ) ? Config::get('constant.DEFAULT_AWS').$uploadSponsorThumbPath.$sponsor->sp_logo : asset('/backend/images/proteen_logo.png');
                                        ?>
                                        <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    </td>
                                    <td>
                                        <a href="{{ url('/admin/edit-sponsor') }}/{{$sponsor->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/delete-sponsor') }}/{{$sponsor->id}}"><i class="i_delete fa fa-trash"></i> &nbsp;&nbsp;</a>
                                        <a href="{{ url('/admin/sponsor-activity') }}/{{$sponsor->id}}" ><i class="fa fa-eye"></i>&nbsp;&nbsp;</a>
                                        <a href="" onClick="add_coins_details({{$sponsor->id}});" data-toggle="modal" id="#sponsorCoinsData" data-target="#sponsorCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"><center>{{trans('labels.norecordfound')}}</center></td>
                                </tr>
                            @endforelse
                        </tbody>
                     </table>
                    @if (isset($sponsors) && !empty($sponsors))
                        <div class="pull-right">
                            <?php echo $sponsors->render(); ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="sponsorCoinsData" class="modal fade" role="dialog">

    </div>
</section>

@stop
@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        $("#enterprise_table").DataTable({
            "lengthMenu": [ 10, 25, 50, 100, 200, 500, 1000 ]
        });
    });
    function add_coins_details($id)
    {
       $.ajax({
         type: 'post',
         url: '{{ url("admin/add-coins-data-for-sponsor") }}',
         data: {
           sponsorid:$id
         },
         headers: { 
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
         },
         success: function (response)
         {
            $('#sponsorCoinsData').html(response);
         }
       });
    }
</script>

@stop