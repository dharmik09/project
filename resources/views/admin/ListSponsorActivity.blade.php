@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.viewsponsoractivityform')}}
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
            <a href=" {{ url('admin/sponsors') }} ">Back</a>
            <div class="box box-primary">
               <div class="box-body">
                    <table class="table table-striped" id="sponsor_list" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.viewsponsoractivitytype')}}</th>
                                <th>{{trans('labels.viewsponsoractivitylevel')}}</th>
                                <th>{{trans('labels.viewsponsoractivityimage')}}</th>
                                <th>{{trans('labels.viewsponsoractivitylocation')}}</th>
                                <th>{{trans('labels.viewactivitystartdate')}}</th>
                                <th>{{trans('labels.viewactivityenddate')}}</th>
                                <th>{{trans('labels.viewactivitystatus')}}</th>
                                <th>{{trans('labels.viewactions')}}</th>
                            </tr>
                        </thead>
                        <?php $serialno = 0; ?>
                        <tbody>
                            @forelse($sponsorsActivities as $sponsorsActivity)
                            <?php $serialno++; ?>
                                <tr>
                                    <td>
                                        <?php echo $serialno; ?>
                                    </td>
                                    <td>
                                       
                                       @if ($sponsorsActivity->sa_type == 1)
                                            Ads
                                       @elseif ($sponsorsActivity->sa_type == 2)
                                            Event
                                       @elseif ($sponsorsActivity->sa_type == 3)
                                            Contest
                                       @endif
                                       
                                    <td>
                                        <a target="_blank" href="{{ url('admin/view-sponsor-activity') }}/{{$sponsorsActivity->id}}">{{$sponsorsActivity->sa_name}}</a>
                                    </td>
                                    <td>
                                        <?php
                                            $image_tag = ($sponsorsActivity->sa_image != "" && Storage::disk('s3')->exists($sponsorActivityThumbImageUploadPath.$sponsorsActivity->sa_image) ) ? Config::get('constant.DEFAULT_AWS').$sponsorActivityThumbImageUploadPath.$sponsorsActivity->sa_image : asset('/backend/images/proteen_logo.png');
                                        ?>
                                        <img src="{{$image_tag}}" class="user-image" height="70px" width="70px">   
                                    </td>
                                    <td>
                                        {{$sponsorsActivity->sa_location}}
                                    </td>
                                    <td>
                                        {{date('d/m/Y', strtotime($sponsorsActivity->sa_start_date))}}
                                    </td>
                                    <td>
                                        {{date('d/m/Y', strtotime($sponsorsActivity->sa_end_date))}}
                                    </td>
                                    <td>
                                        @if ($sponsorsActivity->deleted == 1)
                                            <i class="s_active fa fa-square"></i>
                                        @else
                                            <i class="s_inactive fa fa-square"></i>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <a href="{{ url('/admin/edit-sponsor-activity') }}/{{$sponsorsActivity->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="9"><center>{{trans('labels.norecordfound')}}</center></td>
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
    $('#sponsor_list').DataTable();
</script>
@stop