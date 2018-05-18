@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            User Advance Level Tasks
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
                    <table class="table table-striped" id="list_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>UserName</th>
                                <th>Professions</th>
                                <th>Submission date</th>
                                <th>Task Status</th>
                                <th>View More</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  $serialno = 1; ?>
                            @forelse($userTasks  as $task)
                            <tr>
                                <td>
                                    <?php  echo $serialno; ?>
                                </td>
                                <td>
                                    {{$task->t_name}}
                                </td>
                                <td>
                                    {{$task->pf_name}}
                                </td>
                                <td>
                                    <?php echo date('d F Y', strtotime($task->created_at)); ?>
                                </td>
                                <td>
                                <?php
                                    switch($task->l4aaua_is_verified) {
                                        case 0:
                                            echo "Not Submitted";
                                            break;

                                        case 1:
                                            echo "Pending";
                                            break;

                                        case 2:
                                            echo "Verified";
                                            break;

                                        case 3:
                                            echo "Rejected";
                                            break;

                                        default:
                                            echo "-";
                                            break;
                                    };
                                ?>
                                </td>
                                <td>
                                    <?php $type = (isset($_GET['type']) && $_GET['type'] > 0 )? "?".$_GET['type']."": 3;?>
                                    <a href="{{ url('/admin/viewUserAllAdvanceActivities') }}/{{$task->l4aaua_teenager}}/{{$task->l4aaua_profession_id}}/{{$type}}"><i class="fa fa-eye"></i> &nbsp;&nbsp;</a>
                                </td>
                            </tr>
                            <?php  $serialno++; ?>
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
        $('#list_table').DataTable();
    </script>
@stop