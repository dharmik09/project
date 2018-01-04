@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.headers')}}
         <a href="{{ url('admin/addHeader') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <table id="listHeader" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.headerblheadprofession')}}</th>
                                <th>{{trans('labels.headerblheadcountry')}}</th>
                                <th>{{trans('labels.headerblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = ''; ?>
                            @forelse($headers as $header)
                            <tr>
                                <?php $count++; ?>
                                <td>
                                    {{$header->pf_name}}
                                </td>
                                <td>
                                    <select class="form-control" id="p_country{{$header->pfic_profession}}" name="p_country">
                                      <option value="demo">{{trans('labels.formlblselectcountry')}}</option>
                                    <?php
                                        $countryId = explode(',', $header->country_id);
                                        $countryName = explode(',', $header->country_name);
                                        for($i=0;$i<count($countryId);$i++)
                                        {
                                    ?>
                                            <option value="{{$countryId[$i]}}">{{$countryName[$i]}}</option>
                                    <?php
                                        }
                                    ?>
                                    </select>
                                </td>
                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <input type="hidden" id="url{{$header->pfic_profession}}" value="{{ url('/admin/editHeader') }}/{{$header->pfic_profession}}">
                                    <a id="editUrl{{$header->pfic_profession}}" href="{{ url('/admin/editHeader') }}/{{$header->pfic_profession}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                    <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteHeader') }}/{{$header->pfic_profession}}"><i class="i_delete fa fa-trash"></i></a>
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
        $('#listHeader').DataTable();
    });
    <?php foreach ($headers as $header) { ?>
    $('#p_country{{$header->pfic_profession}}').on('change', function (e) {
        var attr = $('#url{{$header->pfic_profession}}').val() + "/" + this.value;
        $('#editUrl{{$header->pfic_profession}}').attr('href', attr);    
    });

    $('#editUrl{{$header->pfic_profession}}').on('click', function (e) {
        if($('#p_country{{$header->pfic_profession}}').val() =='demo'){
            alert("{{trans('validation.selectcountryforprofessionedit')}}");
            return false;
        }
    });
    <?php } ?>
</script>
@stop