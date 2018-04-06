@if(isset($teenagerData))
    @foreach($teenagerData as $key => $value)
        <div class="team-list">
            <div class="flex-item">
                <div class="team-detail">
                    <div class="team-img">
                        <?php
                            if(isset($value->t_photo) && $value->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo) > 0) {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo;
                            } else {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                            }
                        ?>
                        <img src="{{ Storage::url($teenPhoto) }}" alt="team">
                    </div>
                    <a href="{{url('teenager/network-member/'.$value->t_uniqueid)}}" title="{{$value->t_name}}">{{$value->t_name}}</a>
                </div>
            </div>
            <div class="flex-item">
                <div class="team-point">
                    <?php $teenPoints = 0;
                        $basicBoosterPoint = Helpers::getTeenagerBasicBooster($value->id);
                        $teenPoints = (isset($basicBoosterPoint['total']) && $basicBoosterPoint['total'] > 0) ? number_format($basicBoosterPoint['total']) : 0;
                    ?>
                    <span class="points">
                    {{ $teenPoints }} points</span>
                        <?php $connStatus = Helpers::getTeenAlreadyInConnection(Auth::guard('teenager')->user()->id, $value->id); 
                        $chatUrl = "javascript:void(0);";
                        if (isset($connStatus) && !empty($connStatus)) {
                            if (isset($connStatus['count']) && !empty($connStatus['count']) && $connStatus['count'] == 1) {
                                $chatUrl = url("teenager/chat/" . $value->t_uniqueid );
                            } else if (isset($connStatus['count']) && !empty($connStatus['count'])  && $connStatus['count'] == 3) {
                                if (isset($connStatus['connectionDetails']) && !empty($connStatus['connectionDetails'])) {
                                    if ($connStatus['connectionDetails']->tc_status != '' && $connStatus['connectionDetails']->tc_status == 1) {
                                        $chatUrl = url("teenager/chat/" . $value->t_uniqueid );
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
    @endforeach
@else
	<div class="sec-forum"><span>No result Found</span></div>
@endif