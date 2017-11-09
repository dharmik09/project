@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.personalitytypes')}}
        <a href="{{ url('developer/addPersonalityType') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
                    <table id="listPersonalityType" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.personalityblheadname')}}</th>
                                <th>{{trans('labels.personalityblheadlogo')}}</th>
                                <th>{{trans('labels.personalityblheadstatus')}}</th>
                                <th>{{trans('labels.personalityblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personalitytypes as $personality)
                                <tr>
                                    <td>
                                        {{$personality->pt_name}}
                                    </td>
                                    <td>
                                        <?php  
                                            if(isset($personalityThumbPath)) { 
                                                $image = ($personality->pt_logo != "" && Storage::disk('s3')->exists($personalityThumbPath.$personality->pt_logo)) ? Config::get('constant.DEFAULT_AWS').$personalityThumbPath.$personality->pt_logo : asset('/backend/images/proteen_logo.png'); ?>
                                            <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                        <?php } ?>
                                    </td>
                                    <td>
                                         @if ($personality->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                        @else
                                            <i class="s_inactive fa fa-square"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a target="_blank" href="{{ url('/developer/editPersonalityType') }}/{{$personality->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                        <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deletePersonalityType') }}/{{$personality->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listPersonalityType').DataTable();
    });
</script>
@stop