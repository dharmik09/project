@if(count($notificationData)>0)
    @foreach($notificationData as $key => $value)
    <div class="notification-block <?php echo ($value->n_read_status == 1) ? 'read' : 'unread' ?>" id="{{$value->id}}notification-block" onclick="readNotification('{{$value->id}}')">
        <div class="notification-img">
            <?php
                if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                    $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                } else {
                    $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                }
            ?>
            <img src="{{ Storage::url($teenPhoto) }}" alt="notification img">
        </div>
        <div class="notification-content"><a href="#">{!!$value->n_notification_text!!}</a><span class="date">{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->diffForHumans()}}</span>
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