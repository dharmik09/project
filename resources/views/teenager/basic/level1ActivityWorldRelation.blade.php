<div class="my-world">
    <h3>My World</h3>
    <div class="sec-filter clearfix">
        <div class="col-md-offset-1 col-md-10 col-sm-offset-1 col-sm-10 col-xs-12">
            <div class="form-group custom-select">
                <select tabindex="8" class="form-control icon_selection_select" onChange="getWorldData(this.value);">
                    <option value="">Select Category</option>
                    <option value="1">Self</option>
                    <option value="2">Relation</option>
                </select>
             </div>
        </div>
        <div id="relation_data">
            <form class="clearfix" id="relationWorld">
                {{ csrf_field() }}
                <input type="hidden" name="categoryType" value="3">
                <h4>Please upload a photo</h4>
                <div class="upload-img" id="custom-img">
                    <span><i class="icon-plus"></i></span>
                    <input type='file' id="img-upload" name="relative_image" class="upload_button" accept=".png, .jpg, .jpeg, .bmp" onchange="readIconURL(this);"/>
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
                    <button type="submit" class="btn btn-primary" id="myWorldNext" title="Next">Next</button>
                </div>
            </form>
        </div>
        <div id="self_data" style="display:none">
            <input type="hidden" name="categoryType" value="3">
            <h4>Please upload a photo</h4>
            <div class="upload-img" id="custom-img">
                <span><i class="icon-plus"></i></span>
                <input type="file" name="pic" accept="image/*" onchange="readURL(this);">
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" tabindex="1">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Nickname" tabindex="2">
                </div>
            </div>
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" id="myWorldNext2" title="Next">Next</button>
            </div>
        </div>
    </div>
</div>
