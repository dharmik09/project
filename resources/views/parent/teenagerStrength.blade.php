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
        ?>
        <?php if(strpos($miVal['slug'], 'mit_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-{{$miVal['score']}} {{$progressClass}}">
                    
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
        ?>
        <?php if(strpos($aptVal['slug'], 'apt_') !== false ) { ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-{{$aptVal['score']}} {{$progressClass}}">
                    
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
        ?>
        <?php if(substr($ptVal['slug'], 0, 3) === 'pt_' && strpos($ptVal['slug'], 'pt_') !== false ) {  ?>
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-{{$ptVal['score']}} {{$progressClass}}">
                    
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