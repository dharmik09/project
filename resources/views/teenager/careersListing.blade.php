@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
@endpush

@section('content')

<div class="bg-offwhite">
<!-- mid section starts-->
<div class="container">
    <div class="careers-list">
        <div class="top-heading text-center listing-heading">
            <h1>careers</h1>
            <p>You have completed <strong class="font-blue">212 of 550</strong> careers</p>
        </div>
        <div class="sec-filter listing-filter">
            <div class="row">
                <div class="col-md-2 text-right"><span>Filter by:</span></div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group custom-select"><select tabindex="8" class="form-control"><option value="all categories">all categories</option><option value="Strong match">Strong match</option><option value="Potential match">Potential match</option><option value="Unlikely match">Unlikely match</option></select></div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="form-group custom-select bg-blue"><select tabindex="8" class="form-control"><option value="all careers">all careers</option><option value="agriculture">agriculture</option><option value="conservation">conservation</option><option value="Veterinarians">Veterinarians</option></select></div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group search-bar clearfix"><input type="text" placeholder="search" tabindex="1" class="form-control search-feild"><button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button></div>
                </div>
            </div>
        </div>
        <!-- mid section-->
        <section class="career-content listing-content">
            <div class="bg-white">
                <div class="panel-group" id="accordion">
                    @forelse($basketsData as $key => $value)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$value->id}}" id="{{$value->id}}" onclick="fetchProfessionData(this.id)"class="collapsed">{{$value->b_name}}</a> <a href="{{ url('teenager/career-grid') }}" title="Grid view" class="grid"><i class="icon-grid"></i></a></h4>
                        </div>
                        <div class="panel-collapse collapse" id="accordion{{$value->id}}">
                            <div id="profession{{$value->id}}"></div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </section>
        <!-- mid section end-->
    </div>
</div>
</div>

@stop

@section('script')
<script>
    $('.play-icon').click(function() {
        $(this).hide();
        $('iframe').show();
    })

    function fetchProfessionData(id) {

        if ( !$("#profession"+id).hasClass( "dataLoaded" ) ) {

            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/career')}}",
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'basket_id':id},
                success: function (response) {
                    $("#profession"+id).html(response);
                    $("#profession"+id).addClass("dataLoaded");
                }
            });
        }
    }

</script>
@stop