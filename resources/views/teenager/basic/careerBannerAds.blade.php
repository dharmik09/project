<div class="ad-slider owl-carousel">
    @forelse ($bannerAdImages as $bannerAdImage)
    <div class="ad-sec-h">
        <div class="d-table">
            <img src="{{$bannerAdImage['image']}}">
        </div>
    </div>
    @empty
    <div class="ad-sec-h">
        <div class="t-table">
            <div class="table-cell">
                No Ads available!
            </div>
        </div>
    </div>
    @endforelse
</div>