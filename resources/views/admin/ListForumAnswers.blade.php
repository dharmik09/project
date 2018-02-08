@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.lblforumanswer')}}
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
                    <h3>{{trans('labels.lblforumquestion')}}</h3>
                    <p>{{$questionData->fq_que}}</p>
                    <table id="listLevel1Traits" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.lblforumteenname')}}</th>
                                <th>{{trans('labels.lblforumanswertime')}}</th>
                                <th>{{trans('labels.lblforumanswer')}}</th>
                                <th>{{trans('labels.activityblheadstatus')}}</th>
                                <th>{{trans('labels.traitslblheadaction')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($data as $value)
                            <?php $serialno++; ?>
                            <tr>
                                <td>
                                    <?php echo $serialno; ?>
                                </td>
                                <td>
                                    {{ucfirst($value->teenager->t_name).' '.ucfirst($value->teenager->t_lastname)}}
                                </td>
                                <td>
                                    {{date('jS M Y',strtotime($value->created_at))}}
                                </td>
                                <td>
                                    @if(strlen($value->fq_ans)>50)
                                        {{substr($value->fq_ans, 0, 50) . '...'}}
                                        <a href="#" data-toggle="modal" data-target="#forumAnswerModel{{$value->id}}">Read More</a>
                                        <div class="modal modal-centered fade" id="forumAnswerModel{{$value->id}}" tabindex="-1" role="dialog" aria-labelledby="forumAnswerModel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3>Forum Answer</h3>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{$value->fq_ans}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{$value->fq_ans}}
                                    @endif
                                </td>
                                <td>
                                    @if ($value->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                </td>
                                <td>
                                    @if ($value->deleted == 1)
                                        <a href="{{ url('/admin/changeanswerstatus').'/'.$value->id.'/'.Config::get('constant.INACTIVE_FLAG')}}" class="btn btn-block btn-danger btn-xs">{{trans('labels.lblmarkasinactive')}}</a>
                                    @else
                                        <a href="{{ url('/admin/changeanswerstatus').'/'.$value->id.'/'.Config::get('constant.ACTIVE_FLAG')}}" class="btn btn-block btn-success btn-xs">{{trans('labels.lblmarkasactive')}}</a>
                                    @endif
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
        $('#listLevel1Traits').DataTable();
    });
</script>
@stop