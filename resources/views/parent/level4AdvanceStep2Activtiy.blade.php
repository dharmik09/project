@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
            @include('teenager/teenagerLevelPointBox')
                <a class="back_me" href="{{url('parent/level4-advance')}}/{{$professionDetail[0]->id}}/{{$response['teen_id']}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Back</span></a>
                @if(isset($professionDetail) && !empty($professionDetail))
                <ul class="nav nav-tabs" id="bestSelectFrom">
                    <li id="3" data-type="Image" class="{{(isset($typeId) && $typeId == 3)?'active':''}}" onclick="setMediaType(3, 'Image')"><a data-toggle="tab" href="#image" class="tabClass">Image</a></li>
                    <li id="2" data-type="Document" class="{{(isset($typeId) && $typeId == 2)?'active':''}}" onclick="setMediaType(2, 'Document')"><a data-toggle="tab" href="#document" class="tabClass">Document</a></li>
                    <li id="1" data-type="Video" class="{{(isset($typeId) && $typeId == 1)?'active':''}}" onclick="setMediaType(1, 'Video')"><a data-toggle="tab" href="#video" class="tabClass">Video</a></li>
                </ul>
                <div class="tab-content">
                    <div class="level_icon"><h2>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h2></div>
                    @if($typeId == 3 && count($userLevel4AdvanceImageTask) < 5)
                    <form id="add_advance_task" class="form-horizontal" method="post" action="{{ url('/parent/submit-level4-advance-activity') }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="profession_id" value="{{ $professionId }}">
                        <input type="hidden" name="media_type" id="media_type" value="3">
                        <div class="my_updmain">
                            <div class="my_upd">
                                <div for="file-input" class="l4_upld">
                                    <img src="{{ Storage::url('frontend/images/picture-mini.png')}}" id="previe"/>
                                    <p id="info_meta">Click to add <span id="media_name_image_tag"></span></p>
                                    <span class="hidden_box"></span>
                                </div>
                                <input id="file-input" data-filetype='{{$typeId}}' name="advance_task" type="file" onchange="readURL2(this);" class="l4_input_upld" accept=".png, .jpg, .jpeg, .bmp"/>
                            </div>
                        </div>
                        <div id="err"></div>
                        <div class="save_image"><input type="submit" id="submitData" value="Save" class="btn primary_btn"></div>
                    </form>
                    @endif
                    @if($typeId == 2 && count($userLevel4AdvanceDocumentTask) < 1)
                    <form id="add_advance_task" class="form-horizontal" method="post" action="{{ url('/parent/submit-level4-advance-activity') }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="profession_id" value="{{ $professionId }}">
                        <input type="hidden" name="media_type" id="media_type" value="3">
                        <div class="my_updmain">
                            <div class="my_upd">
                                <div for="file-input" class="l4_upld">
                                    <img src="{{ Storage::url('frontend/images/picture-mini.png')}}" id="previe"/>
                                    <p id="info_meta">Click to add <span id="media_name_image_tag"></span></p>
                                    <span class="hidden_box"></span>
                                </div>
                                <input id="file-input" data-filetype='{{$typeId}}' name="advance_task" type="file" onchange="readURL2(this);" class="l4_input_upld"/>
                            </div>
                        </div>
                        <div id="err"></div>
                        <div class="save_image"><input type="submit" id="submitData" value="Save" class="btn primary_btn"></div>
                    </form>
                    @endif
                    @if($typeId == 1 && count($userLevel4AdvanceVideoTask) < 1)
                    <form id="add_advance_task" class="form-horizontal" method="post" action="{{ url('/parent/submit-level4-advance-activity') }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="profession_id" value="{{ $professionId }}">
                        <input type="hidden" name="media_type" id="media_type" value="3">
                        <div class="my_updmain">
                            <div class="my_upd">
                                <div for="file-input" class="l4_upld">
                                    <img src="{{ Storage::url('frontend/images/picture-mini.png')}}" id="previe"/>
                                    <p id="info_meta">Click to add <span id="media_name_image_tag"></span></p>
                                    <span class="hidden_box"></span>
                                </div>
                                <input id="file-input" data-filetype='{{$typeId}}' name="advance_task" type="file" onchange="readURL2(this);" class="l4_input_upld"/>
                            </div>
                        </div>
                        <div id="err"></div>
                        <div class="save_image"><input type="submit" id="submitData" value="Save" class="btn primary_btn"></div>
                    </form>
                    @endif
                    
                    <div id="image" class="tab-pane fade {{(isset($typeId) && $typeId == 3)?'in active':''}}">
                        @include('parent/advanceImageTask')
                    </div>
                    <div id="document" class="tab-pane fade {{(isset($typeId) && $typeId == 2)?'in active':''}}">
                        @include('parent/advanceDocumentTask')
                    </div>
                    <div id="video" class="tab-pane fade {{(isset($typeId) && $typeId == 1)?'in active':''}}">
                        @include('parent/advanceVideoTask')
                    </div>
                </div>                    
                @else
                <div class="no_data_page">
                    <span class="nodata_outer">
                        <span class="nodata_middle">
                            No such Profession available!
                        </span>
                    </span>
                </div>

                @endif

            </div>
        </div>

    </div>
