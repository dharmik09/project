<h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
<span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
<div class="upload-img" id="img-preview">
    <span>photo upload</span>
    <input type="file" name="pic" accept="image/*" onchange="readURL(this);">
</div>
<div id="imgErr" class="photo-error-register"></div>
<button id="taskSave" class="btn-primary" title="Submit">Submit</button>
@if(isset($userLevel4AdvanceImageTask) && count($userLevel4AdvanceImageTask) > 0)
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
            No Image found
        </div>
    </div>
</div>
@endif