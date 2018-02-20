<div class="upload-screen quiz-box sec-show cost-estimator">
    @if(isset($professionDetail) && !empty($professionDetail))
    <ul id="activityTasks" class="nav nav-tabs">
        <li id="3" class="active"><a data-toggle="tab" href="#home">Image</a></li>
        <li id="2"><a data-toggle="tab" href="#submenu1">Document</a></li>
        <li id="1"><a data-toggle="tab" href="#submenu2">Video</a></li>
    </ul>
    <div class="tab-content">
        <div id="home" class="tab-pane fade in active">
            @include('teenager/basic/l4AdvanceActivityImageTask')
        </div>
        <div id="submenu1" class="tab-pane fade">
            @include('teenager/basic/l4AdvanceActivityDocumentTask')
        </div>
        <div id="submenu2" class="tab-pane fade">
            @include('teenager/basic/l4AdvanceActivityVideoTask')
        </div>
    </div>
    @else
    @endif
</div>