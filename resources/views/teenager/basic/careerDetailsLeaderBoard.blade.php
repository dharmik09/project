@forelse($leaderboardTeenagers as $leaderBoard)
<div class="team-list">
    <div class="flex-item">
        <div class="team-detail">
            <div class="team-img">
                <?php
                    if($leaderBoard->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$leaderBoard->t_photo) > 0) {
                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$leaderBoard->t_photo;
                    } else {
                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                    }
                ?>
                <img src="{{ Storage::url($teenPhoto) }}" alt="team">
            </div>
            <a href="{{ url('teenager/network-member/'.$leaderBoard->t_uniqueid)}}" title="{{$leaderBoard->t_name}}"> {{ $leaderBoard->t_name }}</a>
        </div>
    </div>
    <div class="flex-item">
        <div class="team-point">
            {{ $leaderBoard->tlb_points }} points
            <a href="#" title="Chat">
                <i class="icon-chat">
                    <!-- -->
                </i>
            </a>
        </div>
    </div>
</div>
@empty
@endforelse
@if ($nextleaderboardTeenagers && count($nextleaderboardTeenagers) > 0)
    <p class="text-center"><a href="javascript:void(0)" title="load more" class="load-more">load more</a></p>
@endif
