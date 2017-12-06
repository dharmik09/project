@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.baskets')}}
        <a href="{{ url('admin/addBasket') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listBasket" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                               <th>{{trans('labels.serialnumber')}}</th>
                               <th>{{trans('labels.basketblheadname')}}</th>
                               <th>{{trans('labels.basketblheadlogo')}}</th>
                               <th>{{trans('labels.basketblheadstatus')}}</th>
                               <th>{{trans('labels.basketblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($baskets as $basket)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$basket->b_name}}
                                </td>
                                <td>                                
                                    <?php 
                                        $image = ($basket->b_logo != "" && isset($basket->b_logo)) ? Storage::url($uploadBasketThumbPath.$basket->b_logo) : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                   
                                </td>
                                <td>
                                    @if ($basket->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editBasket') }}/{{$basket->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteBasket') }}/{{$basket->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td><center>{{trans('labels.norecordfound')}}</center></td>
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
        $('#listBasket').DataTable();
    });
</script>
@stop