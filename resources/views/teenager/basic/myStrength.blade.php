<?php $countStrength = 0; ?>
@forelse($teenagerStrength as $strengthKey => $strengthValue)
    <?php
        $countStrength++;
        if ($countStrength > 4) {
            $key = 'none';
            $elementClass = "expandStrength";
        } else {
            $key = 'block';
            $elementClass = '';
        }
        
        if($strengthValue['scale'] == 'H'){
            $progressClass = 'progress-strong';
        }
        elseif($strengthValue['scale'] == 'M'){
            $progressClass = 'progress-potential';
        }
        elseif($strengthValue['scale'] == 'L'){
            $progressClass = 'progress-unlikely';
        }else{
            $progressClass = '';
        }
    ?>
    <div class="col-md-6 col-sm-6 col-xs-6 flex-items {{ $elementClass }}" style="display: {{ $key }};">
        <div class="my_chart">
            
            <div class="progress-radial progress-{{$strengthValue['score']}} {{$progressClass}}">
            </div>
            <h4><a href="/teenager/multi-intelligence/{{$strengthValue['type']}}/{{$strengthValue['slug']}}"> {{ $strengthValue['name'] }}</a></h4>
        </div>
    </div>
@empty
    <div class="col-md-12 col-sm-12 col-xs-12 flex-items">
        <center>
            <h3>Please attempt at least one section of Profile Builder to view your strength!</h3>
        </center>
    </div>
@endforelse

@if(count($teenagerStrength) > 4 && !empty($teenagerStrength))
    <p>
        <a id="strength" href="javascript:void(0);" >Expand</a>
    </p>
@endif