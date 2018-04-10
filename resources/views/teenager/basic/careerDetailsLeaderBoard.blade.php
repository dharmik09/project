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
            <?php 
                if ($leaderBoard->id == Auth::guard('teenager')->user()->id) {
                    $memberUrl = "javascript:void(0)";
                } else {
                    $memberUrl = url('teenager/network-member/'.$leaderBoard->t_uniqueid);
                } ?>
            <a href="{{$memberUrl}}" title="{{$leaderBoard->t_name}}"> {{ $leaderBoard->t_name }}</a>
        </div>
    </div>
    <div class="flex-item">
        <div class="team-point">
            <span class="points">
            {{ $leaderBoard->tlb_points }} points</span>
            <?php $connStatus = Helpers::getTeenAlreadyInConnection(Auth::guard('teenager')->user()->id, $leaderBoard->id); 
                    $chatUrl = "javascript:void(0);";
                    if (isset($connStatus) && !empty($connStatus)) {
                        if (isset($connStatus['count']) && !empty($connStatus['count']) && $connStatus['count'] == 1) {
                            $chatUrl = url("teenager/chat/" . $leaderBoard->t_uniqueid );
                        } else if (isset($connStatus['count']) && !empty($connStatus['count'])  && $connStatus['count'] == 3) {
                            if (isset($connStatus['connectionDetails']) && !empty($connStatus['connectionDetails'])) {
                                if ($connStatus['connectionDetails']->tc_status != '' && $connStatus['connectionDetails']->tc_status == 1) {
                                    $chatUrl = url("teenager/chat/" . $leaderBoard->t_uniqueid );
                                }
                            }
                        }
                    } 
                ?>
            <a href="{{$chatUrl}}" title="Chat">
                <i class="icon-chat">
                    <!-- -->
                </i>
            </a>
        </div>
    </div>
</div>
@empty
<center>
    <h3>No Records found.</h3>
</center>
@endforelse
@if ($nextleaderboardTeenagers && count($nextleaderboardTeenagers) > 0)
    <div class="loader_con remove-row">
        <img src="{{Storage::url('img/loading.gif')}}">
    </div>
    <p class="text-center remove-row"><a id="load-more-leaderboard" href="javascript:void(0)" title="load more" class="load-more">load more</a></p>
@endif
