<div class="row competitive-sec owl-carousel">
    @forelse ($scholarshipPrograms as $scholarshipProgram)
    <div class="col-sm-6">
        <div class="quiz-box">
            <div class="img">
                <?php
                    if ($scholarshipProgram->sa_image != "" && Storage::size(Config::get('constant.SA_ORIGINAL_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image) > 0) {
                        $activityImage = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . $scholarshipProgram->sa_image);
                    } else {
                        $activityImage = Storage::url(Config::get('constant.SA_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                    }
                ?>
                <img src="{{ $activityImage }}" alt="abl logo">
            </div>
            <h6>{{$scholarshipProgram->sp_company_name}}</h6>
            <h6>{{$scholarshipProgram->sa_name}}</h6>
            <p>{{$scholarshipProgram->sa_description}}</p>
            <ul class="btn-list">
                <li><a href="#" title="learn more" class="btn">learn more</a></li>
                <li><a href="#" title="Apply" class="btn btn-apply">Apply</a></li>
            </ul>
        </div>
    </div>
    @empty
    @endforelse
</div>
<div class="overlay">
    <div class="overlay-inner">
        <div class="icon"><!--<i class="icon-lock"></i>-->
        <img src="{{ Storage::url('img/img-lock.png') }}" alt="lock image"></div>
        <p>Complete previous section<br> to unlock</p>
    </div>
</div>