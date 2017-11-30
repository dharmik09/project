@if(isset($professionId) && !empty($professionId))
<div class="promisebtn timer_btn">
    <a href="javascript:void(0);" class="promise btn_golden_border" title="" onclick="getPromisePlus({{$professionId}}, {{$parentId}},{{$days}});" data-ref="#{{$professionId}}">
        <span class="promiseplus">PROMISE Plus</span>
        <span class="coinouter">
            <span class="coinsnum">{{$days}} Days Left</span>
        </span>
    </a>
</div>
@else
<span class="coinsnum">{{$days}} Days Left</span>
@endif