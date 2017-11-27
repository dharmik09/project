<div class="table_container" >
    <table class="sponsor_table">
        <tr>
            <th>{{trans('labels.blheadgiftedto')}}</th>
            <th>{{trans('labels.giftedcoins')}}</th>
            <th>{{trans('labels.gifteddate')}}</th>
        </tr>
        @if(!empty($teenCoinsDetail) && count($teenCoinsDetail) > 0)
        @foreach($teenCoinsDetail as $key=>$data)
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
        @else
        <tr><td colspan="3">{{trans('labels.teenpair')}}</td></tr>
        @endif
        <tr>
            <td colspan="3">
                @if (isset($teenCoinsDetail) && !empty($teenCoinsDetail))
                      <?php echo $teenCoinsDetail->render(); ?>
                @endif
            </td>
        </tr>
    </table>
</div>