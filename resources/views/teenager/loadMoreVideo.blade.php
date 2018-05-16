<?php shuffle($videoDetail); ?>
@forelse($videoDetail as $video)
    <div class="item clearfix">
        <div class="grid-box">
            <?php
                $videoId = '';
                $videoCode = Helpers::youtube_id_from_url($video['v_link']);
                if ($videoCode != '') {
                    if(strlen($video['v_link']) > 50) {
                        preg_match('/=(.*?)\&/s', $video['v_link'], $output);
                        $videoId = $output[1];
                    } else {
                        if (strpos($video['v_link'], '=') !== false) {
                            $output = explode('=',$video['v_link']);
                            $videoId = $output[1];
                        } else {
                            $videoId = substr($video['v_link'], strrpos($video['v_link'], '/') + 1);
                        }
                    }
                }
            ?>
            <figure>
                <a title="Play : {{ $video['v_title'] }}" @if($videoId != '') href="https://www.youtube.com/watch?v={{$videoId}}?rel=0&amp;showinfo=0&autoplay=1" @else href="{{$video['v_link']}}" @endif class="play-video">
                    <?php if(Storage::size(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH').$video['v_photo']) > 0 && $video['v_photo'] != "")  {
                        $imagePath = Storage::url(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH').$video['v_photo']);
                    } else {
                        $imagePath = Storage::url(Config::get('constant.VIDEO_ORIGINAL_IMAGE_UPLOAD_PATH').'proteen-logo.png');
                    } ?>
                    <img src="{{ $imagePath }}" alt="{{ $video['v_title'] }}">
                    <div class="overlay">
                        <i class="icon-play"></i>
                    </div>
                </a>
                <h4 class="text-center">{{ $video['v_title'] }}</h4>
                <figcaption>{{ $video['v_description'] }} </figcaption>
            </figure>
        </div>
    </div>
@empty
    <div class="col-sm-12 text-center">
        <h3>Video will coming soon! </h3>
    </div>
@endforelse
