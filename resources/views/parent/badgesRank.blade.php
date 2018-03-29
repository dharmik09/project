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


