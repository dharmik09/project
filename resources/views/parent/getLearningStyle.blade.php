@if(isset($userLearningData) && !empty($userLearningData))
@foreach($userLearningData as $key=>$data)
@if (stripos($data->ls_name, "factual") !== false)
@if (stripos($data->ls_name, "remembering") !== false)
<div class="learn_outer lg_header_border">
    <div class="learn_inner lg_header_font">
        <p>
            <img src="{{ asset('frontend/images/Factual.png')}}" alt=""><span>Factual</span>
        </p>
        <button class="micro_detail_button" data-idaa="factual_data">More</button>
        <div class="micro_detail factual_data">Basic elements an individual must know to be acquainted with a subject or  solve problems in it</div>
    </div>
</div>
@endif
<div class="learn_outer factual_data" style="display:none;">
    <div class="graph_outer <?php if (($data->interpretationrange) == 'Low') {echo 'graph_outer3';} else if (($data->interpretationrange) == 'Medium') {echo 'graph_outer1';} else if (($data->interpretationrange) == 'High' ) {echo 'graph_outer2';} else { echo 'graph_outer4';}?>" style="width: <?php if ($data->totalAttemptedP != 0) { echo ($data->totalAttemptedP+50)."%"; } else {echo "50%";}?>">
    </div>
    <div class="learn_inner">
        <p>
            <img src="{{$data->ls_image}}" alt=""><span><?php $data->ls_name = str_replace('factual',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}} </span>
        </p>
        <button class="micro_detail_button micro_detail_more">More</button>
        <div class="micro_detail">{!! nl2br(e($data->ls_description)) !!}</div>
    </div>
</div>
@endif
@if (stripos($data->ls_name, "conceptual") !== false)
@if (stripos($data->ls_name, "remembering") !== false)
<div class="learn_outer lg_header_border">
    <div class="learn_inner lg_header_font">
        <p>
            <img src="{{ asset('frontend/images/Conceptual.png')}}" alt=""><span>Conceptual</span>
        </p>
        <button class="micro_detail_button" data-idaa="conceptual_data">More</button>
        <div class="micro_detail conceptual_data">The inter-relationships among the basic elements within a larger structure that enable them to function together</div>
    </div>
</div>
@endif
<div class="learn_outer conceptual_data"  style="display:none;">
    <div class="graph_outer <?php if (($data->interpretationrange) == 'Low') {echo 'graph_outer3';} else if (($data->interpretationrange) == 'Medium') {echo 'graph_outer1';} else if (($data->interpretationrange) == 'High' ) {echo 'graph_outer2';} else { echo 'graph_outer4';}?>" style="width: <?php if ($data->totalAttemptedP != 0) { echo ($data->totalAttemptedP+50)."%"; } else {echo "50%";}?>">
    </div>
    <div class="learn_inner">
        <p>
            <img src="{{$data->ls_image}}" alt=""><span><?php $data->ls_name = str_replace('conceptual',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}} </span>
        </p>
        <button class="micro_detail_button micro_detail_more">More</button>
        <div class="micro_detail">{{$data->ls_description}}</div>
    </div>
</div>
@endif
@if (stripos($data->ls_name, "procedural") !== false)
@if (stripos($data->ls_name, "remembering") !== false)
<div class="learn_outer lg_header_border">
    <div class="learn_inner lg_header_font">
        <p>
            <img src="{{ asset('frontend/images/Procedural.png')}}" alt=""><span>Procedural</span>
        </p>
        <button class="micro_detail_button" data-idaa="procedural_data">More</button>
        <div class="micro_detail procedural_data">How to do something, methods of enquiry and criteria for using skills, algorithms, techniques and methods</div>
    </div>
</div>
@endif
<div class="learn_outer procedural_data"  style="display:none;">
    <div class="graph_outer <?php if (($data->interpretationrange) == 'Low') {echo 'graph_outer3';} else if (($data->interpretationrange) == 'Medium') {echo 'graph_outer1';} else if (($data->interpretationrange) == 'High' ) {echo 'graph_outer2';} else { echo 'graph_outer4';}?>" style="width: <?php if ($data->totalAttemptedP != 0) { echo ($data->totalAttemptedP+50)."%"; } else {echo "50%";}?>">
    </div>
    <div class="learn_inner">
        <p>
            <img src="{{$data->ls_image}}" alt=""><span><?php $data->ls_name = str_replace('procedural',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}} </span>
        </p>
        <button class="micro_detail_button micro_detail_more">More</button>
        <div class="micro_detail">{{$data->ls_description}}</div>
    </div>
</div>
@endif
@if (stripos($data->ls_name, "meta_cognitive") !== false)
@if (stripos($data->ls_name, "remembering") !== false)
<div class="learn_outer lg_header_border">
    <div class="learn_inner lg_header_font">
        <p>
            <img src="{{ asset('frontend/images/Metacognitive.png')}}" alt=""><span>Meta-Cognitive</span>
        </p>
        <button class="micro_detail_button" data-idaa="meta_co_data">More</button>
        <div class="micro_detail meta_co_data" >Knowledge of cognition - the mental process of acquiring knowledge and understanding through thought, experience, and the senses in general, as well as awareness and knowledge of one's own cognition.</div>
    </div>
</div>
@endif
<div class="learn_outer meta_co_data"  style="display:none;">
    <div class="graph_outer <?php if (($data->interpretationrange) == 'Low') {echo 'graph_outer3';} else if (($data->interpretationrange) == 'Medium') {echo 'graph_outer1';} else if (($data->interpretationrange) == 'High' ) {echo 'graph_outer2';} else { echo 'graph_outer4';}?>" style="width: <?php if ($data->totalAttemptedP != 0) { echo ($data->totalAttemptedP+50)."%"; } else {echo "50%";}?>">
    </div>
    <div class="learn_inner">
        <p>
            <img src="{{$data->ls_image}}" alt=""><span><?php $data->ls_name = str_replace('meta_cognitive',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}} </span>
        </p>
        <button class="micro_detail_button micro_detail_more">More</button>
        <div class="micro_detail">{{$data->ls_description}}</div>
    </div>
</div>
@endif
@endforeach
@else
No data found
@endif

