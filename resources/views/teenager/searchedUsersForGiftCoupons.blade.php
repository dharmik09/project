<div id="loading-wrapper-sub" class="loading-screen">
    
    <div id="loading-content">
    </div>
</div>
<table class="table table-hover">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($activeTeenagers) && count($activeTeenagers) > 0)
        @foreach($activeTeenagers as $key=>$data)
        <tr>
            <td>
                <?php $teenImage = Helpers::getTeenagerImageUrl($data->t_photo, 'thumb'); ?>
                <img src="{{ $teenImage }}" alt="{{ $data->t_photo }}" height="45px" width="45px">
            </td>
            <td>
                {{$data->t_name}}
            </td>
            <td>
                <button class="btn btn-gift" onclick="consumeCoupon({{$coupon_id}}, '{{$data->t_email}}', 'gift');">Gift</button>
            </td>
        </tr>
        @endforeach
        @else
        <div class="no-data">
            <div class="data-content">
                <div>
                    <i class="icon-empty-folder"></i>
                </div>
                <p>No data found</p>
            </div>
        </div>
        @endif
        <tr>
        <td colspan="3">
            @if (isset($activeTeenagers) && !empty($activeTeenagers))
                  <?php echo $activeTeenagers->render(); ?>
            @endif
        </td>
    </tr>
    </tbody>
</table>