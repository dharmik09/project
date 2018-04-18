@if ($basketDetails && count($basketDetails) > 0)
    @foreach ($basketDetails as $basket)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
            <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$basket->id}}" class="collapsed" onclick="fetchProfessionDetails({{$basket->id}})">{{$basket->b_name}}</a> 
            <span onclick="changePageLayout(1, {{$basket->id}});">
                <i class="icon-list"></i>
            </span>
            <a href="javascript:void(0);" onclick="changePageLayout(2, {{$basket->id}});" title="Careers" class="grid">
                <i class="icon-grid"></i>
            </a>
            </h4>
        </div>
        <div class="panel-collapse collapse <?php if (in_array($basket->id, $shownBasketId)) { ?> in <?php } ?> " id="accordion{{$basket->id}}">
            @include('teenager/basic/careerListGridSection')
        </div>
    </div>
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif

