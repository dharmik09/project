@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
            {{trans('labels.lblprofessionlearningstyle')}}
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addProfessionLeaningStyle') }}" class="btn btn-block btn-primary">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/importLearningStyle') }}" class="btn btn-block btn-primary">{{trans('labels.importdata')}}</a>
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">

            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body" style="overflow-x:scroll;">
                    <table id="listProfessionLearningStyle" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.profession')}}</th>
                                <th>Factual Remembering</th>
                                <th>Factual Understanding</th>
                                <th>Factual Applying & Analysing</th>
                                <th>Factual Evaluating & Creating</th>
                                <th>Conceptual Remembering</th>
                                <th>Conceptual Understanding</th>
                                <th>Conceptual Applying & Analysing</th>
                                <th>Conceptual Evaluating & Creating</th>
                                <th>Procedural Remembering</th>
                                <th>Procedural Understanding</th>
                                <th>Procedural Applying & Analysing</th>
                                <th>Procedural Evaluating & Creating</th>
                                <th>Meta-Cognitive Remembering</th>
                                <th>Meta-Cognitive Understanding</th>
                                <th>Meta-Cognitive Applying & Analysing</th>
                                <th>Meta-Cognitive Evaluating & Creating</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($learningstyledetail as $style)
                            <tr>
                                <td>
                                    {{$style->pf_name}}
                                </td>
                                <?php $activity = explode('##', $style->activity_name); ?>
                                    @forelse($activity as $key => $value)
                                        @if(intval($value) > 0) 
                                            <?php $concepts = Helpers::getConceptName($value);?>
                                            <td>{{$concepts}}</td>
                                        @else
                                            <td>{{$value}}</td>
                                        @endif
                                    @empty
                                    @endforelse

                                <td>
                                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                    <a href="{{ url('/admin/editProfessionLearningStyle') }}/{{$style->pls_profession_id}}/{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>{{trans('labels.norecordfound')}}</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td>
                                <td></td><td></td><td></td><td></td><td></td><td></td>
                                <td></td><td></td><td></td><td></td><td></td>
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
        $('#listProfessionLearningStyle').DataTable();
    });
</script>
@stop