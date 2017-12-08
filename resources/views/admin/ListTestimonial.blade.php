@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.testimonials')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addTestimonial') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listTestinomial" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.testimonialname')}}</th>
                                <th>{{trans('labels.testimonialtitle')}}</th>
                                <th>{{trans('labels.testimonialimage')}}</th>
                                <th>{{trans('labels.testimonialdescription')}}</th>
                                <th>{{trans('labels.testimonialaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($testimonials as $testimonial)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{ucwords ($testimonial->t_name)}}
                                </td>
                                <td>
                                    {{ $testimonial->t_title }}
                                </td>
                                <td>
                                    <?php 
                                        $image = ($testimonial->t_image != "" && isset($testimonial->t_image)) ? Storage::url($testimonialOriginalImageUploadPath.$testimonial->t_image) : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                </td>
                                <td>
                                    {{ $testimonial->t_description }}
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editTestimonial') }}/{{$testimonial->id}}/{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteTestimonial') }}/{{$testimonial->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td><center>{{trans('labels.norecordfound')}}</center></td>
                                <td></td><td></td><td></td><td></td><td></td>
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
        $('#listTestinomial').DataTable();
    });
</script>
@stop