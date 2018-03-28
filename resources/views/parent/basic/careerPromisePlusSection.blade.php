<div class="promise-plus front_page" id="hidepromiseplus">
    <div class="heading">
        <span><i class="icon-plus"></i></span>
        <h3>Promise Plus</h3>
    </div>
    <p>Your individualized suggestion based on your career explore role play tasks. The more you explore, the more the suggestion gets refined</p>
    <div class="unbox-btn">
        <a id="promise_plus" href="javascript:void(0)" title="Unlock Me" class="btn-primary" @if($promisePlusRemainingDays == 0) onclick="getCoinsConsumptionDetails('{{$promisePlusComponent->pc_required_coins}}', '{{$promisePlusComponent->pc_element_name}}', '{{$promisePlusRemainingDays}}');" @else onclick="getPromisePlusData({{$professionsData->id}})"@endif >
           <span id="promisespan" class="unbox-me btn-default">@if($promisePlusRemainingDays == 0) Unlock Me @else See Now! @endif</span><span class="coins-outer promise-plus-coins"><span class="coins"></span> {{ ($promisePlusRemainingDays > 0) ? $promisePlusRemainingDays . ' days left' : $promisePlusComponent->pc_required_coins }}</span>
        </a></div>
</div>
<div id="showPromisePlusData">
    
</div>
