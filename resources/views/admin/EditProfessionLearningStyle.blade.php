@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>
        {{trans('labels.lblprofessionlearningstyle')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($learningstyleDetail) &&!empty($learningstyleDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.lblprofessionlearningstyle')}}</h3>
                </div><!-- /.box-header -->
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>

                <form id="addProfessionLearningStyle" class="form-horizontal" method="post" action="{{ url('/admin/saveProfessionLearningStyle') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($learningstyleDetail) &&!empty($learningstyleDetail)) ? $learningstyleDetail[0]->pls_profession_id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="professions" class="col-sm-2 control-label">Select Profession</label>
                            @if(isset($learningstyleDetail) && !empty($learningstyleDetail))
                            <?php
                                if (old('pls_profession'))
                                    $pls_profession = old('pls_profession');
                                elseif ($learningstyleDetail)
                                    $pls_profession = $learningstyleDetail[0]->pf_name;
                                else
                                    $pls_profession = '';
                            ?>
                            <div class="col-sm-8">
                                <input type="hidden" class="form-control" name="pls_profession_id" value="{{$pls_profession}}" readonly="readonly">
                                <input type="text" class="form-control" name="pls_profession" value="{{$pls_profession}}" readonly="readonly">
                            </div>
                            @else

                            <div class="col-sm-8">
                                <select class="form-control" name="pls_profession_id" id="pls_profession_id">
                                    <option value="">{{trans('labels.formlblselectprofessionheader')}}</option>
                                </select>
                            </div>
                            @endif
                        </div>

                        @forelse($learningstyleDetail as $style)
                            <?php
                                $pls_parameter_id = $style->pls_parameter_id;
                                $pls_activity_name = $style->pls_activity_name;

                                if (intval($pls_activity_name) > 0) {
                                    $concepts = Helpers::getConceptName($pls_activity_name);
                                } else {
                                    $concepts = $pls_activity_name;
                                }

                                $concepts = str_replace(',', '##', $concepts);
                            ?>
                            <div class="form-group">
                                <label for="Factual Remembering" class="col-sm-2 control-label">{{ucwords (str_replace('_',' ',$style->ls_name))}}</label>
                                <input type="hidden" class="form-control" name="pls_parameter_id[]" value="{{$pls_parameter_id}}">
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="activity_name[]" value="{{$concepts}}" placeholder="{{ucwords (str_replace('_',' ',$style->ls_name))}}">
                                    <input type="hidden" class="form-control" name="pls_activity_name[]" value="{{$pls_activity_name}}" placeholder="">
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

<?php // $row++; }  ?>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('/admin/professionLearningStyle') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section><!-- /.content -->
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">

jQuery(document).ready(function () {

    var validationRules = {
        pls_profession_id: {
            required: true
        }

    }

    $("#addProfessionLearningStyle").validate({
        rules: validationRules,
        messages: {
            pls_profession_id: {
                required: "<?php echo trans('validation.professionoptionrequired'); ?>"
            }
        }
    });
});
</script>
@stop