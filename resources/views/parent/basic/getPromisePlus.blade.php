@if(isset($finalPromisePlusData['level4Data']) && !empty($finalPromisePlusData['level4Data']))

<div class="promise-plus-overlay">
    <?php if($finalPromisePlusData['promisePlus'] == 'match'){ $promiseClass = 'match-high';}elseif($finalPromisePlusData['promisePlus'] == 'moderate'){$promiseClass = 'match-med';}else{$promiseClass = '';}  ?>
    <?php 
    if($finalPromisePlusData['level2Promise'] == 'match') 
    {
        if($finalPromisePlusData['promisePlus'] == 'match'){
                $imageClass = 'str-s-icon-1';	
        }elseif($finalPromisePlusData['promisePlus'] == 'moderate'){
                $imageClass = 'med-m-icon-2';
        }elseif($finalPromisePlusData['promisePlus'] == 'nomatch'){
                $imageClass = 'emojis-icon-3';
        }					
    }
    if($finalPromisePlusData['level2Promise'] == 'moderate') 
    {
        if($finalPromisePlusData['promisePlus'] == 'match'){
                $imageClass = 'str-s-icon-4';	
        }elseif($finalPromisePlusData['promisePlus'] == 'moderate'){
                $imageClass = 'med-m-icon-5';
        }elseif($finalPromisePlusData['promisePlus'] == 'nomatch'){
                $imageClass = 'emojis-icon-6';
        }					
    }
    if($finalPromisePlusData['level2Promise'] == 'nomatch') 
    {
        if($finalPromisePlusData['promisePlus'] == 'match'){
                $imageClass = 'str-s-icon-7';	
        }elseif($finalPromisePlusData['promisePlus'] == 'moderate'){
                $imageClass = 'med-m-icon-8';
        }elseif($finalPromisePlusData['promisePlus'] == 'nomatch'){
                $imageClass = 'emojis-icon-9';
        }					
    }					
    ?>
    <div class="promise-plus {{$promiseClass}}">
        <button type="button" class="close" data-dismiss="modal" onclick="hidePromisePlusModal()"><i class="icon-close"></i></button>
        <div class="heading">
            <span class="emojis-img">     
                
                <img class="{{$imageClass}}" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAAB1AQMAAAC7wWdyAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAABdJREFUeNpjYBgFo2AUjIJRMApGwZAEAAfFAAFzojyWAAAAAElFTkSuQmCC">
            </span> 
            <h3>Promise Plus</h3>
        </div>
        <p>
           {{$finalPromisePlusData['level4Data'][0]->ps_description or ''}} 
        </p>        
    </div>
</div>
    
@else
<div class="promise-plus-overlay">
    <div class="promise-plus">
        <button type="button" class="close" data-dismiss="modal" onclick="hidePromisePlusModal()"><i class="icon-close"></i></button>
        <div class="heading">
            <span class="emojis-img">     
            </span> 
            <h3>Promise Plus</h3>
        </div>
        <p>Please attempt profession first to see Promise Plus</p>
    </div>
</div>
@endif