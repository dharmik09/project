@forelse($myConnections as $myConnection)
<div class="team-list">
    <div class="flex-item">
        <div class="team-detail">
            <div class="team-img">
                <?php
                    if(isset($myConnection->t_photo) && $myConnection->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $myConnection->t_photo) > 0) {
                        $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$myConnection->t_photo;
                    } else {
                        $teenImage = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                    }
                ?>
                <img src="{{ Storage::url($teenImage) }}" alt="{{ $myConnection->t_name }}">
            </div>
            <a href="{{ url('teenager/network-member') }}/{{$myConnection->t_uniqueid}}" title="{{ $myConnection->t_name }}"> {{ $myConnection->t_name }}</a>
        </div>
    </div>
    <div class="flex-item">
        <div class="team-point">
            <span class="points">
            <?php $teenPoints = 0;
                $basicBoosterPoint = Helpers::getTeenagerBasicBooster($myConnection->id);
                $teenPoints = (isset($basicBoosterPoint['total']) && $basicBoosterPoint['total'] > 0) ? number_format($basicBoosterPoint['total']) : 0;
            ?>
            {{ $teenPoints }} points
            <?php  $connStatus = Helpers::getTeenAlreadyInConnection(Auth::guard('teenager')->user()->id, $myConnection->id); 
                    $chatTitleText = "Please make a connection to chat";
                    $chatUrl = url('teenager/network-member/'.$myConnection->t_uniqueid);
                    if (isset($connStatus) && !empty($connStatus)) {
                        if (isset($connStatus['count']) && !empty($connStatus['count']) && $connStatus['count'] == 1) {
                            $chatUrl = url("teenager/chat/" . $myConnection->t_uniqueid );
                            $chatTitleText = "Chat";
                        } else if (isset($connStatus['count']) && !empty($connStatus['count'])  && $connStatus['count'] == 3) {
                            if (isset($connStatus['connectionDetails']) && !empty($connStatus['connectionDetails'])) {
                                if ($connStatus['connectionDetails']->tc_status != '' && $connStatus['connectionDetails']->tc_status == 1) {
                                    $chatUrl = url("teenager/chat/" . $myConnection->t_uniqueid );
                                    $chatTitleText = "Chat";
                                }
                            }
                        }
                    } 
                ?>
            </span>
            <a href="{{$chatUrl}}" title="{{$chatTitleText}}"><i class="icon-chat"><!-- --></i></a>
        </div>
    </div>
</div>
@empty
    No Connections found.
@endforelse
@if (!empty($myConnections->toArray()) && $myConnectionsCount > 10)
    <div id="menu2-loader-con" class="loader_con remove-my-connection-row">
        <img src="{{Storage::url('img/loading.gif')}}">
    </div>
    <p class="text-center remove-my-connection-row"><a id="load-more-connection" href="javascript:void(0)" title="load more" class="load-more" data-id="{{ $myConnection->id }}">load more</a></p>
@endif