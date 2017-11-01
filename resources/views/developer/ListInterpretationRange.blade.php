@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.interpretationrange')}}
        <?php if(isset($interpretationRangeDetail) && empty($interpretationRangeDetail)){ ?>
            <a href="{{ url('developer/addInterpretationRange') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
        <?php } else {?>
            <a href="{{ url('/developer/editInterpretationRange') }}" class="btn btn-block btn-primary add-btn-primary pull-right"> Edit </a>
        <?php } ?>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header"></div>
                <div class="box-body">
                    <table id="listInterpretationRange" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.formlbltext')}} </th>
                                <th>{{trans('labels.formlblrange')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interpretationRangeDetail as $range)
                            <tr>
                                <td>
                                    {{$range['ir_text']}}
                                </td>
                                <td>
                                <?php
                                    if ($range['ir_min_score'] == $range['ir_max_score']) {
                                    ?>
                                        {{$range['ir_min_score']}}
                                    <?php
                                    } else {
                                    ?>
                                        {{$range['ir_min_score']}} - {{$range['ir_max_score']}}
                                    <?php
                                    }
                                    ?>
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
        $('#listInterpretationRange').DataTable();
    });
</script>
@stop