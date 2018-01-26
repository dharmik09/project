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
        } ?>
    <div class="col-md-6 col-sm-6 col-xs-6 flex-items {{ $elementClass }}" style="display: {{ $key }};">
        <div class="my_chart">
            <div class="progress-radial progress-{{$strengthValue['score']}}">
            </div>
            <h4><a href="/teenager/multi-intelligence/{{$strengthValue['type']}}/{{$strengthKey}}"> {{ $strengthValue['name'] }}</a></h4>
        </div>
    </div>
@empty
    <div class="col-md-6 col-sm-6 col-xs-6 flex-items">
        <center>
            <h3>No Records Found</h3>
        </center>
    </div>
@endforelse

@if(count($teenagerStrength) > 4 && !empty($teenagerStrength))
    <p>
        <a id="strength" href="javascript:void(0);" >Expand</a>
    </p>
@endif