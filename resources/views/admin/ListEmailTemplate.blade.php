@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.emailtemplates')}}
         <a href="{{ url('admin/addTemplate') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listEmailTemplate" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.templateblheadname')}}</th>
                                <th>{{trans('labels.templateblheadpseudoname')}}</th>
                                <th>{{trans('labels.templateblheadsubject')}}</th>
                                <th>{{trans('labels.templateblheadstatus')}}</th>
                                <th>{{trans('labels.templateblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($template as $templates)
                            <tr>
                            <td>
                                {{$templates->et_templatename}}
                            </td>
                            <td>
                                {{$templates->et_templatepseudoname}}
                            </td>
                            <td>
                                {{$templates->et_subject}}
                            </td>
                            <td>
                                @if ($templates->deleted == 1)
                                <i class="s_active fa fa-square"></i>
                                @else
                                    <i class="s_inactive fa fa-square"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/admin/editTemplate') }}/{{$templates->id}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                            </td>
                         </tr>
                         @empty
                         <tr>
                            <td colspan="5"><center>{{trans('labels.norecordfound')}}</center></td>
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
        $('#listEmailTemplate').DataTable();
    });
</script>
@stop