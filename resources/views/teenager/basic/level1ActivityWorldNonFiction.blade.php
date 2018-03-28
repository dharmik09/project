<div class="real-world-survey clearfix fictional-world">
    <div class="selection-container">
        <form id="level1ActivityWorldForm" action="{{ url('teenager/save-first-level-icon-category') }}" method="post">
            {{csrf_field()}}
            <div class="row sec-filter">
                <div class="col-sm-4">
                    <div class="icon-slider owl-carousel" id="icon-slider">
                        @if(isset($mainArray['topTrendingImages'][0]))
                            @foreach($mainArray['topTrendingImages'] as $trendingImages)
                                <div class="icon-item">
                                    <div class="icon-content">
                                        <img src="{{$trendingImages['image']}}" alt="{{$trendingImages['name']}}">
                                        <span class="rank-i">{{$trendingImages['rank']}}</span>
                                        <div class="character-name"><span>{{$trendingImages['name']}}</span></div>
                                    </div>
                                    <span class="rank">Votes : {{$trendingImages['votes']}}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="icon-item">
                                <div class="icon-content">
                                    <img src="{{ Storage::url('img/proteen-logo.png') }}" alt="Icon image">
                                    <div class="character-name"><span>Top Trending Icon</span></div>
                                </div>
                                <span class="rank">Vote More</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group custom-select">
                                <select tabindex="8" class="form-control" name="categoryId" id="categoryIdValue" onChange="getIconName(this.value, '2', 1, '')" data-category-type="2">
                                    @if(isset($mainhumanIconCategoryArray) && $mainhumanIconCategoryArray)
                                        <option value="">Select Category</option>
                                        @foreach($mainhumanIconCategoryArray as $mainIconArray)
                                            <option value="{{ $mainIconArray['id'] }}">{{$mainIconArray['name']}}</option>
                                        @endforeach
                                    @else
                                        <option value="">Select Category</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" class="form-control add-icon" data-toggle="modal" data-target="#fiction_modal_icon">Add ICON <span class="micro_icon"><i class="icon-plus"></i></span></a>
                            </div>
                        </div>
                        <div class="col-sm-12 searchOnIcon" style="display:none">
                            <div class="form-group search-bar clearfix">
                                <input type="text" placeholder="search" tabindex="1" class="form-control search-feild" id="searchForIcon" onkeyup="getIconName('4', '2', 1, this.value)">
                                <button type="submit" class="btn-search"><i class="icon-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="icon-container">
                <div class="sec-forum no_selected_category">
                    <span>Please select one category</span>
                </div>
                <div class="set-icon-selection loaderSection">
                    <div style="display: block;" class="loading-screen-data loading-wrapper-sub">                        
                        <div class="loading-content"></div>
                    </div>
                    <div class="icon-container-inner selected_category" style="display:none">
                        
                    </div>
                </div>
                <div class="form-btn">
                    <span class="icon"><i class="icon-arrow-spring"></i></span>
                    <br/>
                    <button type="submit" title="Next" class="btn btn-primary" id="nextSubmit">Next</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade custom-select" id="fiction_modal_icon" role="dialog">
    <form method="post" action="{{ url('/teenager/add-icon-category') }}" enctype="multipart/form-data" id="fictionForm">
        {{csrf_field()}}
        <input type="hidden" name="categoryType" value="2"/>
        <div class="modal-dialog">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <h4 class="modal-title errorGoneMsgPopup"></h4>
                </div>
                <div class="modal-body">
                    <div class="sec-filter">
                        
                        <div class="form-group custom-select">
                            <select tabindex="8" class="form-control" data-category-type="2" id="categoryName1" name="categoryId">
                                @if(isset($mainhumanIconCategoryArray) && $mainhumanIconCategoryArray)
                                    <option value="">Select Category</option>
                                    @foreach($mainhumanIconCategoryArray as $mainIconArray)
                                        <option value="{{ $mainIconArray['id'] }}">{{$mainIconArray['name']}}</option>
                                    @endforeach
                                @else
                                    <option value="">Select Category</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Name *" id="characterName1" name="characterName">
                        </div>
                        <div class="upload-img profile-img" id="img-upload">
                            <span><i class="icon-plus"></i></span>
                            <input type="file" id="icon_image" name="image" accept="image/*" onchange="readIconURL(this, '#img-upload');">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" name="fiction" value="Save" class="btn btn-primary" id="fictionSave" onClick="checkIconUploadData()">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>