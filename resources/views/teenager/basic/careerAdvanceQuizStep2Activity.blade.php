<div class="upload-screen quiz-box sec-show cost-estimator">
    @if(isset($professionDetail) && !empty($professionDetail))
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home">Image</a></li>
        <li><a data-toggle="tab" href="#submenu1">Document</a></li>
        <li><a data-toggle="tab" href="#submenu2">Video</a></li>
    </ul>
    <div class="tab-content">
        <div id="home" class="tab-pane fade in active">
            <h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
            <span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
            <div class="upload-img" id="img-preview">
                <span>photo upload</span>
                <input type="file" name="pic" accept="image/*" onchange="readURL(this);">
            </div>
            <button class="btn-primary" type="submit" title="Submit">Submit</button>
            <div class="upload-content">
                <div class="no-data">
                    <div class="nodata-middle">
                        No Image found
                    </div>
                </div>
            </div>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress under-review">Under Review</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="btn-dlt"><i class="fa fa-trash"></i></span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <span class="view-img">View Image</span>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress approved">Approved</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="pnt-get">150</span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <p class="approved-date">Approved - 02 February 2018</p>
                                    <p class="approved-by">Approved by - Admin User</p>
                                    <span class="view-img">View Image</span>
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
        </div>

        <div id="submenu1" class="tab-pane fade">
            <h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
            <span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
            <div class="upload-img" id="img-preview">
                <span>Document upload</span>
                <input type="file" name="pic" accept="application/*" onchange="readURL(this);">
            </div>
            <button class="btn-primary" type="submit" title="Submit">Submit</button>
            <div class="upload-content">
                <div class="no-data">
                    <div class="nodata-middle">
                        No Documents found
                    </div>
                </div>
            </div>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress under-review">Under Review</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="btn-dlt"><i class="fa fa-trash"></i></span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <span class="view-img">View Image</span>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress approved">Approved</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="pnt-get">150</span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <p class="approved-date">Approved - 02 February 2018</p>
                                    <p class="approved-by">Approved by - Admin User</p>
                                    <span class="view-img">View Image</span>
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
        </div>
        <div id="submenu2" class="tab-pane fade">
            <h4>{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : ''}}</h4>
            <span class="pull-right close" onclick="getMediaUploadSection();"><i class="icon-close"></i></span>
            <div class="upload-img" id="img-preview">
                <span>Video upload</span>
                <input type="file" name="pic" accept="video/*" onchange="readURL(this);">
            </div>
            <button class="btn-primary" type="submit" title="Submit">Submit</button>
            <div class="upload-content">
                <div class="no-data">
                    <div class="nodata-middle">
                        No videos found
                    </div>
                </div>
            </div>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress under-review">Under Review</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="btn-dlt"><i class="fa fa-trash"></i></span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <span class="view-img">View Image</span>
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
                    <li>
                        <div class="upd-detail clearfix img-type">
                            <div class="img-cont">
                                <div class="img-inner">
                                    <img src="img/agriculture-equipment.jpg" alt="Proteen" class="myImg">
                                    <span class="under-progress approved">Approved</span>
                                </div>
                            </div>
                            <div class="detail-cont">
                                <div class="cst-grp">
                                    <span class="pnt-get">150</span>
                                </div>
                                <div class="date">
                                    <p class="under-progress-date">Created - 02 February 2018</p>
                                    <p class="approved-date">Approved - 02 February 2018</p>
                                    <p class="approved-by">Approved by - Admin User</p>
                                    <span class="view-img">View Image</span>
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
        </div>
    </div>
    @else
    @endif
</div>