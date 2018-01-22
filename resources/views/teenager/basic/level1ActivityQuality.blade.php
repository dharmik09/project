<form id="level1ActivityPart2" action="{{url('/teenager/save-first-level-icon-quality')}}" onsubmit="return checkQualityData()" method="post" enctype="multipart/form-data" >
    {{csrf_field()}}
    <div class="qualities-sec">
        <div class="select-qualities">
            <div class="icon-img">
                <img src="{{$data['icon_image']}}" alt="{{$data['icon_name']}}">
            </div>
            <div class="icon-name">
                <p>Select Qualities For {{$data['icon_name']}}</p>
            </div>
        </div>
        <div class="row flex-container">
            @if(isset($response['qualityList']) && count($response['qualityList']) > 0)
                @foreach($response['qualityList'] as $key => $qualityValue)
                    <div class="col-md-4 col-sm-6 col-xs-6 flex-items">
                        <div class="ck-button">
                            <label>
                                <input class="iconCheck" type="checkbox" value="{{$qualityValue['id']}}" id="icon[{{$qualityValue['id']}}]" name="icon[{{$qualityValue['id']}}]">
                                <span>{{$qualityValue['quality']}}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            @endif
            <input type="hidden" name="category_type" value="{{ $categoryType }}">
            @if($categoryType == "2" || $categoryType == "1")
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
            @elseif($categoryType == "3")
                <input type="hidden" name="relation_category" value="{{ $relation_category }}">
                <input type="hidden" name="relation_id" value="{{ $lastInterIdRelation }}">
            @elseif($categoryType == "4")
                <input type="hidden" name="self_id" value="{{ $lastInterIdSelf }}">
            @endif
        </div>
    </div>
    <div class="form-btn">
        <span class="icon"><i class="icon-arrow-spring"></i></span>
        <br/>
        <button type="submit" title="Submit" class="btn btn-primary">Submit</button>
    </div>
</form>