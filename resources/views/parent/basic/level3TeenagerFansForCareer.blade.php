@if(isset($teenagerData) && count($teenagerData) > 0)
    @foreach($teenagerData as $value)
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
                    <a href="javascript:void(0)">{{$value->t_name}}</a>
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
                </div>
            </div>
        </div>
    @endforeach
@else
	<div class="sec-forum"><span>No result Found</span></div>
@endif