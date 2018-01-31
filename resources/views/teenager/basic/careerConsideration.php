@if(isset($careerConsideration) && count($careerConsideration) > 0)
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
                    $careerClass = '';
                    break; 
            };
        ?>
        <div class="career-data {{$careerClass}}">
            <a href="{{ url('teenager/career-detail/') }}/{{ $professionArray['pf_slug'] }}" title="{{ $professionArray['pf_name'] }}"><h2>{{ $professionArray['pf_name'] }}</h2></a>
            <div class="clearfix">
                @if( $professionArray['added_my_career'] == 0 ) <a href="{{ url('teenager/career-detail/') }}/{{ $professionArray['pf_slug'] }}" class="addto pull-left text-uppercase">add to my careers</a> @else <a href="javascript:void(0)" class="addto pull-left"> Added </a> @endif
                <span class="status-career pull-right">Complete</span>
            </div>
        </div>
    @empty
        <div class="career-data">
            <h3 href="javascript:void(0);" class="interest-section">No any careers consideration!</h3>
        </div>
    @endforelse
@else
    <div class="career-data">
        <h3 href="javascript:void(0);" class="interest-section">No any careers consideration!</h3>
    </div>
@endif
@if(count($careerConsideration) > 0) <p><a href="">Expand</a></p> @endif