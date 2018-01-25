@forelse ($memberDetails as $memberDetail)
<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 no-gutter">
    <div class="connection-block">
        <figure>
            <?php if (isset($memberDetail->t_photo) && $memberDetail->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $memberDetail->t_photo) > 0) {
                $memberImage = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $memberDetail->t_photo);
            } else {
                $memberImage = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
            } ?>
            <div class="connection-img" style="background-image: url('{{ $memberImage }} ')">
                <div class="overlay">
                    <ul>
                        <li><a href="{{ url('teenager/network-member') }}/{{$memberDetail->t_uniqueid }}" title="{{ $memberDetail->t_name }}"><i class="icon-pro-user"></i></a></li>
                        <li><a href="#" title="chat"><i class="icon-chat"></i></a></li>
                    </ul>
                </div>
            </div>
            <figcaption><a href="{{ url('teenager/network-member') }}/{{$memberDetail->t_uniqueid }}" title="{{ $memberDetail->t_name }}" >{{ $memberDetail->t_name }}</a></figcaption>
        </figure>
    </div>
</div>
@empty
<center><h3>No Records Found</h3></center>
@endforelse
