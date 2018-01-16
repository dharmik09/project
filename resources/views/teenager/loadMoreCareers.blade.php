@forelse ($myCareers as $myCareer)
<div class="careers-block">
    <div class="careers-img">
        <!-- <i class="icon-image"></i> -->
        <?php
            if ($myCareer->pf_logo != "" && Storage::size(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$myCareer->pf_logo) > 0) {
                $pfLogo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$myCareer->pf_logo);
            } else {
                $pfLogo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH')."proteen-logo.png");
            } ?>
    <span class="i-image"><img src="{{ $pfLogo }}" alt="career image"></span>
    </div>
    <div class="careers-content">
        <h4>{{ $myCareer->pf_name }}</h4>
    </div>
</div>
@empty
<center>
    <h3>No Records found.</h3>
</center>
@endforelse
@if (!empty($myCareers) && $myCareersCount > 10)
    <p class="text-center remove-my-careers-row">
        <a id="load-more-career" href="javascript:void(0)" title="load more" class="load-more" data-id="{{ $myCareer->attemptedId }}">load more</a>
    </p>
@endif