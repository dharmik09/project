<div class="ad-slider owl-carousel">
    @forelse ($mediumAdImages as $mediumAdImage)
    <div class="ad-v">
        <div class="d-table">
            <img src="{{$mediumAdImage['image']}}">
        </div>
    </div>
    @empty
    <div class="ad-v">
        <div class="t-table">
            <div class="table-cell">
                No Ads available!
            </div>
        </div>
    </div>
    @endforelse
</div>
<div class="ad-slider owl-carousel">
    @forelse ($largeAdImages as $largeAdImage)
    <div class="ad-v-2">
        <div class="d-table">
            <img src="{{$largeAdImage['image']}}">
        </div>
    </div>
    @empty
    <div class="ad-v-2">
        <div class="t-table">
            <div class="table-cell">
                No Ads available!
            </div>
        </div>
    </div>
    @endforelse
</div>