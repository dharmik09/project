 <ul class="career-list">
     @forelse ($relatedCareers as $career)
    <?php $career->matched = rand(0,2); 
        switch($career->matched) {
            case 0:
                $matchClass = "match-strong";
                break;

            case 1: 
                $matchClass = "match-potential";
                break;

            case 2:
                $matchClass = "match-unlikely";
                break;
                
            default:
                $matchClass = "";
                break;
        };
    ?>
    <li class="{{$matchClass}}">
        <a href="#" title="{{$career->pf_name}}">{{$career->pf_name}}</a>
    </li>
    @empty
        No Careers found
    @endforelse
</ul>
<?php 
    if (isset($relatedCareersCount) && $relatedCareersCount > Config::get('constant.RECORD_PER_PAGE')) { ?>
    <p class="text-center remove-row"><a id="see-more" href="javascript:void(0)" title="see more" data-id="{{$career->id}}">see more</a></p>
<?php } ?>
