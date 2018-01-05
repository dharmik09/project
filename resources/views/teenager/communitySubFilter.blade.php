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
    @elseif ($filterOption == 't_age') 
    <label class="remove-sub-filter">Filter by:</label>
    <div class="form-group custom-select remove-sub-filter">
        <select id="sub_filter" tabindex="1" class="form-control">
            <option value="">Select</option>
            @foreach ($filterData as $age)
                <option value="{{ $age }}">{{ $age }}</option>
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
@elseif (empty($filterData) && $filterOption == 't_pincode')
    <div class="form-group search-bar clearfix remove-sub-filter">
        <input type="text" id="search_pincode" name="search_pincode" placeholder="search" tabindex="1" class="form-control search-feild pincode_div">
    </div>
@endif
