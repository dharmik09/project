@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{(isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->t_name : ''}} -
        <strong>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</strong>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <?php $pointTable = Config::get('constant.DEFAULT_AWS').$level4AdvanceOriginalImageUploadPath.'point_table.png'; ?>
            <span class="read_more" onclick="viewLargeImage('{{$pointTable}}')">Click to view reference point table</span>
        </div>
        <div class="col-md-9">
            <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/level4AdvanceActivityUserTask') }}">Back</a>
        </div>
        <div class="box-body">
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div>
                    <ul class="nav nav-tabs" id="bestSelectFrom" role="tablist">
                        <li id="3" data-type="Image" class="{{(isset($typeId) && $typeId == 3)?'active':''}}" onclick="setMediaType(3, 'Image')"><a data-toggle="tab" href="#image_tab">Image</a></li>
                        <li id="2" data-type="Document" class="{{(isset($typeId) && $typeId == 2)?'active':''}}" onclick="setMediaType(2, 'Document')"><a data-toggle="tab" href="#document_tab" >Document</a></li>
                        <li id="1" data-type="Video" class="{{(isset($typeId) && $typeId == 1)?'active':''}}" onclick="setMediaType(1, 'Video')"><a data-toggle="tab" href="#video_tab" >Video</a></li>
                    </ul>
              
                </div>
                <div class="tab-content">
                    <div role="tabpanel" id="image_tab" class="tab-pane fade {{(isset($typeId) && $typeId == 3)?'in active':''}}">
                        <div class="box-body">
                            @if(isset($userAllImageTasks) && !empty($userAllImageTasks->toArray()))
                                <form id="advance_task_review" class="form-horizontal" method="post" action="{{ url('/admin/verifyUserAdvanceTask') }}" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="teenager" value="{{(isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->id : 0}}">
                                <input type="hidden" name="profession_id" value="{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->id : 0}}">
                                <input type="hidden" name="typeId" value="{{(isset($typeId) && !empty($typeId)) ? $typeId : 3}}">
                                @foreach($userAllImageTasks as $task)
                                    @if($task->l4aaua_media_name != '' && Storage::disk('s3')->exists($level4AdvanceThumbImageUploadPath.$task->l4aaua_media_name))
                                        <?php
                                            $Originalimage =  Storage::url($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name);
                                            $image =  Storage::url($level4AdvanceThumbImageUploadPath.$task->l4aaua_media_name);
                                        ?>
                                    @else
                                        <?php
                                            $image =  asset($level4AdvanceThumbImageUploadPath.'proteen-logo.png');
                                            $Originalimage =  asset($level4AdvanceThumbImageUploadPath.'proteen-logo.png');
                                        ?>
                                    @endif
                                    <div class='cst_tbl'>
                                      <table class='l4-intermediate-media'>
                                        <tr class="header_text">
                                          <td class="img">
                                              <span class="multi_image_setup">
                                                  <?php if (!empty($image)) { ?>
                                                      <img class="img_view" src="{{$image}}" id="{{$task->id}}" onclick="viewLargeImage('{{$Originalimage}}')"/>
                                                  <?php } ?>
                                              </span>
                                              <span class="read_more" onclick="viewLargeImage('{{$Originalimage}}')">View Image</span>
                                          </td>
                                          <td style="vertical-align: top;">
                                              <div class="col-md-12">
                                                    <div class="col-md-4">
                                                        <strong>Submit date:&nbsp;&nbsp;</strong>{{date('d F Y',strtotime($task->created_at))}}
                                                    </div>
                                                    <div class="col-md-2">
                                                        Point&nbsp;&nbsp;
                                                        <input type="number"  value="{{(isset($task->l4aaua_earned_points) && !empty($task->l4aaua_earned_points)) ? $task->l4aaua_earned_points : 0}}" class="form-control number_btn" name="boosterPoint[{{$task->id}}]">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span style="vertical-align: middle;">Status&nbsp;&nbsp;</span>
                                                      <span class="verify_reject"><input type="radio" value="2" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 2){echo "checked";}  ?>  name="status[{{$task->id}}]" id='verify_{{$task->id}}'><label for='verify_{{$task->id}}'>Verify</label></span>
                                                      <span class="verify_reject"><input type="radio" value="3" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 3){echo "checked";}  ?> name="status[{{$task->id}}]" id='reject_{{$task->id}}'><label for='reject_{{$task->id}}'>Reject</label></span>
                                                    </div>
                                                    <input type="hidden" name="verified_status[{{$task->id}}]" value="{{$task->l4aaua_is_verified}}"/>
                                                    <div class="col-md-3">
                                                        @if($task->l4aaua_is_verified == 2)
                                                            <p class="approved_date">Approved - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                            <p class="approved_date">Approved By - {{$task->adminname}}</p>
                                                        @elseif($task->l4aaua_is_verified == 3)
                                                            <p class="rejected_date">Rejected - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                            <p class="rejected_date">Rejected By - {{$task->adminname}}</p>
                                                        @else
                                                            <p></p>
                                                        @endif
                                                    </div>
                                              </div>
                                              <div class="tab_detail_cont_text_area">
                                                  <textarea placeholder="Enter note here" name="note[{{$task->id}}]">{{(isset($task->l4aaua_note) && !empty($task->l4aaua_note)) ? $task->l4aaua_note : ''}}</textarea>
                                              </div>
                                          </td>
                                          <td class="button_delete" style="vertical-align: bottom;">
                                              <input type="button" style="margin-bottom: 14px;" value="Delete" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aaua_media_name}}','{{$task->l4aaua_media_type}}');">
                                          </td>
                                        </tr>
                                      </table>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                                    </div>
                                @endforeach
                                </form>
                            @else
                                <div>No Images found for this profession</div>
                            @endif
                        </div>
                    </div>
                    <div role="tabpanel" id="document_tab" class="tab-pane fade {{(isset($typeId) && $typeId == 2)?'in active':''}}">
                        @if(isset($userAllDocumentTasks) && !empty($userAllDocumentTasks->toArray()))
                            <form id="advance_task_review" class="form-horizontal" method="post" action="{{ url('/admin/verifyUserAdvanceTask') }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="teenager" value="{{(isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->id : 0}}">
                            <input type="hidden" name="profession_id" value="{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->id : 0}}">
                            <input type="hidden" name="typeId" value="{{(isset($typeId) && !empty($typeId)) ? $typeId : 3}}">
                            @forelse($userAllDocumentTasks as $task)
                              @if($task->l4aaua_media_name != '' && Storage::disk('s3')->exists($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name))
                                  <?php
                                      $image =  Storage::url($level4AdvanceOriginalImageUploadPath.'document.png');
                                      $documentPath = Storage::url($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name);
                                  ?>
                              @else
                                  <?php 
                                      $image =  Storage::url($level4AdvanceOriginalImageUploadPath.'no_document.png');
                                      $documentPath = 'javascript:void(0)';
                                  ?>
                              @endif
                            <div class='cst_tbl'>
                              <table class='l4-intermediate-media'>
                                <tr class="header_text">
                                  <td class="img">
                                      <span class="multi_image_setup">
                                            <?php if (!empty($image)) { ?>
                                            <img class="img_view" src="{{$image}}" id="{{$task->id}}"/>
                                            <?php } ?>
                                     </span>
                                       <a href="{{$documentPath}}">View Document</a>
                                  </td>
                                  <td style="vertical-align: top;">
                                      <div class="col-md-12">
                                            <div class="col-md-4">
                                                <strong>Submit date:&nbsp;&nbsp;</strong>{{date('d F Y',strtotime($task->created_at))}}
                                            </div>
                                            <div class="col-md-2">
                                                Point&nbsp;&nbsp;
                                                <input type="number" value="{{(isset($task->l4aaua_earned_points) && !empty($task->l4aaua_earned_points)) ? $task->l4aaua_earned_points : 0}}" class="form-control number_btn" name="boosterPoint[{{$task->id}}]">
                                            </div>
                                            <div class="col-md-3">
                                                <span style="vertical-align: middle;">Status&nbsp;&nbsp;</span>
                                              <span class="verify_reject"><input type="radio" value="2" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 2){echo "checked";}  ?>  name="status[{{$task->id}}]" id='verify_{{$task->id}}'><label for='verify_{{$task->id}}'>Verify</label></span>
                                              <span class="verify_reject"><input type="radio" value="3" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 3){echo "checked";}  ?> name="status[{{$task->id}}]" id='reject_{{$task->id}}'><label for='reject_{{$task->id}}'>Reject</label></span>
                                            </div>
                                            <input type="hidden" name="verified_status[{{$task->id}}]" value="{{$task->l4aaua_is_verified}}"/>
                                            <div class="col-md-3">
                                                @if($task->l4aaua_is_verified == 2)
                                                    <p class="approved_date">Approved - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                    <p class="approved_date">Approved By - {{$task->adminname}}</p>
                                                @elseif($task->l4aaua_is_verified == 3)
                                                    <p class="rejected_date">Rejected - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                    <p class="rejected_date">Rejected By - {{$task->adminname}}</p>
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                      </div>
                                      <div class="tab_detail_cont_text_area">
                                          <textarea placeholder="Enter note here" name="note[{{$task->id}}]">{{(isset($task->l4aaua_note) && !empty($task->l4aaua_note)) ? $task->l4aaua_note : ''}}</textarea>
                                      </div>
                                  </td>

                                  <td class="button_delete" style="vertical-align: bottom;">
                                      <input type="button" style="margin-bottom: 14px;" value="Delete" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aaua_media_name}}','{{$task->l4aaua_media_type}}');">
                                  </td>
                                </tr>
                              </table>
                            </div>
                            @endforeach
                            <div class="box-footer">
                                <?php
                                    $disable = '';
                                    if (isset($userAllDocumentTasks[0]->l4aaua_is_verified) && $userAllDocumentTasks[0]->l4aaua_is_verified != 1) { $disable = 'disabled'; } ?>
                                <button type="submit" class="btn btn-primary btn-flat" <?php echo $disable; ?>>{{trans('labels.savebtn')}}</button>
                            </div>
                            </form>
                            @else
                                <div>No Document found for this profession</div>
                            @endif

                    </div>
                    <div role="tabpanel" id="video_tab" class="tab-pane fade {{(isset($typeId) && $typeId == 1)?'in active':''}}">
                       @if(isset($userAllVideoTasks) && !empty($userAllVideoTasks->toArray()))
                            <form id="advance_task_review" class="form-horizontal" method="post" action="{{ url('/admin/verifyUserAdvanceTask') }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="teenager" value="{{(isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->id : 0}}">
                            <input type="hidden" name="profession_id" value="{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->id : 0}}">
                            <input type="hidden" name="typeId" value="{{(isset($typeId) && !empty($typeId)) ? $typeId : 3}}">
                            @forelse($userAllVideoTasks as $task)
                              @if($task->l4aaua_media_name != '' && Storage::disk('s3')->exists($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name))
                                  <?php 
                                      $image =  Storage::url($level4AdvanceOriginalImageUploadPath.'video.png');
                                      $videoPath = Storage::url($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name);
                                  ?>
                              @else
                                  <?php
                                      $image = Storage::url($level4AdvanceOriginalImageUploadPath.'no-video.png');
                                      $videoPath = 'javascript:void(0)';
                                  ?>
                              @endif
                            <div class='cst_tbl'>
                              <table class='l4-intermediate-media'>
                                <tr class="header_text">
                                  <td class="img">
                                      <span class="multi_image_setup">
                                            <?php if (!empty($image)) { ?>
                                            <img class="img_view" src="{{$image}}" id="{{$task->id}}"/>
                                            <?php } ?>
                                     </span>
                                       <a href="{{$videoPath}}">View Video</a>
                                  </td>
                                  <td style="vertical-align: top;">
                                      <div class="col-md-12">
                                            <div class="col-md-4">
                                                <strong>Submit date:&nbsp;&nbsp;</strong>{{date('d F Y',strtotime($task->created_at))}}
                                            </div>
                                            <div class="col-md-2">
                                                Point&nbsp;&nbsp;
                                                     <input type="number" value="{{(isset($task->l4aaua_earned_points) && !empty($task->l4aaua_earned_points)) ? $task->l4aaua_earned_points : 0}}" class="form-control number_btn" name="boosterPoint[{{$task->id}}]">
                                            </div>
                                            <div class="col-md-3">
                                                <span style="vertical-align: middle;">Status&nbsp;&nbsp;</span>
                                              <span class="verify_reject"><input type="radio" value="2" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 2){echo "checked";}  ?>  name="status[{{$task->id}}]" id='verify_{{$task->id}}'><label for='verify_{{$task->id}}'>Verify</label></span>
                                              <span class="verify_reject"><input type="radio" value="3" <?php if(isset($task->l4aaua_is_verified) && $task->l4aaua_is_verified == 3){echo "checked";}  ?> name="status[{{$task->id}}]" id='reject_{{$task->id}}'><label for='reject_{{$task->id}}'>Reject</label></span>
                                            </div>
                                            <input type="hidden" name="verified_status[{{$task->id}}]" value="{{$task->l4aaua_is_verified}}"/>
                                            <div class="col-md-3">
                                                @if($task->l4aaua_is_verified == 2)
                                                    <p class="approved_date">Approved - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                    <p class="approved_date">Approved By - {{$task->adminname}}</p>
                                                @elseif($task->l4aaua_is_verified == 3)
                                                    <p class="rejected_date">Rejected - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                                    <p class="rejected_date">Rejected By - {{$task->adminname}}</p>
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                      </div>
                                      <div class="tab_detail_cont_text_area">
                                          <textarea placeholder="Enter note here" name="note[{{$task->id}}]">{{(isset($task->l4aaua_note) && !empty($task->l4aaua_note)) ? $task->l4aaua_note : ''}}</textarea>
                                      </div>
                                  </td>

                                  <td class="button_delete" style="vertical-align: bottom;">
                                      <input type="button" style="margin-bottom: 14px;" value="Delete" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aaua_media_name}}','{{$task->l4aaua_media_type}}');">
                                  </td>
                                </tr>
                              </table>
                            </div>
                            @endforeach
                            <div class="box-footer">
                                <?php
                                    $disable = '';
                                    if (isset($userAllVideoTasks[0]->l4aaua_is_verified) && $userAllVideoTasks[0]->l4aaua_is_verified != 1) { $disable = 'disabled'; } ?>
                                <button type="submit" class="btn btn-primary btn-flat" <?php echo $disable;?>>{{trans('labels.savebtn')}}</button>
                            </div>
                            </form>
                            @else
                            <div>No Video found for this profession</div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="uploaded_content" class="modal fade hint_image_modal_show" role="dialog">
    <div class="modal-dialog viewlargeimage">
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
   function viewLargeImage(originalImage)
   {
        $('#uploaded_content').modal('show');
        $('#uploaded_content img').attr('src', originalImage);
   }
   function deleteLevel4AdvanceTaskUser(media_id,media_name,media_type)
    {
        res = confirm('Are you sure you want to delete this record?');
        if(res){
        $.ajax({
            url: "{{ url('admin/deleteUserAdvanceTask') }}",
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
        }else{
            return false;
        }
    }
    function setMediaType(dataval, datatype) {
        var fullurl = document.URL;
        $('#media_type').val(dataval);
        $('#media_name_image_tag').text(datatype);
        var str = fullurl;
        var i = str.lastIndexOf('/');
        if (i != -1) {
            str = str.substr(0, i) + "/" + dataval;
        }
        location.replace(str);
    }
</script>

@stop