@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.lblcoinpackage')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addCoins') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listCoinsPackage" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.packagename')}}</th>
                                <th>{{trans('labels.formlblcoins')}}</th>
                                <th>{{trans('labels.price')}}</th>
                                <th>{{trans('labels.formusertype')}}</th>
                                <th>{{trans('labels.cmsblheadstatus')}}</th>
                                <th>{{trans('labels.professionblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($coinsDetail as $coins)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{ucwords (str_replace('_',' ',$coins->c_package_name))}}
                                </td>
                                <td>
                                    {{ $coins->c_coins }}
                                </td>
                                <td>
                                    {{ $coins->c_price }}
                                </td>
                                <td>
                                    @if ($coins->c_user_type == 1)
                                        Teen/Parent/Mentor
                                    @else
                                        Sponsor
                                    @endif
                                </td>
                                <td>
                                    @if ($coins->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editCoins') }}/{{$coins->id}}/{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteCoins') }}/{{$coins->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listCoinsPackage').DataTable();
    });
</script>
@stop