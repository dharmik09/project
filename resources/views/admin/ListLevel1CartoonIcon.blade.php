@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<div id="successDiv" class="col-md-12" style="display: none;">
    <div class="box-body">
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
            <span class="successDiv"></span>
        </div>
    </div>
</div>
<div id="errorDiv" class="col-md-12" style="display: none;">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
            <span class="errorDiv"></span>
        </div>
    </div>
</div>
<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.level1cartoonicons')}}
        </div>
	<div class="col-md-2">
            <a href="{{ url('admin/viewUserImage') }}" class="btn btn-block btn-primary">{{trans('labels.viewuserimage')}}</a>
        </div>
	<div class="col-md-1">
            <a href="{{ url('admin/addCartoon') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/uploadCartoons') }}" class="btn btn-block btn-primary">{{trans('labels.bulkupload')}}</a>
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="col-md-3">
                    <label for="selectAll">Select All:</label>&nbsp;&nbsp;&nbsp;
                    <input id="selectAll" name="selectAll" type="checkbox" value="">
                </div>
                <div class="col-md-3">
                    <a id="bulkDelete" href="javascript:void(0);" class="btn btn-block btn-primary bulkDelete" >Bulk Delete</a>
                </div>
            </div>
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body level1CartoonIcon">
                    @include('admin/Level1CartoonIconData')
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#listCartoonIcon').DataTable();
        $('#selectAll').click(function () {
            if ($(this).hasClass('allChecked')) {
                $("input[name='iconsCheckbox[]']").prop('checked', false);
            } else {
                $("input[name='iconsCheckbox[]']").prop('checked', true);
            }
            $(this).toggleClass('allChecked');
        });
    });
    $("#bulkDelete").click(function() {
        if ($("input:checkbox:checked").length == 0) {
            alert("Check at least one checkbox");
        } else {
            $("#bulkDelete").toggleClass('sending').blur();
            var iconsCheckbox = [];
            $("input[name='iconsCheckbox[]']").each( function () {
                if ($(this).prop('checked') == true) {
                    iconsCheckbox.push($(this).val());
                }
            });
            $.ajax({
                url: "{{url('admin/bulkDeleteCartoonIcons')}}",
                type : 'POST',
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                data: {
                    deletedIconsId: iconsCheckbox,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        $(".successDiv").text(response.message);
                        $("#successDiv").css('display', 'block');
                        $("#successDiv .alert").addClass('show');
                    } else {
                        $(".errorDiv").text(response.message);
                        $("#errorDiv").css('display', 'block');
                        $("#errorDiv .alert").addClass('show');
                    }
                    $("#selectAll").prop('checked', false);
                    $(".level1CartoonIcon").html(response.view);
                    $("#bulkDelete").removeClass('sending').blur();
                }
            });
        }
    });

</script>
@stop