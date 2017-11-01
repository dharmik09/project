@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1qualities')}}
        <a href="{{ url('developer/addLevel1Quality') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
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
                    <table id="listLevel1Quality" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.level1qualityblheadname')}}</th>
                                <th>{{trans('labels.level1qualityblheadstatus')}}</th>
                                <th>{{trans('labels.level1qualityblheadactions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($level1qualities as $qualities)
                            <tr>
                                <td>
                                    {{$qualities->l1qa_name}}
                                </td>

                                <td>
                                     @if ($qualities->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a target="_blank" href="{{ url('/developer/editLevel1Quality') }}/{{$qualities->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/developer/deleteLevel1Quality') }}/{{$qualities->id}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listLevel1Quality').DataTable();
    });
</script>
@stop