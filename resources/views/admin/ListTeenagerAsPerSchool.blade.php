@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.teenagers')}}
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
                <div class="box-header">
                    @forelse($teenagers as $value)
                        <?php $school_id = $value->t_school; ?>
                    @empty
                        <?php $school_id = $id; ?>
                    @endforelse
                </div>
                <div class="box-body">
                    <table class="table table-striped" id="school_teenager_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.teentblheadname')}}</th>
                                <th>{{trans('labels.teentblheademail')}}</th>
                                <th>{{trans('labels.teentblheadphone')}}</th>
                                <th>{{trans('labels.teentblheadbirthdate')}}</th>
                                <th>School Validated</th>
                                <th>Verified Status</th>
                                <th>Sign Up Date</th>
                                <th>{{trans('labels.teenblheadstatus')}}</th>
                                <th>{{trans('labels.tblheadactions')}}</th>
                            </tr>
                        </thead>
                        <?php $serialno = 0; ?>
                        <tbody>
                            @forelse($teenagers as $teenager)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    <a target="_blank" href=" {{url('/admin/view-teenager')}}/{{$teenager->id}} ">{{$teenager->t_name}}</a>
                                </td>
                                <td>
                                    {{$teenager->t_email}}
                                </td>
                                <td>
                                    {{$teenager->t_phone}}
                                </td>
                                <td>
                                    {{date('d-m-Y',strtotime($teenager->t_birthdate))}}
                                </td>
                                <td>
                                    {{$teenager->email_sent}}
                                </td>
                                <td><?php echo ($teenager->t_isverified == 1)?"<span class='yes0'>Yes</span>":"<span class='no0'>No</span>"; ?></td>
                                <td>
                                    {{date('d/m/Y',strtotime($teenager->created_at))}}
                                </td>
                                <td>
                                    @if ($teenager->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/admin/edit-teenager') }}/{{$teenager->id}}/{{$teenager->t_school}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10"><center>{{trans('labels.norecordfound')}}</center></td>
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
    $(document).ready(function(){
        $('#school_teenager_table').DataTable();   
    });
</script>
@stop