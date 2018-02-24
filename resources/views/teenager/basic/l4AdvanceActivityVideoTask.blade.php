<h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
<span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
<form id="add_advance_task" class="add_advance_task">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="profession_id" value="{{ $professionId }}">
    <input type="hidden" name="media_type" id="media_type" value="1">
    <div class="upload-img" id="img-preview">
        <span>Video upload</span>
        <input type="file" name="media" accept="video/*" onchange="readURL(this);">
    </div>
    <div id="mediaErr" class="photo-error-register"></div>
    <button id="taskSave" class="btn-primary btn-default" title="Submit" type="submit">Submit</button>
</form>
@if(isset($userLevel4AdvanceVideoTask) && count($userLevel4AdvanceVideoTask) > 0)
<div class="upload-content">
    <ul class="upld-img">
        <form id="advance_task_review" class="advance_task_review" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="profession_id_review" value="{{ $professionId }}">
            <?php $pendingTask = 0; ?>
            @foreach($userLevel4AdvanceVideoTask as $key=>$task)
            <?php 
                if(Storage::size($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name) > 0 && $task->l4aaua_media_name != '') { 
                    $media = Storage::url($level4AdvanceOriginalImageUploadPath.'video.png');
                    $mediaPath = Storage::url($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name);
                }else{
                    $media = Storage::url($level4AdvanceOriginalImageUploadPath.'no-video.jpg');
                    $mediaPath = 'javascript:void(0)';
                }
            ?>
            <input type="hidden" name="data_id[]" value="{{$task->id}}">
            <input type="hidden" name="data_status[]" value="{{$task->l4aaua_is_verified}}">
            <input type="hidden" name="data_type" value="2">
            @if($task->l4aaua_is_verified == 0)

            <?php $pendingTask++; $statusArr = array('class'=>'under-progress','text'=>'Uploaded')?> 
            @elseif($task->l4aaua_is_verified == 1)
            <?php $statusArr = array('class'=>'under-progress under-review','text'=>'Under Review')?> 
            @elseif($task->l4aaua_is_verified == 2)
            <?php $statusArr = array('class'=>'under-progress approved','text'=>'Approved')?> 
            @elseif($task->l4aaua_is_verified == 3)
            <?php $statusArr = array('class'=>'under-progress rejected','text'=>'Rejected')?> 
            @else
            <?php $statusArr = array('class'=>'under-progress','text'=>'Not Submitted')?> 
            @endif
            <li>
                <div class="upd-detail clearfix img-type">
                    <div class="img-cont">
                        <div class="img-inner">
                            <img src="{{$media}}" alt="Proteen" class="myImg" title="Click to enlarge">
                            <span class="{{$statusArr['class']}}">{{$statusArr['text']}}</span>
                        </div>
                    </div>
                    <div class="detail-cont">
                        <div class="cst-grp">
                             @if($task->l4aaua_is_verified != 2)
                                <span class="btn-dlt"><i class="fa fa-trash" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aaua_media_name}}','{{$task->l4aaua_media_type}}');"></i></span>
                            @endif
                            @if(isset($task->l4aaua_earned_points) && $task->l4aaua_earned_points > 0)
                                <span class="pnt-get">{{$task->l4aaua_earned_points}}</span>
                            @endif
                        </div>
                        <div class="date">
                            <p class="under-progress-date">Created - {{date('d F Y',strtotime($task->created_at))}}</p>
                            @if($task->l4aaua_is_verified == 2)
                                <p class="approved-date">Approved - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                <p class="approved-date">Approved By - {{$task->adminname}}</p>
                            @elseif($task->l4aaua_is_verified == 3)
                                <p class="rejected-date">Rejected - {{date('d F Y',strtotime($task->l4aaua_verified_date))}}</p>
                                <p class="rejected-date">Rejected By - {{$task->adminname}}</p>
                            @else
                                <p></p>
                            @endif
                            <p>{{(isset($task->l4aaua_note) && !empty($task->l4aaua_note)) ? $task->l4aaua_note : ''}}</p>
                            <span class="view-img"><a target="_blank" href="{{$mediaPath}}">View Video</a></span>
                        </div>
                        <!-- The Modal -->
                        <div id="myModal" class="modal">
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
            No videos found
        </div>
    </div>
</div>
@endif