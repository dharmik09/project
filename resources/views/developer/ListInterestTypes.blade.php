@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.interesttypes')}}
        <a href="{{ url('developer/addInterestType') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
                      <table id="listIntrestType" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.interestblheadname')}}</th>
                                <th>{{trans('labels.interestblheadlogo')}}</th>
                                <th>{{trans('labels.interestblheadstatus')}}</th>
                                <th>{{trans('labels.interestblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interesttypes as $interest)
                            <tr>
                                <td>
                                    {{$interest->it_name}}
                                </td>
                                <td>
                                    <?php  
                                    if(isset($interestThumbPath)){ 
                                        if(File::exists(public_path($interestThumbPath.$interest->it_logo)) && $interest->it_logo != '') { ?>
                                            <img src="{{ url($interestThumbPath.$interest->it_logo) }}" alt="{{$interest->it_logo}}" >
                                        <?php }else{ ?>
                                            <img src="{{ asset('/backend/images/avatar5.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');?>" width="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');?>">
                                    <?php   }
                                        }
                                    ?>
                                </td>
                                <td>
                                     @if ($interest->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a target="_blank" href="{{ url('/developer/editInterestType') }}/{{$interest->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deleteInterestType') }}/{{$interest->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listIntrestType').DataTable();
    });
</script>
@stop