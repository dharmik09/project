@if (isset($filterData) && !empty($filterData))
    @if ($filterOption == 't_school')
    <label class="remove-sub-filter">Filter by:</label>
    <div class="form-group custom-select remove-sub-filter">
        <select id="sub_filter" tabindex="1" class="form-control">
            <option value="">Select</option>
            @foreach ($filterData as $data)
                <option value="{{ $data->school_id }}">{{ $data->sc_name }}</option>
            @endforeach
        </select>
    </div>
    @else 
    <label class="remove-sub-filter">Filter by:</label>
    <div class="form-group custom-select remove-sub-filter">
        <select id="sub_filter" tabindex="1" class="form-control">
            <option value="">Select</option>
            @foreach ($filterData as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    @endif
@endif
