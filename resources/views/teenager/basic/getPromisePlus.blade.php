@if(isset($finalPromisePlusData) && !empty($finalPromisePlusData))

<div class="promise-plus-overlay">
    <div class="promise-plus">
        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
        <div class="heading">
            <span class="emojis-img">
                <?php if($finalPromisePlusData['level2Promise'] == 'match') {
                    ?>
                        <img src="{{ asset('frontend/images/Fitting_Choice_HH.png')}}"  alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'match') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Stretch_Yourself_HM.png')}}" alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Secondary_Choice_HL.png')}}" alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
                    <?php
                    } if($finalPromisePlusData['level2Promise'] == 'moderate'){
                    ?>
                        <img src="{{ asset('frontend/images/Growth_Option_MH.png')}}" alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'match') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Possible_Choice_MM.png')}}"  alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Stretch_Yourself_ML.png')}}"  alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
                    <?php
                    } if($finalPromisePlusData['level2Promise'] == 'nomatch') {
                    ?>
                        <img src="{{ asset('frontend/images/Surprise_Match_LH.png')}}"  alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'match') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Secondary_Choice_LM.png')}}"  alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
                        <img src="{{ asset('frontend/images/Look_Elsewhere_LL.png')}}" alt="" class="promise_plus_img <?php if($finalPromisePlusData['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
                    <?php
                    }?>
                <!--<img class="emojis-icon-2" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAAB1AQMAAAC7wWdyAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGwZAEAAfFAAFzojyWAAAAAElFTkSuQmCC">-->
            </span>
            <h3>Promise Plus</h3>
        </div>
        <p>
           {{$finalPromisePlusData['level4Data'][0]->ps_description or ''}} 
        </p>        
    </div>
</div>
@else
No data found
@endif