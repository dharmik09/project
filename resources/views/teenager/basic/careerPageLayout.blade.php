@if ($basketDetails && count($basketDetails) > 0)
    @foreach ($basketDetails as $basket)
    <?php if (count($basket->profession) == 0) { continue; } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
            <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$basket->id}}" class="collapsed" onclick="openProfessionTab({{$basket->id}})">{{$basket->b_name}}</a> 
            <span id="list-icon-{{$basket->id}}" onclick="changePageLayout(1, {{$basket->id}});" class="active">
                <i class="icon-list"></i>
            </span>
            <a id="grid-icon-{{$basket->id}}" href="javascript:void(0);" onclick="changePageLayout(2, {{$basket->id}});" title="Careers" class="grid">
                <i class="icon-grid"></i>
            </a>
            </h4>
        </div>
        <div class="panel-collapse collapse <?php if (in_array($basket->id, $shownBasketId)) { ?> in collapsedTab <?php } ?> " id="accordion{{$basket->id}}">
            @include('teenager/basic/careerListGridSection')
        </div>
    </div>
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif

