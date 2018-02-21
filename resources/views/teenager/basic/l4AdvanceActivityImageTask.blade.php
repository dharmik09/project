<h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
<span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
<form id="add_advance_task" class="add_advance_task">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="profession_id" value="{{ $professionId }}">
    <input type="hidden" name="media_type" id="media_type" value="3">
    <div class="upload-img" id="img-preview">
        <span>photo upload</span>
        <input type="file" name="media" accept="image/*" onchange="readURL(this);">
    </div>
    <div id="mediaErr" class="photo-error-register"></div>
    <button id="taskSave" class="btn-primary" title="Submit" type="submit">Submit</button>
</form>
@if(isset($userLevel4AdvanceImageTask) && count($userLevel4AdvanceImageTask) > 0)
<div class="upload-content">
    <ul class="upld-img">
        <?php $pendingTask = 0; ?>
        @foreach($userLevel4AdvanceImageTask as $key=>$task)
        <?php 
            if(File::exists(public_path($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name)) && $task->l4aaua_media_name != '') { 
                $media =  url($level4AdvanceOriginalImageUploadPath.'document.png');
                $mediaPath = url($level4AdvanceOriginalImageUploadPath.$task->l4aaua_media_name);
            }else{
                $media =  url($level4AdvanceOriginalImageUploadPath.'no_document.png');
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
                        <span class="view-img"><a target="_blank" href="{{$mediaPath}}">View Image</a></span>
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