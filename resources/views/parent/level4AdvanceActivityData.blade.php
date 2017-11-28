@if(isset($activityData) && !empty($activityData))
<div class="modal-body">
    <div class="video_questn">
        <?php
        if (strpos($activityData[0]->l4aa_description, '[PROFESSION_NAME]') !== false) {
            $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
            $descText = str_replace('[PROFESSION_NAME]', $professionName, $activityData[0]->l4aa_description);
        } else {
            $descText = $activityData[0]->l4aa_description;
        }
        ?>
        <p class="sub_header base_title">{{$descText}}</p>
        <p class="sub_title">Interview Q's:</p>
        <?php $k = 1; ?>
        <ul>
            @foreach($activityData as $key=>$data)
            <?php
            if (strpos($data->l4aa_text, '[PROFESSION_NAME]') !== false) {
                $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
                $questionText = str_replace('[PROFESSION_NAME]', $professionName, $data->l4aa_text);
            } else {
                $questionText = $data->l4aa_text;
            }
            ?>
            <li class="base_question">{{$questionText}}</li>
            <?php $k++; ?>
            @endforeach
        </ul>
    </div>
</div>
<div class="modal-footer">
    <a class="btn primary_btn" href="{{url('parent/level4-advance-step2')}}/{{(isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->id : ''}}/{{$type}}/{{$teenId}}">Go!</a>
</div>
@else
No data found
@endif

