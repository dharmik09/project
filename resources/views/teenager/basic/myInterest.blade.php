<?php $countInterest = 0; ?>
@forelse($teenagerInterest as $interestKey => $interestValue)
    <?php
        $countInterest++; 
        if ($countInterest > 4) {
            $key = 'none';
            $elementClass = "expandElement";
        } else {
            $key = 'block';
            $elementClass = '';
        }
    ?>
    <div class="col-md-6 col-sm-6 col-xs-6 flex-items {{ $elementClass }}" style="display: {{ $key }};" > 
        <div class="my_chart">
            <div class="progress-radial progress-{{$interestValue['score']}}">
            </div>
            <h4>
                <a href="{{ url('teenager/interest/') }}/{{$interestKey}}">{{ $interestValue['name']}}
                </a>
            </h4>
        </div>
    </div>
@empty
    <div class="col-md-12 col-sm-12 col-xs-12 flex-items ">
        <center>
            <h3>Please attempt at least one section of Profile Builder to view your strength!</h3>
        </center>
    </div>
@endforelse

@if (count($teenagerInterest) > 4 && !empty($teenagerInterest))
    <p>
        <a id="interest" href="javascript:void(0);" class="interest-section">Expand</a>
    </p>
@endif