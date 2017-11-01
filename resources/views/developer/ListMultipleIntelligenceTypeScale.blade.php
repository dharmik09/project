@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.multipleintelligencetypescale')}}
        <?php if(isset($multipleintelligencetypescale) && empty($multipleintelligencetypescale)){ ?>
            <a href="{{ url('developer/addMultipleintelligenceTypeScale') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
        <?php } else {?>
            <a href="{{ url('/developer/editMultipleintelligenceTypeScale') }}" class="btn btn-block btn-primary add-btn-primary pull-right"> Edit </a>
        <?php } ?>
    </h1>
</section>

<section class="content">
    <div class="row">
        
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header"></div>
                <div class="box-body">
                    <table id="listMultipleintelligenceTypeScale" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.personalityblheadname')}} </th>
                                <th>{{trans('labels.formlblhighrange')}}</th>
                                <th>{{trans('labels.formlblmoderaterange')}}</th>
                                <th>{{trans('labels.formlbllowrange')}}</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($multipleintelligencetypescale as $scales)
                            <tr>
                                <td>
                                        {{$scales->multipleIntelligent->mit_name}}
                                </td>
                                <td>
                                <?php
                                    if($scales->mts_high_min_score == $scales->mts_high_max_score)
                                    {
                                    ?>
                                        {{$scales->mts_high_min_score}}
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        {{$scales->mts_high_min_score}} - {{$scales->mts_high_max_score}}
                                    <?php
                                    }
                                    ?>
                                </td>

                                <td>
                                <?php
                                    if($scales->mts_moderate_min_score == $scales->mts_moderate_max_score)
                                    {
                                    ?>
                                        {{$scales->mts_moderate_min_score}}
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        {{$scales->mts_moderate_min_score}} - {{$scales->mts_moderate_max_score}}
                                    <?php
                                    }
                                    ?>
                                </td>

                                <td>
                                <?php
                                    if($scales->mts_low_min_score == $scales->mts_low_max_score)
                                    {
                                    ?>
                                        {{$scales->mts_low_min_score}}
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        {{$scales->mts_low_min_score}} - {{$scales->mts_low_max_score}}
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
        $('#listMultipleintelligenceTypeScale').DataTable();
    });
</script>
@stop