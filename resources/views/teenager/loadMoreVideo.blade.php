<div class="masonary-grid">
    <div class="grid_sizer"></div>
    <div class="product-list clearfix">
        @forelse($videoDetail as $video)
            <div class="item clearfix">
                <div class="grid-box">
                    <?php
                        $videoId = '';
                        $videoCode = Helpers::youtube_id_from_url($video->v_link);
                        if ($videoCode != '') {
                            if(strlen($video->v_link) > 50) {
                                preg_match('/=(.*?)\&/s', $video->v_link, $output);
                                $videoId = $output[1];
                            } else {
                                if (strpos($video->v_link, '=') !== false) {
                                    $output = explode('=',$video->v_link);
                                    $videoId = $output[1];
                                } else {
                                    $videoId = substr($video->v_link, strrpos($video->v_link, '/') + 1);
                                }
                            }
                        }
                    ?>
                    <figure>
                        <a title="Play : {{ $video->v_title }}" @if($videoId != '') href="https://www.youtube.com/watch?v={{$videoId}}?rel=0&amp;showinfo=0&autoplay=1" @else href="{{$video->v_link}}" @endif class="play-video">
                            <img src="{{ Storage::url(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH').$video->v_photo) }}" alt="{{ $video->v_title }}">
                            <div class="overlay">
                                <i class="icon-play"></i>
                            </div>
                        </a>
                        <h4 class="text-center">{{ $video->v_title }}</h4>
                        <figcaption>{{ $video->v_description }} </figcaption>
                    </figure>
                </div>
            </div>
        @empty
            <div class="col-sm-12 text-center">
                <h3>Video will coming soon! </h3>
            </div>
        @endforelse
    </div>
</div>
@if(isset($videoCount) && $videoCount > 12)
    <p id="remove-row" class="text-center"><a id="load-more" href="javascript:void(0)" title="load more" data-id="{{ $video->id }}" class="btn btn-primary">load more</a></p>
@endif