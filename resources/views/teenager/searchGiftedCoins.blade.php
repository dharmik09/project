<table class="table table-hover">
    <thead>
        <tr>
            <th>{{trans('labels.formlblimage')}}</th>
            <th>{{trans('labels.teentblheadname')}}</th>
            <th>{{trans('labels.availablecoins')}}</th>
            <th>Gift ProCoins</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($activeTeenagers) && count($activeTeenagers) > 0)
        @foreach($activeTeenagers as $key => $data)
        <?php $teenImage = Helpers::getTeenagerImageUrl($data->t_photo, 'thumb'); ?>
        <tr>
            <td><img src="{{$teenImage}}" alt="user_default" style="width:60px;display:inline-block;vertical-align:middle;"></td>
            <td>{{$data->t_name}}</td>
            <td><?php echo number_format($data->t_coins); ?></td>
            <td><input type="text" placeholder="Enter Procoins" class="procoins-amt form-control"><a href="javascript:void(0)" title="gift" class="btn btn-default gft-btn">Gift</a></td>
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
        @if (isset($activeTeenagers) && !empty($activeTeenagers))
        <tr>
            <td colspan="4">
                  <?php echo $activeTeenagers->render(); ?>
            </td>
        </tr>
        @endif
    </tbody>
</table>