<?php
    $getAuthUserData = Helpers::getAuthUserData(Auth::guard('teenager')->user()->id);
    $profilePicUrl = ($getAuthUserData->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $getAuthUserData->t_photo) > 0 ) ? Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $getAuthUserData->t_photo) : Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");;
    $profilePicName = ($getAuthUserData->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $getAuthUserData->t_photo) > 0 ) ? $getAuthUserData->t_photo : "proteen-logo.png";
?>
<div class="my-world">
    <h3>My World</h3>
    <div class="sec-filter clearfix">
        <div class="col-md-offset-1 col-md-10 col-sm-offset-1 col-sm-10 col-xs-12">
            <div class="form-group custom-select">
                <select tabindex="8" class="form-control icon_selection_select" onChange="getWorldData(this.value);">
                    <option value="2" @if(isset($type) && $type == 3) selected="selected" @endif>Relation</option>
                    <option value="1" @if(isset($type) && $type == 4) selected="selected" @endif>Self</option>
                </select>
             </div>
        </div>
        <div class="loaderSection" id="relation_data" @if(isset($type) && $type == 4) style="display:none" @endif>
            <div style="display: block;" class="loading-screen-data loading-wrapper-sub">                
                <div class="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div>
            </div>
            <form class="clearfix" id="relationWorld">
                {{ csrf_field() }}
                <input type="hidden" name="categoryType" value="3">
                <h4>Please upload a photo</h4>
                <div class="upload-img profile-img" id="custom-img">
                    <span><i class="icon-plus"></i></span>
                    <input type='file' id="img-upload" name="relative_image" class="upload_button" accept=".png, .jpg, .jpeg, .bmp" onchange="readIconURL(this, '#custom-img');"/>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <select tabindex="8" class="form-control" name="relations_category" id="icon_category_3">
                            <option value="">Select Category</option>
                            @foreach($mainRelationArray as $relationsCategoryList)
                                <option value="{{$relationsCategoryList['id']}}">{{$relationsCategoryList['name']}}</option>>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" id="relations_name" name="relations_name" class="form-control" placeholder="Please enter name" tabindex="2" />
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary myWorldNext" id="myWorldNext" title="Next">Next</button>
                </div>
            </form>
        </div>
        <div class="loaderSection" id="self_data" @if(isset($type) && $type == 3) style="display:none" @endif  @if(!isset($type)) style="display:none" @endif>
            <div style="display: block;" class="loading-screen-data loading-wrapper-sub">                
                <div class="loading-content"></div>
            </div>
            <form class="clearfix" id="myOwnWorld">
                <input type="hidden" name="categoryType" value="4">
                <input type='hidden' name="hidden_self_image" value="{{$profilePicName or ''}}" />
                <h4>Please upload a photo</h4>
                <div class="upload-img profile-img" id="custom-img-new" @if($profilePicUrl != "") style='background-image: url({{$profilePicUrl}})' @endif>
                    <span><i class="icon-plus"></i></span>
                    <input type='file' id="img-upload2" name="self_image" class="upload_button upload-img img-upload" accept=".png, .jpg, .jpeg, .bmp" onchange="readIconURL(this, '#custom-img-new');"/>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" id="teen_firstname" class="form-control" name="teen_name" placeholder="Please enter first name" value="{{$getAuthUserData->t_name}}" required>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" name="teen_lastname" id="lastname" class="form-control" placeholder="Please enter last name" value="{{ $getAuthUserData->t_lastname }}">
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary myWorldNext" id="myWorldNextOwn" title="Next">Next</button>
                </div>
            </form>
        </div>
    </div>
</div>
