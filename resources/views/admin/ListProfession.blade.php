@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="col-md-12">
        <h1>
            <div class="col-md-7">
                {{trans('labels.professions')}}
            </div>
            <div class="col-md-1">
                <a href="{{ url('admin/addProfession') }}" class="btn btn-block btn-primary pull-right">{{trans('labels.add')}}</a>
            </div>
            <div class="col-md-2">
                <a href="{{ url('admin/exportProfessoin') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
            </div>
            <div class="col-md-2">
                <a href="{{ url('admin/addProfessionBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionbulkupload')}}</a>
            </div>
        </h1>
    </div>
    <div class="col-md-12">
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseCertificationBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisecertificatebulkupload')}}</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseSubjectBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisesubjectbulkupload')}}</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseTagBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisetagbulkupload')}}</a>
            </div>
    </div>
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
                    <table id="listProfession" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.professionblheadname')}}</th>
                                <th>{{trans('labels.professionblheadbasket')}}</th>
                                <th>{{trans('labels.professionblheadlogo')}}</th>
                                <th>{{trans('labels.professionblheadstatus')}}</th>
                                <th>{{trans('labels.professionblheadaction')}}</th>
                                <th>Competitors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($professions as $profession)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$profession->pf_name}}
                                </td>
                                <td>
                                    <?php $basketsNames = Helpers::getMultipleBasketNamesForProfession($profession->id); ?>
                                    {{(isset($basketsNames) && $basketsNames != '')?$profession->b_name.', '.$basketsNames:$profession->b_name}}
                                </td>
                                <td>
                                    <?php
                                        $image = ($profession->pf_logo != "" && isset($profession->pf_logo)) ? Storage::url($uploadProfessionThumbPath.$profession->pf_logo) : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                   </td>
                                <td>
                                    @if ($profession->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editProfession') }}/{{$profession->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteProfession') }}/{{$profession->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                                <td>
                                    <a href="" onClick="fetch_competitors_details({{$profession->id}});" data-toggle="modal" id="#userCompetotorsData" data-target="#userCompetotorsData"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;&nbsp;</a>
                                    <a href="{{ url('admin/exportCompetitors')}}/{{$profession->id}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
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
    <div id="userCompetotorsData" class="modal fade" role="dialog">

    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
  </script>
<script type="text/javascript">

    function fetch_competitors_details($id)
    {
       $.ajax({
         type: 'post',
         url: '{{ url("admin/getUserCompetitorsData") }}',
         data: {
           Professionid:$id
         },
         success: function (response)
         {
            $('#userCompetotorsData').html(response);
         }
       });
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#listProfession').DataTable();
    });
</script>
@stop
