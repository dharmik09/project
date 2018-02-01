@if(!empty($reasoningGurus) && count($reasoningGurus) > 0)
    @forelse ($reasoningGurus as $guru)
    <div class="team-list">
        <div class="flex-item">
            <div class="team-detail">
                <div class="team-img">
                    <?php
                        if(isset($guru->t_photo) && $guru->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$guru->t_photo)) {
                            $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$guru->t_photo;
                        } else {
                            $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                        }
                    ?>
                    <img src="{{ Storage::url($teenPhoto) }}" alt="team">
                </div>
                <?php 
                    if(Auth::guard('teenager')->user()->id == $guru->id) { 
                        $url = "javascript:void(0)";
                    } else { 
                        $url = url('teenager/network-member/'.$guru->t_uniqueid); 
                    } ?>
                <a href="{{$url}}" title="{{$guru->t_name}}"> {{$guru->t_name}}</a>
            </div>
        </div>
        <div class="flex-item">
            <div class="team-point">
                {{ $guru->t_coins }} points
                <a href="#" title="Chat"><i class="icon-chat"><!-- --></i></a>
            </div>
        </div>
    </div>
    @empty
        No Records Found
    @endforelse
@else
<div class="no-data">
    <div class="data-content">
        <div>
            <i class="icon-empty-folder"></i>
        </div>
        <p>No data found</p>
    </div>
</div>
@endif
@if ($nextSlotExist >= 0)
<div class="loader_con remove-row">
    <img src="{{Storage::url('img/loading.gif')}}">
</div>
<p class="text-center remove-row">
    <a id="see-more-guru" href="javascript:void(0)" title="see more">see more</a>
</p>
@endif