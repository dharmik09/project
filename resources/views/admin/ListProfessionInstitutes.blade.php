@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-11">
            {{trans('labels.professioninstitueslist')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addProfessionInstituteCourseList') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
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
                    <table id="listProfessionschoollist" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.lblinstitutesid')}}</th>
                                <th>{{trans('labels.lblname')}}</th>
                                <th>{{trans('labels.lblstate')}}</th>
                                <th>{{trans('labels.lblpincode')}}</th>
                                <th>{{trans('labels.lblaffialteduniversity')}}</th>
                                <th>{{trans('labels.lblmanagement')}}</th>
                                <th>{{trans('labels.lblaccredationbody')}}</th>
                                <th>{{trans('labels.lblisaccredation')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($professionSchoolData as $data)
                            <tr>
                                <td>
                                    <span title="">{{$data->school_id}}</span>
                                </td>
                                <td>
                                    {{$data->college_institution}}
                                </td>
                                <td>
                                    {{$data->institute_state}}
                                </td>
                                <td>
                                    {{$data->pin_code}}
                                </td>
                                <td>
                                    {{$data->affiliat_university}}
                                </td>
                                <td>
                                    {{$data->management}}
                                </td>
                                <td>
                                    {{$data->accreditation_body}}
                                </td>
                                <td>
                                    @if($data->is_accredited == 1)
                                        True
                                    @elseif($data->is_accredited == 0)
                                        False
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
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
        $('#listProfessionschoollist').DataTable();
    });
</script>
@stop