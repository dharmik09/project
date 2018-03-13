@if (isset($teenagerMyIcons) && !empty($teenagerMyIcons))
<ul class="row owl-carousel">
    @forelse($teenagerMyIcons as $teenagerMyIcon)
    <li class="col-sm-3 col-xs-6">
        <figure>
            <div class="icon-img">
                <a href="javascript:void(0);" data-placement="bottom" title="{{ str_limit($teenagerMyIcon['iconDescription'], $limit = 100, $end = '...') }}" data-toggle="tooltip">
                    <img src="{{ $teenagerMyIcon['iconImage'] }}">
                </a>
            </div>
        </figure>
    </li>
    @empty
    You can vote your role models in the 'My Votes' section above
    @endforelse
</ul>
@else
    <h3>You can vote your role models in the 'My Votes' section above</h3>
@endif
