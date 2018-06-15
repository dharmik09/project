@if(count($notificationData)>0)
    @foreach($notificationData as $key => $value)
    <div class="notification-block <?php echo (in_array($value->id, $readData)) ? 'read' : 'unread' ?>" id="{{$value->id}}notification-block" onclick="readNotification('{{$value->id}}')">
        <div class="notification-img">
            <?php
                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                    $photoURL = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                    if(Storage::size(Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo)>0){
                        $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                    }
                }
            ?>
            <img src="{{ Storage::url($teenPhoto) }}" alt="notification img">
        </div>
        <?php
            switch ($value->n_notification_type) {
                case Config::get('constant.NOTIFICATION_TYPE_GIFT_PRO_COINS'):
                    $redirectPageUrl = url("teenager/gift-coins");
                    break;
                
                case Config::get('constant.NOTIFICATION_TYPE_GIFT_COUPANS'):
                    $redirectPageUrl = url("teenager/coupons");
                    break;

                case Config::get('constant.NOTIFICATION_TYPE_PROFILE_VIEW'):
                    $senderId = $value->n_sender_id;
                    $teenDetails = Helpers::getTeenagerDetailsById($value->n_sender_id);
                    if (!empty($teenDetails) && $teenDetails->t_uniqueid != "") {
                        $redirectPageUrl = url("teenager/network-member/".$teenDetails->t_uniqueid);
                    } else {
                        $redirectPageUrl = url ("teenager/chat");
                    }
                    break;

                default:
                    $redirectPageUrl = url("teenager/chat");
                    break;
            };
        ?>
        <div class="notification-content"><a href="{{ $redirectPageUrl }}">{!!$value->n_notification_text!!}</a>
            <span class="date">
                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->diffForHumans() }}
            </span>
            @if($value->n_record_id != 0)
            <ul class="btn-list text-right">
                @if($value->community->tc_status == 1)
                    <li><a href="#" title="accept" class="accept">Accepted</a></li>
                @elseif($value->community->tc_statsus == 2)
                    <li><a href="#" title="decline" class="decline">Declined</a></li>
                @elseif($value->community->tc_status == 0)
                    <li><a href="{{url('teenager/accept-request').'/'.$value->n_record_id}}" title="accept" class="accept">Accept</a></li>
                    <li><a href="{{url('teenager/decline-request').'/'.$value->n_record_id}}" title="decline" class="decline">Decline</a></li>
                @endif
                <div id="pageWiseNotifications"></div>
            </ul>
            @endif
        </div>
        <div class="close"><i class="icon-close" onclick="removeNotificationBlock({{$value->id}});"></i></div>
    </div>
    @endforeach
@endif