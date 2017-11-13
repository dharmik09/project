@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        Icons Report
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="box box-info">
            <form action="{{url('admin/iconReport/')}}" method="post" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
                    @include('admin/ChartType')
                    @include('admin/GenderType')
                    <?php $gender = (isset($gender)) ? $gender : '';
                        if ($gender == 1) {
                            $gender = "Male";
                        } else if ($gender == 2) {
                            $gender = "Female";
                        }?>
                    <div class="form-group">
                        <label for="basket" class="col-sm-2 control-label">Select All Icon's</label>
                        <div class="col-sm-3">
                            <select id="category_type" name="category_type" class="form-control">
                                <option value="">Select</option>
                                <option value="1" <?php if (isset($category_type) && $category_type == 1) { echo "selected='selected'";}?>>Fiction</option>
                                <option value="2" <?php if (isset($category_type) && $category_type == 2) { echo "selected='selected'";}?>>Non-Fiction</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <button id="search" type="submit" class="btn btn-primary btn-flat">{{trans('labels.search')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if(isset($topAllSelectedIcons) && !empty($topAllSelectedIcons))
    <div class="row">
        <div class="box box-info">
            <div class="box-body">
            <table class="table table-striped">
                        <tr><th colspan="4">Total : {{count($topAllSelectedIcons)}}</th></tr>
                        <tr>
                            <th>Icon name</th>
                            <th>Category</th>
                            <th>Vote Count</th>
                            <th>Image</th>
                        </tr>
                        @forelse($topAllSelectedIcons as $icon)
                         <tr>
                            <td>
                                {{($category_type == 2)?$icon->hi_name:$icon->ci_name}}
                            </td>
                            <td>
                                {{($category_type == 2)?$icon->hic_name:$icon->cic_name}}
                            </td>
                            <td>
                                {{$icon->timesused}}  @if ($gender != '') ({{$gender}}) @endif
                            </td>
                            <td>
                                @if($category_type == 1)
                                    <?php 
                                        $image = ($icon->ci_image != "" && Storage::disk('s3')->exists($cartoonThumbPath.$icon->ci_image)) ? Config::get('constant.DEFAULT_AWS').$cartoonThumbPath.$icon->ci_image : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                @else
                                    <?php 
                                        $image = ($icon->hi_image != "" && Storage::disk('s3')->exists($humanThumbPath.$icon->hi_image)) ? Config::get('constant.DEFAULT_AWS').$humanThumbPath.$icon->hi_image : asset('/backend/images/proteen_logo.png'); 
                                    ?>
                                    <img src="{{$image}}" class="user-image" alt="Default Image" height="{{ Config::get('constant.DEFAULT_IMAGE_HEIGHT') }}" width="{{ Config::get('constant.DEFAULT_IMAGE_WIDTH') }}">
                                @endif
                            </td>
                         </tr>
                         @empty
                         <tr>
                            <td colspan="2"><center>{{trans('labels.norecordfound')}}</center></td>
                         </tr>
                         @endforelse
                    </table>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="box box-info">
            <div class="box-body">
            <div class="col-md-12">
                <div id="highchart_human_icon">Chart Loads here...</div>
            </div>               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-body">                    
            <div class="col-md-12">
                <div id="highchart_cartoon_icon">Chart Loads here...</div>  
            </div>               
            </div>
        </div>
    </div>
</body>
</section>
   
@stop
@section('script')
<script src="{{ asset('backend/js/highchart.js')}}"></script>
<script src="{{ asset('backend/js/chosen.jquery.js')}}"></script>

<script type="text/javascript">
    
var config = {
    '.chosen-select': {},
    '.chosen-select-deselect': {allow_single_deselect: true},
    '.chosen-select-no-single': {disable_search_threshold: 10},
    '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
    '.chosen-select-width': {width: "95%"},
}
for (var selector in config) {
    $(selector).chosen(config[selector]);
}

var humanIconData = <?php echo $finalIconData['human']; ?>;
var cartoonIconData = <?php echo $finalIconData['cartoon']; ?>;
var chartType = '{{$chart}}';

 
loadChart(humanIconData,'Top Trending Non-Fiction Icon','highchart_human_icon'); 
loadChart(cartoonIconData,'Top Trending Fiction Icon','highchart_cartoon_icon'); 
                  
              
                  
function loadChart(chartData,lableText,loadDiv){    
    $('#'+loadDiv).highcharts({
        chart: {
            type: chartType,
        },
        title: {
            text: lableText
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'No of votes'
            },
            labels: {
                formatter: function () {
                    return this.value + "";
                }
            },
            lineWidth: 1           
        },
        legend: {
            enabled: false,
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: 'Vote Count({point.y})'
                }
            }
        },
        tooltip: {
            pointFormat: '<span>Category : <b>{point.iconcategory}</b></span><br/>Vote Count : <b>{point.y}</b><br/>'
        },
        series: [{
                colorByPoint: true,
                data: chartData
        }]
    });
}
                    
</script>

@stop    

