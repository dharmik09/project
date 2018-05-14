@if ($careerConsideration && count($careerConsideration) > 0)
    <div class="unbox-btn">
        <a id="career_unbox" href="javascript:void(0)" title="Unlock Me" @if($remainingDaysForCareerConsider <= 0) onclick="getCareersConsiderDetails('{{Auth::guard('teenager')->user()->t_coins}}', '{{ $componentsCareerConsider->pc_required_coins }}');" @endif class="btn-primary" data-toggle="modal" >
            <span class="unbox-me career_text">@if($remainingDaysForCareerConsider <= 0) Unlock Me @else See Now! @endif</span>
            <span class="coins-outer career_coins">
                <span class="coins"></span> {{ ($remainingDaysForCareerConsider > 0) ? $remainingDaysForCareerConsider . ' days left' : $componentsCareerConsider->pc_required_coins }}
            </span>
        </a>
    </div>
    <?php $countCareersConsideration = 0; ?>
    @if ($remainingDaysForCareerConsider > 0)
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
            if ($countCareersConsideration > 7) {
                $carrerStyleTwo = 'none';
                $careerExpandClassTwo = "sec-wrap-5";
            } else {
                $carrerStyleTwo = 'block';
                $careerExpandClassTwo = "";
            }
        ?>
        <div class="careers-container">
        <div class="career-data {{$careerClass}} {{ $careerExpandClassTwo }} " style="display: {{ $carrerStyleTwo }};">           
            <h2>
                <?php $pfName = $professionArray['pf_name']; ?>
                @if( $professionArray['added_my_career'] == 0) 
                    <a id="add-to-star" href="javascript:void(0)" class="addto pull-left prof_sec_{{$professionArray['id']}}" onclick="addToMyCareerProfession({{$professionArray['id']}}, {{Config::get('constant.ADD_STAR_TO_CAREER')}}, '{{$pfName}}' )" title="Add to My Careers">
                        <img src="{{ Storage::url('img/star.png') }}">
                    </a>
                @else
                    <a id="remove-star" href="javascript:void(0)" onclick="addToMyCareerProfession({{$professionArray['id']}}, {{Config::get('constant.REMOVE_STAR_FROM_CAREER')}}, '{{$pfName}}' )" class="addto pull-left selected prof_sec_{{$professionArray['id']}}" title="In My Careers">
                        <img src="{{ Storage::url('img/star-active.png') }}" class="hover-img">
                    </a>
                @endif
                {{ $professionArray['pf_name'] }}
            </h2>
            
            <div class="clearfix">
                <!-- @if( $professionArray['added_my_career'] == 0 ) 
                    <a href="javascript:void(0)" class="addto pull-left text-uppercase prof_sec_{{$professionArray['id']}}" onclick="addToMyCareerProfession({{$professionArray['id']}})">add to my careers</a> 
                @else 
                    <a href="javascript:void(0)" class="addto pull-left text-uppercase"> Added </a> 
                @endif -->
                @if($professionArray['is_completed'] == 0) 
                    <a href="{{ url('teenager/career-detail/') }}/{{ $professionArray['pf_slug'] }}" class="status-career pull-right">Explore ></a> @else <span class="status-career pull-right">Complete</span> 
                @endif
            </div>
        </div>
        </div>    
    @empty
        <div class="career-data">
            <h3 href="javascript:void(0);" class="interest-section">Build your profile to know careers to consider!</h3>
        </div>
    @endforelse
    <div class="data-explainations clearfix">
        <div class="data"><span class="small-box career-data-color-1"></span><span>Strong match</span></div>
        <div class="data"><span class="small-box career-data-color-2"></span><span>Potential match</span></div>
        <div class="data"><span class="small-box career-data-color-3"></span><span>Unlikely match</span></div>
    </div>
    @if(count($careerConsideration) > 7) 
        <p>
            <span class="expand-4 less">Expand</span>
        </p>
    @endif
    @else
        <div class="career-data">
            <h3 href="javascript:void(0);" class="interest-section">Please consume your procoins to view your career suggestions</h3>
        </div>
    @endif
    
@endif