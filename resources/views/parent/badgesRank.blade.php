<span class="img_container">
    <img src="{{$response['profession_logo']}}" alt="">
</span>
<span class="title dashboard_profession">
    <span class="profession_text" style="font-size: 14px;" title="{{$response['professionName']}}">
        <span>{{$response['professionName']}}</span>
    </span>
</span>

<div class="rank_local">
    <span style="font-size: 14px;"><?php echo ($teenagerId == Auth::guard('parent')->user()->id)?"My":''; ?> Rank : {{$response['rank']}}</span>
    <span style="font-size: 14px;"><?php echo ($teenagerId == Auth::guard('parent')->user()->id)?"My":''; ?> Points : {{$response['yourscore']}}</span>
</div>


<div class="ahchivement">
    <?php
    if (isset($response['badges'][0]['newbie']) && $response['badges'][0]['newbie'] != '') {
        ?>
        <img src="{{$response['badges'][0]['newbie']}}" alt="" />
    <?php } else {
        ?>
        <img src="{{Storage::url('frontend/images/newbie_default.png')}}" alt="" />
        <?php
    }
    if (isset($response['badges'][0]['apprentice']) && $response['badges'][0]['apprentice'] != '') {
        ?>
        <img src="{{$response['badges'][0]['apprentice']}}" alt="" />
    <?php } else {
        ?>
        <img src="{{Storage::url('frontend/images/apprentice_default.png')}}" alt="" />
        <?php
    }
    if (isset($response['badges'][0]['wizard']) && $response['badges'][0]['wizard'] != '') {
        ?>
        <img src="{{$response['badges'][0]['wizard']}}" alt="" />
    <?php } else {
        ?>
        <img src="{{Storage::url('frontend/images/wizard_default.png')}}" alt="" />
    <?php } ?>
</div>