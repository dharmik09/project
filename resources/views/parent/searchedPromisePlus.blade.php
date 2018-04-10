<table class="table table-hover">
    <thead>
        <tr>
            <th>{{trans('labels.component')}}</th>
            <th>{{trans('labels.profession')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
    </thead>
    <tbody>
        <div id="loader-promise-plus" class="loading-screen loading-wrapper-sub">
            <div id="loading-content">
                <img src="{{ Storage::url('img/Bars.gif') }}">
            </div>
        </div>
        @if(!empty($deductedCoinsDetail) && count($deductedCoinsDetail) > 0)
        @foreach($deductedCoinsDetail as $key=>$data)
        <tr>
            <td>
                {{$data->pc_element_name}}
            </td>
            <td>
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
            <td colspan="8" class="promise-plus-data">
                @if (isset($deductedCoinsDetail) && !empty($deductedCoinsDetail))
                      {{ $deductedCoinsDetail->appends(['tab' => 'promise_plus'])->render() }}
                @endif
            </td>
        </tr>
    </tbody>
</table>
