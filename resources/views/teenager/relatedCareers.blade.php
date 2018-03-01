<ul class="career-list <?php if (count($relatedCareers) == 0) { ?> userData <?php } ?>">
    @if(!empty($relatedCareers) && count($relatedCareers) > 0)
    @forelse ($relatedCareers as $career)
    <?php
        $professionComplete = Helpers::getProfessionCompletePercentage(Auth::guard('teenager')->user()->id, $career->id);
    ?>
    <li class="{{$career->match_scale}} <?php if (isset($professionComplete) && $professionComplete == 100) { ?> complete-feild <?php } ?> ">
        <a href="{{ url('/teenager/career-detail') }}/{{$career->pf_slug}}" title="{{$career->pf_name}}">{{$career->pf_name}}</a>
        @if (isset($professionComplete) && $professionComplete == 100)
        <a href="javascript:void(0)" class="complete"><span>Complete</span></a>
        @endif
    </li>
    @empty
        <li class="career-data-nomatch">
            <a href="javascript:void(0)" >No Careers found</a>
        </li>
    @endforelse
    @else
    <div class="no-data">
        <div class="data-content">
            <div>
                <i class="icon-empty-folder"></i>
            </div>
            <p>No data found</p>
        </div>
    </div>
    @endif
</ul>
<?php 
    if (isset($relatedCareersCount) && $relatedCareersCount > Config::get('constant.RECORD_PER_PAGE')) { ?>
    <div class="loader_con remove-row">
        <img src="{{Storage::url('img/loading.gif')}}">
    </div>
    <p class="text-center remove-row"><a id="see-more" href="javascript:void(0)" title="see more" data-id="{{(isset($career->id)) ? $career->id : '' }}">see more</a></p>
<?php } ?>
