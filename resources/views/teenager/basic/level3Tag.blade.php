<div class="career-heading clearfix">
    <h4>Related careers:</h4>
    @if(count($professionsTagData->professionTags)>0)
    <div class="pull-right">
        <div class="sec-popup">
            <a href="#" class="custompop" rel="popover" data-popover-content="#pop2" data-placement="bottom">
                <i class="icon-share"></i>
            </a>
            <div class="hide" id="pop2">
                <div class="socialmedia-icon">
                    <p>Share  on:</p>
                    <ul class="social-icon clearfix">
                        <li><a href="#" title="facebook" class="facebook"><i class="icon-facebook"></i></a></li>
                        <li><a href="#" title="Twitter" class="twitter"><i class="icon-twitter"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <ul class="match-list">
        <li><span class="number match-strong">4</span> Strong match</li>
        <li><span class="number match-potential">5</span> Potential match</li>
        <li><span class="number match-unlikely">4</span> Unlikely match</li>
    </ul>
    @endif
</div>
<ul class="career-list">
    @if(count($professionsTagData->professionTags)>0)
        @foreach($professionsTagData->professionTags as $professionTags)
            
                <li class="match-strong complete-feild"><a href="{{url('teenager/career-detail/'.$professionTags->profession['pf_slug'])}}" title="{{$professionTags->profession['pf_name']}}">{{$professionTags->profession['pf_name']}}</a>
                    @if(count($professionTags->profession['professionAttempted'])>0)
                        <a href="#" class="complete"><span>Complete</span></a>
                    @endif
                </li>
            
        @endforeach
    @else
        <center><h3>Careers not available for this tag</h3></center>
    @endif
    <!-- <li class="match-potential"><a href="#" title="Purchasing Agents & Buyers">Purchasing Agents &amp; Buyers</a></li>
    <li class="match-potential"><a href="#" title="Rotary Drill Operators, Oil and Gas">Rotary Drill Operators, Oil and Gas</a></li>
    <li class="match-unlikely"><a href="#" title="Agricultural and Food Science Technicians">Agricultural and Food Science Technicians</a></li>
    <li class="match-strong complete-feild"><a href="#" title="Agricultural Equipment Operators">Agricultural Equipment Operators</a>
        <a href="#" class="complete"><span>Complete</span></a >
    </li>
    <li class="match-strong"><a href="#" title="Conservation Scientists">Conservation Scientists</a></li>
    <li class="match-potential"><a href="#" title="Environmental Engineers">Environmental Engineers</a></li> -->
</ul>