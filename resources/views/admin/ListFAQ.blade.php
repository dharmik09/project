@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.faq')}}
         <a href="{{ url('admin/addFaq') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listFaq" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.questiontext')}}</th>
                                <th>{{trans('labels.questiongroup')}}</th>
                                <th>{{trans('labels.questionans')}}</th>
                                <th>{{trans('labels.formlblphoto')}}</th>
                                <th>{{trans('labels.cmsblheadstatus')}}</th>
                                <th>{{trans('labels.cmsblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqDetail as $value)
                            <tr>
                                <td>
                                    {{$value->f_question_text}}
                                </td>
                                <td>
                                    {{$value->f_que_group}}
                                </td>
                                <td>
                                    {!! str_limit($value->f_que_answer, $limit = 50, $end = '...') !!}
                                </td>
                                <td>
                                    <?php 
                                        $image = ($value->f_photo != "" && Storage::disk('s3')->exists($uploadFAQThumbPath.$value->f_photo)) ? Config::get('constant.DEFAULT_AWS').$uploadFAQThumbPath.$value->f_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                </td>
                                <td>
                                    @if ($value->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/admin/editFaq') }}/{{$value->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteFaq') }}/{{$value->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4"><center>{{trans('labels.norecordfound')}}</center></td>
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
        $('#listFaq').DataTable();
    });
</script>
@stop