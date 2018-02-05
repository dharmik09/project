@if(isset($teenagerData))
    @foreach($teenagerData as $key => $value)
        <div class="team-list">
            <div class="flex-item">
                <div class="team-detail">
                    <div class="team-img">
                        <?php
                            if(isset($value->t_photo) && $value->t_photo != '') {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->t_photo;
                            } else {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                            }
                        ?>
                        <img src="{{ Storage::url($teenPhoto) }}" alt="team">
                    </div>
                    <a href="#" title="{{$value->t_name}}">{{$value->t_name}}</a>
                </div>
            </div>
            <div class="flex-item">
                <div class="team-point">
                    {{$value->t_coins}} points
                    <a href="#" title="Chat">
                        <i class="icon-chat">
                            <!-- -->
                        </i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
@endif