<h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
<span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
@if(count($userLevel4AdvanceImageTask) >= Config::get('constant.DEFAULT_TOTAL_ADVANCE_IMAGE_COUNT') )
    <div class="upload-content">
        <div class="no-data">
            <div class="nodata-middle">
                You reached maximum image upload limit. Total uploaded images are {{ count($userLevel4AdvanceImageTask) }}!
            </div>
        </div>
    </div>
@else
    <form id="add_advance_task" class="add_advance_task">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="profession_id" value="{{ $professionId }}">
        <input type="hidden" name="media_type" id="media_type" value="3">
        <div class="upload-img" id="img-preview">
            <span>photo upload</span>
            <input type="file" name="media" accept="image/*" onchange="readURL(this);">
        </div>
        <div id="mediaErr" class="photo-error-register"></div>
        <button id="taskSave" class="btn-primary btn-default" title="Submit" type="submit">Upload</button>
    </form>
@endif
<span>You can upload maximum {{ Config::get('constant.DEFAULT_TOTAL_ADVANCE_IMAGE_COUNT') }} images.</span>
@if(isset($userLevel4AdvanceImageTask) && count($userLevel4AdvanceImageTask) > 0)
<div class="upload-content">
    <ul class="upld-img">
        <form id="advance_task_review" class="advance_task_review" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="profession_id_review" value="{{ $professionId }}">
            <?php $pendingTask = 0; ?>
            @foreach($userLevel4AdvanceImageTask as $key=>$task)
            <?php 
                if(Storage::size($level4AdvanceThumbImageUploadPath.$task->l4aapa_media_name) > 0 && $task->l4aapa_media_name != '') { 
                    $media =  Storage::url($level4AdvanceThumbImageUploadPath.$task->l4aapa_media_name);
                    $mediaPath = Storage::url($level4AdvanceThumbImageUploadPath.$task->l4aapa_media_name);
                }else{
                    $media =  Storage::url($level4AdvanceThumbImageUploadPath.'proteen-logo.png');
                    $mediaPath = 'javascript:void(0)';
                }
            ?>
            <input type="hidden" name="data_id[]" value="{{$task->id}}">
            <input type="hidden" name="data_status[]" value="{{$task->l4aapa_is_verified}}">
            <input type="hidden" name="data_type" value="2">
            @if($task->l4aapa_is_verified == 0)

            <?php $pendingTask++; $statusArr = array('class'=>'under-progress','text'=>'Uploaded')?> 
            @elseif($task->l4aapa_is_verified == 1)
            <?php $statusArr = array('class'=>'under-progress under-review','text'=>'Under Review')?> 
            @elseif($task->l4aapa_is_verified == 2)
            <?php $statusArr = array('class'=>'under-progress approved','text'=>'Approved')?> 
            @elseif($task->l4aapa_is_verified == 3)
            <?php $statusArr = array('class'=>'under-progress rejected','text'=>'Rejected')?> 
            @else
            <?php $statusArr = array('class'=>'under-progress','text'=>'Not Submitted')?> 
            @endif
            <li>
                <div class="upd-detail clearfix img-type">
                    <div class="img-cont">
                        <div class="img-inner">
                            <img src="{{$media}}" alt="Proteen" class="myImg l4advance{{$task->id}}" title="Click to enlarge">
                            <span class="{{$statusArr['class']}}">{{$statusArr['text']}}</span>
                        </div>
                    </div>
                    <div class="detail-cont">
                        <div class="cst-grp">
                            @if($task->l4aapa_is_verified != 2)
                                <span class="btn-dlt"><i class="fa fa-trash" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aapa_media_name}}','{{$task->l4aapa_media_type}}');"></i></span>
                            @endif
                            @if(isset($task->l4aapa_earned_points) && $task->l4aapa_earned_points > 0)
                                <span class="pnt-get">{{$task->l4aapa_earned_points}}</span>
                            @endif
                        </div>
                        <div class="date">
                            <p class="under-progress-date">Created - {{date('d F Y',strtotime($task->created_at))}}</p>
                            @if($task->l4aapa_is_verified == 2)
                                <p class="approved-date">Approved - {{date('d F Y',strtotime($task->l4aapa_verified_date))}}</p>
                                <p class="approved-date">Approved By - {{$task->adminname}}</p>
                            @elseif($task->l4aapa_is_verified == 3)
                                <p class="rejected-date">Rejected - {{date('d F Y',strtotime($task->l4aapa_verified_date))}}</p>
                                <p class="rejected-date">Rejected By - {{$task->adminname}}</p>
                            @else
                                <p></p>
                            @endif
                            <p>{{(isset($task->l4aapa_note) && !empty($task->l4aapa_note)) ? $task->l4aapa_note : ''}}</p>
                            <span class="view-img"><a target="_blank" onclick="viewImage({{$task->id}});" href="javascript:void(0)">View Image</a></span>
                        </div>
                        <!-- The Modal -->
                        <div id="l4advanceImage" class="modal">
                            <span class="close-modal">&times;</span>
                            <img class="modal-content" id="img01">
                            <div id="caption"></div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
            @if($pendingTask >0)
            <div class="media-submit">
                <button id="mediaSubmit" class="btn-primary btn-default" type="submit" title="Submit">Submit</button>
            </div>
            @endif
        </form>
    </ul>
</div>
@else
<div class="upload-content">
    <div class="no-data">
        <div class="nodata-middle">
            No Image found
        </div>
    </div>
</div>
@endif