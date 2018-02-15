@forelse($newConnections as $newConnection)
<div class="team-list">
    <div class="flex-item">
        <div class="team-detail">
            <div class="team-img">
                <?php
                    if(isset($newConnection->t_photo) && $newConnection->t_photo != '') {
                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$newConnection->t_photo;
                    } else {
                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                    }
                ?>
                <img src="{{ Storage::url($teenPhoto) }}" alt="{{ $newConnection->t_name }}">
            </div>
            <a href="{{ url('teenager/network-member') }}/{{$newConnection->t_uniqueid}}" title="{{ $newConnection->t_name }}"> {{ $newConnection->t_name }}</a>
        </div>
    </div>
    <div class="flex-item">
        <div class="team-point">
            {{ $newConnection->t_coins }} points
            <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
        </div>
    </div>
</div>
@empty
    No Connections found.
@endforelse
@if (!empty($newConnections->toArray()) && $newConnectionsCount > 10)
    <div id="menu1-loader-con" class="loader_con remove-row">
        <img src="{{Storage::url('img/loading.gif')}}">
    </div>
    <p id="remove-row" class="text-center remove-row"><a href="javascript:void(0)" id="load-more" title="load more" class="load-more" data-id="{{ $newConnection->id }}">load more</a></p>
@endif