</div>

<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>

<div id="uploaded_content" class="modal fade hint_image_modal_show" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            <img src="" alt="">
        </div>
    </div>
</div>

@stop
@section('script')
<script type="text/javascript">
                        $(".table_container_outer").mCustomScrollbar({
                            axis: "yx"
                        });
                        function deleteLevel4AdvanceTaskUser(media_id, media_name, media_type)
                        {
                            resdelete = confirm('Are you sure you want to delete this record?');
                            if (resdelete) {
                                $.ajax({
                                    url: "{{ url('parent/delete-user-advance-task') }}",
                                    type: 'post',
                                    data: {
                                        "_token": '{{ csrf_token() }}',
                                        "task_id": media_id,
                                        "media_name": media_name,
                                        "media_type": media_type
                                    },
                                    success: function(response) {
                                        location.reload();
                                    }
                                });
                            } else {
                                return false;
                            }
                        }

                        function setMediaType(dataval, datatype) {
                            var fullurl = document.URL;
                            $('#media_type').val(dataval);
                            $('#media_name_image_tag').text(datatype);
                            var str = fullurl;
                            var i = str.lastIndexOf('/');
                            var lastChar = str.substr(str.length - 1);
                            if (i != -1) {
                                str = str.substr(0, i);
                            }
                            var j = str.lastIndexOf('/');
                            if (j != -1) {
                                str = str.substr(0, j) + "/" + dataval;
                                str = str + "/" + lastChar;

                            }
                            location.replace(str);
                        }
                        jQuery(document).ready(function($) {

                            var currenttab = $('.nav-tabs li.active').attr('id');
                            var currenttabType = $('.nav-tabs li.active').data('type');
                            $('#media_type').val(currenttab);
                            $('#media_name_image_tag').text(currenttabType);
                            $(".acceptClass").change(function(e) {
                                var ext = this.value.match(/\.(.+)$/)[1];
                                ext = ext.toLowerCase();
                                switch (ext)
                                {
                                    case 'jpg':
                                    case 'bmp':
                                    case 'png':
                                    case 'jpeg':
                                    break;
                                    default:
                                        $("#err").html("Image type not allowed").fadeIn();
                                        this.value = '';
                                }
                            });
                            $('.l4_upld').click(function() {
                                $('.l4_input_upld').trigger('click');
                            });
                            var validationRules = {
                                advance_task: {
                                    required: true
                                }
                            };
                            $("#add_advance_task").validate({
                                rules: validationRules,
                                messages: {
                                    advance_task: {
                                        required: "",
                                    }
                                }
                            });
                            $("#add_advance_task").on('submit', (function(e) {
                                e.preventDefault();
                                var formStatus = $('#add_advance_task').validate().form();
                                var valueCheckBonz = $("#file-input").val();
                                if (true == formStatus) {
                                    $('.ajax-loader').show();
                                    $.ajax({
                                        url: "{{ url('parent/submit-level4-advance-activity') }}",
                                        type: "POST",
                                        data: new FormData(this),
                                        contentType: false,
                                        cache: false,
                                        processData: false,
                                        beforeSend: function()
                                        {
                                            //$("#preview").fadeOut();
                                            $("#err").fadeOut();
                                        },
                                        success: function(data)
                                        {
                                            $('.ajax-loader').hide();
                                            if (data == 'required')
                                            {
                                                // invalid file format.
                                                $("#err").html("Please select file to upload!").fadeIn();
                                            }
                                            else if (data == 'invalid')
                                            {
                                                // invalid file format.
                                                $("#err").html("Invalid File !").fadeIn();
                                            }
                                            else if (data == 'invalidmedia')
                                            {
                                                // invalid file format.
                                                $("#err").html("Invalid Type !").fadeIn();
                                            }
                                            else
                                            {
                                                $("#add_advance_task")[0].reset();
                                                location.reload();
                                            }
                                        },
                                        error: function(e)
                                        {
                                            $("#err").html(e).fadeIn();
                                        }
                                    });
                                } else {
                                    $("#err").html("Please select file to upload!").fadeIn();
                                    return false;
                                }
                            }));
                        });
                        function viewLargeImage(originalImage) {
                            $('#uploaded_content').modal('show');
                            $('#uploaded_content img').attr('src', originalImage);
                        }

                        function readURL2(input_file) {
                            var tt = $("#bestSelectFrom li.active").attr('id');
                            if (input_file.files && input_file.files[0]) {
                                $("#err").text('');
                                $("#submitData").removeAttr('disabled');
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    var fileType = input_file.files[0];
                                    if (tt == 3) {
                                        if (fileType.type == 'image/jpeg' || fileType.type == 'image/jpg' || fileType.type == 'image/png' || fileType.type == 'image/bmp') {
                                            if (input_file.files[0].size > 6000000) {
                                                $("#err").text("Maximum File Upload size is 6MB");
                                                $("#submitData").attr('disabled', 'disabled');
                                                $("#file-input").val('');
                                            }else{
                                                $("#err").text(fileType.name);
                                            }
                                        } else {
                                            $("#err").text("File type not allowed");
                                            $("#submitData").attr('disabled', 'disabled');
                                            $("#file-input").val('');
                                        }
                                    } else if (tt == 2) {
                                        if (fileType.type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || fileType.type == 'application/pdf' || fileType.type == 'application/msword' || fileType.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || fileType.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || fileType.type == 'application/vnd.ms-powerpoint') {
                                            if (input_file.files[0].size > 6000000) {
                                                $("#err").text("Maximum File Upload size is 6MB");
                                                $("#submitData").attr('disabled', 'disabled');
                                                $("#file-input").val('');
                                            }else{
                                                $("#err").text(fileType.name);
                                            }
                                        } else {
                                            $("#err").text("File type not allowed");
                                            $("#submitData").attr('disabled', 'disabled');
                                            $("#file-input").val('');
                                        }
                                    } else if (tt == 1) {
                                        if (fileType.type == 'video/mp4' || fileType.type == 'audio/x-m4a' || fileType.type == 'video/3gpp' || fileType.type == 'video/mkv' || fileType.type == 'video/avi' || fileType.type == 'video/flv'){
                                            if (input_file.files[0].size > 6000000) {
                                                $("#err").text("Maximum File Upload size is 6MB");
                                                $("#submitData").attr('disabled', 'disabled');
                                                $("#file-input").val('');
                                            }else{
                                                $("#err").text(fileType.name);
                                            }
                                        }else{
                                            $("#err").text("File type not allowed");
                                            $("#submitData").attr('disabled', 'disabled');
                                            $("#file-input").val('');
                                        }
                                    } else {
                                        $("#err").text("File type not allowed");
                                        $("#submitData").attr('disabled', 'disabled');
                                        $("#file-input").val('');
                                    }
                                };
                                reader.readAsDataURL(input_file.files[0]);
                            }
                        }

</script>
@stop