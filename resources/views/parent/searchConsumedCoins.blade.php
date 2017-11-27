<div class="table_container consumed_coin1 consumed_coin_data">
  <table class="sponsor_table">
      <tr>
          <th>{{trans('labels.component')}}</th>
          <th>{{trans('labels.profession')}}</th>
          <th>{{trans('labels.consumedcoins')}}</th>
          <th>{{trans('labels.consumedcoinsdate')}}</th>
          <th>{{trans('labels.enddate')}}</th>
      </tr>
      @if(!empty($deductedCoinsDetail) && count($deductedCoinsDetail) > 0)
      @foreach($deductedCoinsDetail as $key=>$data)
      <tr>
          <td>
              {{$data->pc_element_name}}
          </td>
          <td class="coin_pf_display">
              @if ($data->pf_name == '')
                  -
              @else
                  {{$data->pf_name}}
              @endif
          </td>
          <td>
              <?php echo number_format($data->dc_total_coins); ?>
          </td>
          <td>
              <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
          </td>
          <td>
              <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
          </td>
      </tr>
      @endforeach
      @else
      <tr><td colspan="5">No data found</td></tr>
      @endif
       <tr>
          <td colspan="5">
              @if (isset($deductedCoinsDetail) && !empty($deductedCoinsDetail))
                    <?php echo $deductedCoinsDetail->render(); ?>
              @endif
          </td>
      </tr>
  </table>
</div>
<div class="table_container consumed_coin3 consumed_coin_data">
    <table class="sponsor_table">
        <tr>
            <th>{{trans('labels.component')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
        @if(!empty($deductedCoinsDetailLS) && count($deductedCoinsDetailLS) > 0)
        @foreach($deductedCoinsDetailLS as $key=>$data)
        <tr>
            <td>
                {{$data->pc_element_name}}
            </td>
            <td>
                <?php echo number_format($data->dc_total_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="4">No data found</td></tr>
        @endif
    </table>
</div>
<div class="table_container consumed_coin2 consumed_coin_data">
    <table class="sponsor_table">
        <tr>
            <th>{{trans('labels.profession')}}</th>
            <th>{{trans('labels.concept')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
        @if(!empty($deductedTemplateCoinsDetail) && count($deductedTemplateCoinsDetail) > 0)
        @foreach($deductedTemplateCoinsDetail as $key=>$value)
        <tr>
            <td class="coin_pf_display">
                @if ($value->pf_name == '')
                    -
                @else
                    {{$value->pf_name}}
                @endif
            </td>
            <td>
                {{$value->gt_template_title}}
            </td>
            <td>
                <?php echo number_format($value->tdc_total_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($value->tdc_start_date)); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($value->tdc_end_date)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="5">No data found</td></tr>
        @endif
        <tr>
        <td colspan="5">
            @if (isset($deductedTemplateCoinsDetail) && !empty($deductedTemplateCoinsDetail))
                  <?php echo $deductedTemplateCoinsDetail->render(); ?>
            @endif
        </td>
    </tr>
    </table>
</div>