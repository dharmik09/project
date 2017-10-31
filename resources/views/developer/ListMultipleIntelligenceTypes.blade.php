@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.multipleintelligencetypes')}}
        <a href="{{ url('developer/addMultipleintelligenceType') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
                      <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{trans('labels.multipleintelligenceblheadname')}}</th>
                                <th>{{trans('labels.multipleintelligenceblheadlogo')}}</th> 
                                <th>{{trans('labels.multipleintelligenceblheadstatus')}}</th>
                                <th>{{trans('labels.multipleintelligenceblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($multipleintelligencetype as $multipleintelligence)
                            <tr>
                                <td>
                                    {{$multipleintelligence->mit_name}}
                                </td>
                                <td>
                                    <?php  
                                    if(isset($miThumbPath)){ 
                                        if(File::exists(public_path($miThumbPath.$multipleintelligence->mit_logo)) && $multipleintelligence->mit_logo != '') { ?>
                                            <img src="{{ url($miThumbPath.$multipleintelligence->mit_logo) }}" alt="{{$multipleintelligence->mit_logo}}" >
                                        <?php }else{ ?>
                                            <img src="{{ asset('/backend/images/avatar5.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_HEIGHT');?>" width="<?php echo Config::get('constant.CARTOON_THUMB_IMAGE_WIDTH');?>">
                                    <?php   }
                                        }
                                    ?>
                                </td>
                                <td>
                                     @if ($multipleintelligence->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a target="_blank" href="{{ url('/developer/editMultipleintelligenceType') }}/{{$multipleintelligence->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deleteMultipleintelligenceType') }}/{{$multipleintelligence->id}}"><i class="i_delete fa fa-trash"></i></a>
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