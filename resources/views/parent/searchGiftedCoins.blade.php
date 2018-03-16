<table class="table table-hover">
    <thead>
        <tr>
            <th>{{trans('labels.blheadgiftedto')}}</th>
            <th>{{trans('labels.giftedcoins')}}</th>
            <th>{{trans('labels.gifteddate')}}</th>
        </tr>
    </thead>
    @if(!empty($parentCoinsDetail) && count($parentCoinsDetail) > 0)
    <tbody>
        @foreach($parentCoinsDetail as $key=>$data)
            <tr>
                <td>
                    {{$data->t_name}}
                </td>
                <td>
                    <?php echo number_format($data->tcg_total_coins); ?>
                </td>
                <td>
                    @if($data->tcg_gift_date != '')
                    <?php echo date('d M Y', strtotime($data->tcg_gift_date)); ?>
                    @else
                    -
                    @endif
                </td>
            </tr>

        @endforeach
        <tr>
            <td colspan="4">
                <?php echo $parentCoinsDetail->render(); ?>
            </td>
        </tr>
    </tbody>
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
</table>