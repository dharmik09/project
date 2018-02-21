<h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
<span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
<form id="add_advance_task" class="add_advance_task">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="profession_id" value="{{ $professionId }}">
    <input type="hidden" name="media_type" id="media_type" value="2">
    <div class="upload-img" id="img-preview">
        <span>Document upload</span>
        <input type="file" name="media" accept="application/*" onchange="readURL(this);">
    </div>
    <div id="mediaErr" class="photo-error-register"></div>
    <button id="taskSave" class="btn-primary" title="Submit" type="submit">Submit</button>
</form>
@if(isset($userLevel4AdvanceDocumentTask) && count($userLevel4AdvanceDocumentTask) > 0)
<div class="upload-content">
    <ul class="upld-img">
        <li>
            <div class="upd-detail clearfix img-type">
                <div class="img-cont">
                    <div class="img-inner">
                        <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg" title="Click to enlarge">
                        <span class="under-progress">Uploaded</span>
                    </div>
                </div>
                <div class="detail-cont">
                    <div class="cst-grp">
                        <span class="btn-dlt"><i class="fa fa-trash"></i></span>
                    </div>
                    <div class="date">
                        <p class="under-progress-date">Created - 02 February 2018</p>
                        <!--<span class="view-img">View Image</span>-->
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
    </ul>
</div>
@else
<div class="upload-content">
    <div class="no-data">
        <div class="nodata-middle">
            No Documents found
        </div>
    </div>
</div>
@endif