@if(isset($teenagerInterest) && count($teenagerInterest) > 0)
<div class="intrest_content content_prime close_load">
    <div class="row flex-container">
        @forelse($teenagerInterest as $interestKey => $interestValue)
        <div class="col-md-3 col-sm-3 col-xs-6 flex-items">
            <div class="my_chart">
                <div class="progress-radial progress-100}} progress-strong">
                    
                </div>
                <h4>{{ $interestValue['name']}}</h4>
            </div>
        </div>
        @empty
            <div class="col-md-12 col-sm-12 col-xs-12 flex-items">
                <center>
                    <h3>Build your profile to discover professions matching your interests!</h3>
                </center>
            </div>
        @endforelse
    </div>
</div>

    <a href="javascript:void(0)" class="load_more intrest_load">
        <span>Show More</span>
    </a>
@else
@endif
                 
                        
                    
