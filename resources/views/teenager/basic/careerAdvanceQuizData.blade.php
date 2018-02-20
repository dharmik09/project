@if(isset($activityData) && !empty($activityData))
<div class="upload-screen quiz-box modal-screen sec-show">
    <em class="close" onclick="getMediaUploadSection();"><i class="icon-close"></i></em>
    <?php
        if (strpos($activityData[0]->l4aa_description, '[PROFESSION_NAME]') !== false) {
            $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
            $descText = str_replace('[PROFESSION_NAME]', $professionName, $activityData[0]->l4aa_description);
        } else {
            $descText = $activityData[0]->l4aa_description;
        } ?>
    <h3>{{$descText}}</h3>
    <h4>Interview Q's</h4>
    <?php $k = 1; ?>
    <ol>
        @foreach($activityData as $key=>$data)
        <?php
        if (strpos($data->l4aa_text, '[PROFESSION_NAME]') !== false) {
            $professionName = (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail[0]->pf_name : '';
            $questionText = str_replace('[PROFESSION_NAME]', $professionName, $data->l4aa_text);
        } else {
            $questionText = $data->l4aa_text;
        }
        ?>
            <li>{{$questionText}}</li>
        <?php $k++; ?>
        @endforeach
    </ol>
    <div class="text-center">
        <button class="btn-primary btn btn-go" title="Go" onclick="getLevel4AdvanceStep2Details({{$professionDetail[0]->id}}, {{$type}});">Go! </button>
    </div>
</div>
@else
    No data found
@endif