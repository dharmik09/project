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
                <?php 
                    if (!empty($exceptScholarshipIds) && in_array($scholarshipProgram->id, $exceptScholarshipIds)) {
                        $callbleFunction = "";
                        $buttonText = "Applied";
                     } else {
                        $callbleFunction = "applyForScholarshipProgram($scholarshipProgram->id)";
                        $buttonText = "Apply";
                     } ?>
                <li><a href="javascript:void(0)" id="apply_{{$scholarshipProgram->id}}" title="Apply" class="btn btn-apply" onclick="{{$callbleFunction}}" >{{$buttonText}}</a><span id="scholarship_message_{{$scholarshipProgram->id}}" style="position: absolute; text-align: center; width: 300px; left: 50%; -webkit-transform: translatex(-50%);
    -ms-transform: translatex(-50%); -o-transform: translatex(-50%); transform: translatex(-50%); padding: 10px; border: 1px solid #fff; background: #fff;-webkit-border-radius: 5px; border-radius: 5px; margin-top: 55px; z-index: 99;-webkit-box-shadow: 0 1px 17px -4px #989494; box-shadow: 0 1px 17px -4px #989494; font-size: 14px; color: rgba(22,28,34,.7); display: none;" ></span></li>
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