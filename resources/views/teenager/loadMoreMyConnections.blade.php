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
            <?php $teenPoints = 0;
                $basicBoosterPoint = Helpers::getTeenagerBasicBooster($myConnection->id);
                $teenPoints = (isset($basicBoosterPoint['total']) && $basicBoosterPoint['total'] > 0) ? number_format($basicBoosterPoint['total']) : 0;
            ?>
            {{ $teenPoints }} points
            <a href="{{url('teenager/chat')}}/{{$myConnection->t_uniqueid}}" title="Chat"><i class="icon-chat"><!-- --></i></a>
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