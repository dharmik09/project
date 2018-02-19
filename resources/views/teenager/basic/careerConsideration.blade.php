<?php $countCareersConsideration = 0; ?>

@forelse($careerConsideration as $professionArray)
    <?php 
        switch($professionArray['match_scale']) {
            case 'match':
                $careerClass = 'career-data-color-1';
                break;
            case 'moderate':
                $careerClass = 'career-data-color-2';
                break;
            case 'nomatch':
                $careerClass = 'career-data-color-3';
                break;
            default:
                $careerClass = 'career-data-nomatch';
                break; 
        };
        $countCareersConsideration++;
        if ($countCareersConsideration > 6) {
            $carrerStyleTwo = 'none';
            $careerExpandClassTwo = "sec-wrap-5";
        } else {
            $carrerStyleTwo = 'block';
            $careerExpandClassTwo = "";
        }
    ?>
    <div class="career-data {{$careerClass}} {{ $careerExpandClassTwo }} " style="display: {{ $carrerStyleTwo }};">
        <a href="{{ url('teenager/career-detail/') }}/{{ $professionArray['pf_slug'] }}" title="{{ $professionArray['pf_name'] }}"><h2>{{ $professionArray['pf_name'] }}</h2></a>
        <div class="clearfix">
            @if( $professionArray['added_my_career'] == 0 ) <a href="javascript:void(0)" class="addto pull-left text-uppercase prof_sec_{{$professionArray['id']}}" onclick="addToMyCareerProfession({{$professionArray['id']}})">add to my careers</a> @else <a href="javascript:void(0)" class="addto pull-left text-uppercase"> Added </a> @endif
            @if($professionArray['is_completed'] == 0) <a href="{{ url('teenager/career-detail/') }}/{{ $professionArray['pf_slug'] }}" class="status-career pull-right">Explore ></a> @else <span class="status-career pull-right">Complete</span> @endif
        </div>
    </div>

@empty
    <div class="career-data">
        <h3 href="javascript:void(0);" class="interest-section">Build your profile to know careers to consider!</h3>
    </div>
@endforelse
@if(count($careerConsideration) > 0) 
    <div class="data-explainations clearfix">
        <div class="data"><span class="small-box career-data-color-1"></span><span>Strong match</span></div>
        <div class="data"><span class="small-box career-data-color-2"></span><span>Potential match</span></div>
        <div class="data"><span class="small-box career-data-color-3"></span><span>Unlikely match</span></div>
    </div>
    @if(count($careerConsideration) > 6) 
        <p>
            <span class="expand-4 less">Expand</span>
        </p>
    @endif
@endif