<div class="upload-screen quiz-box sec-show cost-estimator">
    @if(isset($professionDetail) && !empty($professionDetail))
    <ul id="activityTasks" class="nav nav-tabs">
        <li id="3" <?php if($typeId == 3 || empty($typeId)) { ?> class="active" <?php } ?> ><a data-toggle="tab" href="#home">Image</a></li>
        <li id="2" <?php if($typeId == 2) { ?> class="active" <?php } ?> ><a data-toggle="tab" href="#submenu1">Document</a></li>
        <li id="1" <?php if($typeId == 1) { ?> class="active" <?php } ?> ><a data-toggle="tab" href="#submenu2">Video</a></li>
    </ul>
    <div class="tab-content">
        <div id="home" class="tab-pane fade <?php if($typeId == 3 || empty($typeId)) { ?> in active <?php } ?>">
            @include('parent/basic/l4AdvanceActivityImageTask')
        </div>
        <div id="submenu1" class="tab-pane fade <?php if($typeId == 2 || empty($typeId)) { ?> in active <?php } ?>">
            @include('parent/basic/l4AdvanceActivityDocumentTask')
        </div>
        <div id="submenu2" class="tab-pane fade <?php if($typeId == 1 || empty($typeId)) { ?> in active <?php } ?>"">
            @include('parent/basic/l4AdvanceActivityVideoTask')
        </div>
    </div>
    @else
    @endif
</div>