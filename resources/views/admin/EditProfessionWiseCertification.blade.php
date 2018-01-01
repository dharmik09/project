@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professionwisecertification')}}
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
                    <h3 class="box-title"><?php echo (isset($data) && !empty($data)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.professionwisecertification')}}</h3>
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
                <form id="addProfessionWiseCertification" class="form-horizontal" method="post" action="{{ url('/admin/saveProfessionWiseCertification') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="delete_id" value="<?php echo (isset($data) && !empty($data)) ? $data->profession_id : '0' ?>">
                    <div class="box-body">
                        <?php
                            if (old('profession_id'))
                                $profession_id = old('profession_id');
                            elseif ($data)
                                $profession_id = $data->profession_id;
                            else
                                $profession_id = '';
                        ?>
                        <div class="form-group">
                            <label for="profession_id" class="col-sm-2 control-label">{{trans('labels.lblselectprofession')}}</label>
                            <div class="col-sm-6">
                                <select id="profession_id" name="profession_id"  class="form-control">
                                    <option value="" disabled selected>{{trans('labels.lblselectprofession')}}</option>
                                    
                                    @forelse ($professionList as $professionData)
                                        <option value="{{$professionData->id}}" <?php if(isset($profession_id) && ($profession_id == $professionData->id)){ echo "selected"; } ?>>{{ucfirst($professionData->pf_name)}}</option>
                                    @empty
                                    @endforelse

                                </select>
                            </div>
                        </div>

                        <?php
                            if (old('certificate_id'))
                                $certificate_id = old('certificate_id');
                            elseif ($data)
                                $value = explode(",",$data->certificate_id);
                            else
                                $value = [];
                        ?>
                        <div class="form-group">
                            <label for="certificate_id" class="col-sm-2 control-label">{{trans('labels.lblselectcertificate')}}</label>
                            <div class="col-sm-6">
                                <div class="form-check form-check-inline">
                                    <select class="form-control chosen-select" id="certificate_id" name="certificate_id[]"  multiple="multiple" data-placeholder="Choose a Certificate...">
                                    <option value="">{{trans('labels.lblselectcertificate')}}</option>
                                    @forelse ($certificateList as $certificateData)
                                        <option value="{{$certificateData->id}}" <?php foreach($value as $certificate_id){ if(isset($certificate_id) && ($certificate_id == $certificateData->id)){ echo 'selected'; }} ?>>{{ucfirst($certificateData->pc_name)}}</option>
                                    @empty
                                        <label>{{trans('labels.lblnocertificateavailable')}}</label>
                                    @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professionWiseCertifications') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var config = {
            '.chosen-select'           : {},
            '.chosen-select-deselect'  : {allow_single_deselect:true},
            '.chosen-select-no-single' : {disable_search_threshold:10},
            '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            '.chosen-select-width'     : {width:"95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }

        var validationRules = {
            profession_id : {
                required : true
            },
            'certificate_id[]' : {
                required : true
            }
        }

        $("#addProfessionWiseCertification").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                profession_id : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                'certificate_id[]' : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
@stop
