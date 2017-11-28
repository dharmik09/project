<!-- list for uploaded content -->
<ul class="upld_img">    

@if(isset($userLevel4AdvanceDocumentTask) && !empty($userLevel4AdvanceDocumentTask))
<form id="advance_task_review" class="form-horizontal" method="post" action="{{ url('/parent/submitLevel4AdvanceActivityForReview') }}" enctype="multipart/form-data">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="profession_id_review" value="{{ $professionId }}">
<input type="hidden" name="teen_id" value="{{$response['teen_id']}}">
<?php $pendingTask = 0; ?>
    @foreach($userLevel4AdvanceDocumentTask as $key=>$task)
    <?php 
        if(File::exists(public_path($level4AdvanceOriginalImageUploadPath.$task->l4aapa_media_name)) && $task->l4aapa_media_name != '') {
            $image =  url($level4AdvanceOriginalImageUploadPath.'document.png');
            $documentPath = url($level4AdvanceOriginalImageUploadPath.$task->l4aapa_media_name);
        }else{
            $image =  url($level4AdvanceOriginalImageUploadPath.'no_document.png');
            $documentPath = 'javascript:void(0)';
        }
    ?>
    <input type="hidden" name="data_id[]" value="{{$task->id}}">
    <input type="hidden" name="data_status[]" value="{{$task->l4aapa_is_verified}}">
    <input type="hidden" name="data_type" value="2">
    @if($task->l4aapa_is_verified == 0)

    <?php $pendingTask++; $statusArr = array('class'=>'under_progress','text'=>'Uploaded')?> 
    @elseif($task->l4aapa_is_verified == 1)
    <?php $statusArr = array('class'=>'pending','text'=>'Under Review')?> 
    @elseif($task->l4aapa_is_verified == 2)
    <?php $statusArr = array('class'=>'approved','text'=>'Approved')?> 
    @elseif($task->l4aapa_is_verified == 3)
    <?php $statusArr = array('class'=>'rejected','text'=>'Rejected')?>
    @else
    <?php $statusArr = array('class'=>'under_progress','text'=>'Not Submitted')?> 
    @endif
        <li>
            <div class="upd_detail clearfix">
                <div class="img_cont">
                    <div class="img_inner">
                        <img src="{{$image}}">
                        <span class="cst-label {{$statusArr['class']}}">{{$statusArr['text']}}</span>
                    </div>
                </div>
                <div class="detail_cont">
                     <div class="cst_grp">
                    @if(isset($task->l4aapa_earned_points) && $task->l4aapa_earned_points > 0)
                    <span class="points_booster">{{$task->l4aapa_earned_points}}</span>
                    @endif
                  
                    @if($task->l4aapa_is_verified != 2)
                    <span class="btn_dlt"><i class="fa fa-trash-o" aria-hidden="true" onclick="deleteLevel4AdvanceTaskUser({{$task->id}},'{{$task->l4aapa_media_name}}','{{$task->l4aapa_media_type}}');"></i></span>
                    @endif
                     </div>
                    <div class="date">
                        <p class="under_progress_date">Created - {{date('d F Y',strtotime($task->created_at))}}</p>
                        @if($task->l4aapa_is_verified == 2)
                        <p class="approved_date">Approved - {{date('d F Y',strtotime($task->l4aapa_verified_date))}}</p>
                        <p class="approved_date">Approved By - {{$task->adminname}}</p>
                        @elseif($task->l4aapa_is_verified == 3)
                        <p class="rejected_date">Rejected - {{date('d F Y',strtotime($task->l4aapa_verified_date))}}</p>
                        <p class="rejected_date">Rejected By - {{$task->adminname}}</p>
                        @else
                        <p></p>
                        @endif
                    </div>
                    <p class="disciption">{{(isset($task->l4aapa_note) && !empty($task->l4aapa_note)) ? $task->l4aapa_note : ''}}</p>
                    <span class="read_more"><a target="_blank" href="{{$documentPath}}">View Document</a></span>
                </div>
            </div>
        </li>

    @endforeach
    <div class="save_image clearfix">
        @if($pendingTask >0)
        <input type="submit" id="submitForReview" value="Submit" class="btn primary_btn pull-right">
        @endif
    </div>
</form>
@else
<div class="no_data_page">
    <span class="nodata_outer">
        <span class="nodata_middle">
            No document found
        </span>
    </span>
</div>
@endif
</ul>