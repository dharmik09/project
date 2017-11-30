@if(isset($professionAttempted) && !empty($professionAttempted))
<div class="flip_scroll">
<div style="text-align: center;">
    <div>L-2 PROMISE</div>
    <div style="width:50px;display:inline-block;padding-top: 10px;">
    <?php  if($professionAttempted['level2Promise'] == 'nomatch') {
    ?>
        <img src="{{ asset('frontend/images/Look_Elsewhere_LL.png')}}" height="50px" alt="">
    <?php
    }?>
    <?php if($professionAttempted['level2Promise'] == 'moderate') {
    ?>
        <img src="{{ asset('frontend/images/Possible_Choice_MM.png')}}" height="50px" alt="">
    <?php
    }?>
    <?php  if($professionAttempted['level2Promise'] == 'match') {
    ?>
        <img src="{{ asset('frontend/images/Fitting_Choice_HH.png')}}"height="50px" alt="">
    <?php
    }?>
    </div>
</div>
<?php  if ($professionAttempted['promisePlus'] != '') {?>
<div class="promise_plus_label">
    <div class="pp_label"><span style="border-top: 2px solid #e66a45;">&nbsp;&nbsp;L-4 PROMISE Plus&nbsp;&nbsp;</span></div>
    <div class="ahchivement promise_plus_ach">
    <?php  if($professionAttempted['level2Promise'] == 'match') {
    ?>
        <img src="{{ asset('frontend/images/Fitting_Choice_HH.png')}}"  alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'match') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Stretch_Yourself_HM.png')}}" alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Secondary_Choice_HL.png')}}" alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
    <?php
    } if($professionAttempted['level2Promise'] == 'moderate'){
    ?>
        <img src="{{ asset('frontend/images/Growth_Option_MH.png')}}" alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'match') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Possible_Choice_MM.png')}}"  alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Stretch_Yourself_ML.png')}}"  alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
    <?php
    } if($professionAttempted['level2Promise'] == 'nomatch') {
    ?>
        <img src="{{ asset('frontend/images/Surprise_Match_LH.png')}}"  alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'match') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Secondary_Choice_LM.png')}}"  alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'moderate') { echo 'promise_img';}?>">
        <img src="{{ asset('frontend/images/Look_Elsewhere_LL.png')}}" alt="" class="promise_plus_img <?php if($professionAttempted['promisePlus'] == 'nomatch') { echo 'promise_img';}?>">
    <?php
    }?>
    </div>
</div>
<?php  if (!empty($professionAttempted['level4Data'])) {?>
<div class="clearboth">
    <div style="font-weight:bold;padding-top: 10px;">
        {{$professionAttempted['level4Data'][0]->ps_text}}
    </div>
    <div style="font-size:12px;">
        {{$professionAttempted['level4Data'][0]->ps_description}}
    </div>
</div>
    </div>
<?php
}
} else {
?>
<div class="noData pp_nodata">
    <span>{{trans('labels.nodatainps')}}</span>
</div>
<?php
}?>
@else
No data found
@endif
<script>
jQuery(document).ready(function($) {
    var professionId = <?php echo $professionAttempted['professionId']; ?>;

    $('#close_'+professionId).click(function(event) {
        $(this).parents('.flip-container').removeClass('flip_now');
        $.ajax({
            url: "{{ url('/parent/getremainigdays') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "profession": professionId,
                "parentId": <?php if (Auth::parent()->check()) { echo Auth::parent()->get()->id; } else { echo 0;}?>
            },
            success: function(response) {
               $('#days_'+professionId).html(response);
            }
        });
    });
});
</script>