<table class="table table-hover">
    <thead>
        <tr>
            <th>{{trans('labels.profession')}}</th>
            <th>{{trans('labels.concept')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
    </thead>
    <tbody>
        <div id="loader-l4concept-template" class="loading-screen loading-wrapper-sub">            
            <div id="loading-content">
                <img src="{{ Storage::url("img/Bars.gif") }}">
            </div>
        </div>
        @if(!empty($deductedTemplateCoinsDetail) && count($deductedTemplateCoinsDetail) > 0)
        @foreach($deductedTemplateCoinsDetail as $key=>$value)
        <tr>
            <td>
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
        <div class="no-data">
            <div class="data-content">
                <div>
                    <i class="icon-empty-folder"></i>
                </div>
                <p>No data found</p>
            </div>
            <div class="sec-bttm"></div>
        </div>
        @endif
        <tr>
            <td colspan="5">
                @if (isset($deductedTemplateCoinsDetail) && !empty($deductedTemplateCoinsDetail))
                    <?php echo $deductedTemplateCoinsDetail->appends(['tab' => 'l4_concept_template'])->render(); ?>
                @endif
            </td>
        </tr>
    </tbody>
</table>