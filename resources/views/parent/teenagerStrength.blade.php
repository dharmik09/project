@if(isset($teenagerStrength) && count($teenagerStrength) > 0)
<div class="intrest_content content_secondary close_load">
    <div class="row flex-container">
    @foreach($teenagerStrength as $miKey => $miVal)
        <?php
            if($miVal['scale'] == 'H'){
                $progressClass = 'progress-strong';
            }
            elseif($miVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
            }
            elseif($miVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
            }else{
                $progressClass = '';
            }

            if ($miVal['parentScale'] == 'H'){
                $scaleClass = 'strength-high';
            } else if ($miVal['parentScale'] == 'M'){
                $scaleClass = 'strength-moderate';
            } else if ($miVal['parentScale'] == 'L'){
                $scaleClass = 'strength-low';
            } else {
                $scaleClass = '';
            }
        ?>
        <?php if(strpos($miVal['slug'], 'mit_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$miVal['parentScale']}}</span>
                </div>
                <h4><a href="{{ url('multi-intelligence') }}/{{$miVal['type']}}/{{$miVal['slug']}}">{{$miVal['name']}}</a></h4>
            </div>
        </div>
        <?php } ?>
    @endforeach
    </div>
    <div class="parent_h2_header">
        <h2 class="parent_h2_text">Aptitude</h2>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content"><span class="text">PROMISE Assessment</span>
            <div class="data"><span class="small-box circle-box career-data-color-1"></span><span>High</span></div>
            <div class="data"><span class="small-box circle-box career-data-color-2"></span><span>Moderate</span></div>
            <div class="data"><span class="small-box circle-box career-data-color-3"></span><span>Low</span></div>
        </div>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content"><span class="text">Your Assessment</span>
            <div class="data"><span class="small-box career-data-color-1"></span><span>High</span></div>
            <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
            <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
        </div>
    </div>
    <div class="row flex-container">
    @foreach($teenagerStrength as $aptKey => $aptVal)
    <?php
            if($aptVal['scale'] == 'H'){
                $progressClass = 'progress-strong';
            }
            elseif($aptVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
            }
            elseif($aptVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
            }else{
                $progressClass = '';
            }

            if ($aptVal['parentScale'] == 'H'){
                $scaleClass = 'strength-high';
            } else if ($aptVal['parentScale'] == 'M'){
                $scaleClass = 'strength-moderate';
            } else if ($aptVal['parentScale'] == 'L'){
                $scaleClass = 'strength-low';
            } else {
                $scaleClass = '';
            }
        ?>
        <?php if(strpos($aptVal['slug'], 'apt_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$aptVal['parentScale']}}</span>
                </div>
                <h4><a href="{{ url('multi-intelligence') }}/{{$aptVal['type']}}/{{$aptVal['slug']}}">{{$aptVal['name']}}</a></h4>
            </div>
        </div>
        <?php } ?>
    @endforeach
    </div>
    <div class="parent_h2_header">
        <h2 class="parent_h2_text">Personality</h2>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content"><span class="text">PROMISE Assessment</span>
            <div class="data"><span class="small-box circle-box career-data-color-1"></span><span>High</span></div>
            <div class="data"><span class="small-box circle-box career-data-color-2"></span><span>Moderate</span></div>
            <div class="data"><span class="small-box circle-box career-data-color-3"></span><span>Low</span></div>
        </div>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content"><span class="text">Your Assessment</span>
            <div class="data"><span class="small-box career-data-color-1"></span><span>High</span></div>
            <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
            <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
        </div>
    </div>
    <div class="row flex-container">
    @foreach($teenagerStrength as $ptKey => $ptVal)
    <?php
            if($ptVal['scale'] == 'H'){
                $progressClass = 'progress-strong';
            }
            elseif($ptVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
            }
            elseif($ptVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
            }else{
                $progressClass = '';
            }

            if ($ptVal['parentScale'] == 'H'){
                $scaleClass = 'strength-high';
            } else if ($ptVal['parentScale'] == 'M'){
                $scaleClass = 'strength-moderate';
            } else if ($ptVal['parentScale'] == 'L'){
                $scaleClass = 'strength-low';
            } else {
                $scaleClass = '';
            }
        ?>
        <?php if(substr($ptVal['slug'], 0, 3) === 'pt_' && strpos($ptVal['slug'], 'pt_') !== false ) {  ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$ptVal['parentScale']}}</span>
                </div>
                <h4><a href="{{ url('multi-intelligence') }}/{{$ptVal['type']}}/{{$ptVal['slug']}}">{{$ptVal['name']}}</a></h4>
            </div>
        </div>
        <?php } ?>
    @endforeach
    </div>
</div>
<a href="javascript:void(0)" class="load_more skill_load">
   <!-- <img src="{{Storage::url('frontend/images/load_more.png')}}" alt="" class="">-->
    <span>Show More</span>
</a>
@else
<div class="no_data">No data found</div>
@endif