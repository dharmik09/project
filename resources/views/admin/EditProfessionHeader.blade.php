@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.headers')}}
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
                    <h3 class="box-title"><?php echo (isset($headerDetail) && !empty($headerDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.header')}}</h3>
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
                <form id="addHeaders" class="form-horizontal" method="post" action="{{ url('/admin/saveHeader') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="pfic_profession" class="col-sm-2 control-label">{{trans('labels.formlblprofession')}}</label>
                            <div class="col-sm-10">
                                @if(isset($headerDetail) && !empty($headerDetail))
                                <?php
                                if (old('pfic_profession'))
                                    $pfic_profession = old('pfic_profession');
                                elseif ($headerDetail) {
                                    $pfic_id = $headerDetail[0]->pfic_profession;
                                    $pf_detail = Helpers::getProfessionName($pfic_id);
                                    $pfic_profession = $pf_detail[0]->pf_name;
                                } else
                                    $pfic_profession = '';
                                ?>
                                <input type="hidden" name="pfic_profession_id" value="{{$pfic_id}}" id="pfic_profession" />
                                <input type="text" class="form-control" name="pfic_profession" value="{{$pfic_profession}}" id="pfic_profession" readonly="readonly"/>
                                @else 
                                <?php $professions = Helpers::getActiveProfessions(); ?>
                                <select class="form-control" id="pfic_profession" name="pfic_profession">
                                    <option value="">{{trans('labels.formlblselectprofessionheader')}}</option>
                                        <?php foreach ($professions as $key => $value) { ?>
                                        <option value="{{$value->id}}" >{{$value->pf_name}}({{$value->id}})</option>
                                    <?php } ?>
                                </select>
                                
                                @endif


                            </div>
                        </div>
                        <div id="addmoreheader" class="addmoreheader">
<?php
if (isset($headerDetail) && !empty($headerDetail)) {
    foreach ($headerDetail as $key => $data) {
        ?>
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 control-label">{{trans('labels.formlblsubtitle')}}</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="pfic_title" name="pfic_title[{{$data->id}}]" placeholder="{{trans('labels.formlblsubtitle')}}" value="{{ $data->pfic_title}}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="pfic_content" class="col-sm-2 control-label">{{trans('labels.formlblcontent')}}</label>
                                        <div class="col-sm-10">
                                            <textarea name="pfic_content[{{$data->id}}]" id="pfic_content<?php echo $key; ?>">{{$data->pfic_content}}</textarea>
                                        </div>
                                    </div>
        <?php
    }
}
?>
                        </div>
                        @if(isset($headerDetail) && !empty($headerDetail))

                        @else
                        <div class="form-group">
                            <label  class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">
                                <a href="#" class="btn btn-success fa" name="add" id="add">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/headers') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop
@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
$('textarea').each(function () {

    CKEDITOR.replace($(this).attr('id'));

});
var numberOfContent = 1;
jQuery(document).ready(function ()
{

    jQuery.validator.addMethod("emptyetbody", function (value, element) {
        var data = CKEDITOR.instances['pfic_content'].getData();

        return data != '';
    }, "<?php echo trans('validation.requiredfield') ?>");
    var wrapper = $("#addmoreheader");
    $('#add').click(function (e)
    {
        e.preventDefault();
        var header = '<div class="form-group">' +
                '<label for="" class="col-sm-2 control-label">{{trans("labels.formlblsubtitle")}}</label>' +
                '<div class="col-sm-10">' +
                '<input type="text" class="form-control" id="pfic_title" name="pfic_title[]" placeholder="{{trans("labels.formlblsubtitle")}}" value="" />' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="pfic_content" class="col-sm-2 control-label">{{trans("labels.formlblcontent")}}</label>' +
                '<div class="col-sm-10">' +
                '<textarea name="pfic_content[]" id="newcontent' + numberOfContent + '"></textarea>' +
                '</div>' +
                '</div>';

        $(wrapper).append(header);
        CKEDITOR.replace('newcontent' + numberOfContent);
        numberOfContent++;
    });

    var validationRules =
            {
                pfic_profession: {
                    required: true
                },
                'pfic_content[]': {
                    emptyetbody: true
                },
                'pfic_title[]': {
                    required: true
                },
                deleted: {
                    required: true
                }

            }


    $("#addHeaders").validate({
        ignore: "",
        rules: validationRules,
        messages: {
            pfic_profession: {
                required: "<?php echo trans('validation.requiredfield'); ?>"
            },
            'pfic_content[]': {
                emptyetbody: "<?php echo trans('validation.requiredfield'); ?>"
            },
            'pfic_title[]': {
                required: "<?php echo trans('validation.requiredfield'); ?>"
            },
            deleted: {
                required: "<?php echo trans('validation.requiredfield'); ?>"
            }

        }
    });
});
</script>
@stop