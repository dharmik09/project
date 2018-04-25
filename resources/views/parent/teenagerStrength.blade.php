@if(isset($teenagerStrength) && count($teenagerStrength) > 0)
<div class="intrest_content content_secondary close_load">
    <div class="row flex-container">
    @foreach($teenagerStrength as $miKey => $miVal)
        <?php
            if($miVal['scale'] == 'H'){
                $progressClass = 'progress-strong';
                $scaleClass = 'strength-high';
            }
            elseif($miVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
                $scaleClass = 'strength-moderate';
            }
            elseif($miVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
                $scaleClass = 'strength-low';
            }else{
                $progressClass = '';
                $scaleClass = '';
            }
        ?>
        <?php if(strpos($miVal['slug'], 'mit_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$miVal['scale']}}</span>
                </div>
                <h4>{{$miVal['name']}}</h4>
            </div>
        </div>
        <?php } ?>
    @endforeach
    </div>
    <div class="parent_h2_header">
        <h2 class="parent_h2_text">Aptitude</h2>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content">
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
                $scaleClass = 'strength-high';
            }
            elseif($aptVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
                $scaleClass = 'strength-moderate';
            }
            elseif($aptVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
                $scaleClass = 'strength-low';
            }else{
                $progressClass = '';
                $scaleClass = '';
            }
        ?>
        <?php if(strpos($aptVal['slug'], 'apt_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$aptVal['scale']}}</span>
                </div>
                <h4>{{$aptVal['name']}}</h4>
            </div>
        </div>
        <?php } ?>
    @endforeach
    </div>
    <div class="parent_h2_header">
        <h2 class="parent_h2_text">Personality</h2>
    </div>
    <div class="data-explainations clearfix text-center data-dashboard">
        <div class="content">
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
                $scaleClass = 'strength-high';
            }
            elseif($ptVal['scale'] == 'M'){
                $progressClass = 'progress-potential';
                $scaleClass = 'strength-moderate';
            }
            elseif($ptVal['scale'] == 'L'){
                $progressClass = 'progress-unlikely';
                $scaleClass = 'strength-low';
            }else{
                $progressClass = '';
                $scaleClass = '';
            }
        ?>
        <?php if(substr($ptVal['slug'], 0, 3) === 'pt_' && strpos($ptVal['slug'], 'pt_') !== false ) {  ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100 {{$progressClass}}">
                    <span class="{{$scaleClass}}">{{$ptVal['scale']}}</span>
                </div>
                <h4>{{$ptVal['name']}}</h4>
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