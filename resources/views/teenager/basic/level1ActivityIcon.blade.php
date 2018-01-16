@if(isset($iconCategoryName) && !empty($iconCategoryName) && count($iconCategoryName) > 0)
<div class="row">
    @foreach($iconCategoryName as $iconKey => $iconSet)
        <div class="col-sm-3 col-xs-4">
            <input class="icon-radio part2IconCheck" type="radio" name="category_id" value="{{ $iconSet->id }}" id="category_id{{$iconKey}}">
            <label class="radio_img">
                <span class="icn">
                    <img src="{{ $iconSet->image }}" alt="{{ $iconSet->name }}">
                    <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                </span>
                <span class="title">{{ $iconSet->name }}</span>
            </label>
        </div>
    @endforeach
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div style="text-align:center;margin: 10px 0;">
                @if (isset($iconCategoryName) && !empty($iconCategoryName))
                    <?php echo $iconCategoryName->appends(['categoryId' => $categoryId, 'categoryType' => $data_cat_type, 'searchText' => $textName])->render(); ?>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-sm-12 col-md-12">
        No Icon Found
    </div>
</div>
@endif