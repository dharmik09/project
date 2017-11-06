@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Hint
        <a href="{{ url('admin/hintLogic') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listHint" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                               <th>{{trans('labels.serialnumber')}}</th>
                               <th>Applied Page</th>
                               <th>Hint</th>
                               <th>Image</th>
                               <th>Status</th>
                               <th>{{trans('labels.basketblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($hints as $hint)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{$hint->applied_level}}
                                </td>
                                <td>
                                    {{$hint->hint_text}}
                                </td>
                                <td>                                
                                    <?php if($hint->hint_image != '' && file_exists($hintOriginalImageUploadPath . $hint->hint_image)) { 
                                      $image = asset($hintOriginalImageUploadPath . $hint->hint_image);   
                                    }else{
                                      $image = asset($hintOriginalImageUploadPath . 'proteen-logo.png');  
                                    }
                                    ?>
                                    <img src="{{ $image }}" alt="" width="45px" height="45px">
                                   
                                </td>
                                <td>
                                    @if ($hint->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editHintLogic') }}/{{$hint->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteHint') }}/{{$hint->id}}"><i class="i_delete fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                            </tr>
                            @endforelse
                        </tbody>
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
        $('#listHint').DataTable();
    });
</script>
@stop