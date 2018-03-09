@extends('layouts.home-master')

@push('script-header')
    <title>Seo teasor</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <!-- mid section-->
        <div class="container">
            <section class="career-detail">
                <form>
                    <div class="col-sm-6">
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search career..." value="" tabindex="1" id="autocomplete" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </div>
                   
                    <div class="col-sm-6">
                        <div class="btn-seo">
                            <a class="btn btn-primary" href="{{ url('/teenager') }}">Did not find what you are looking for ? Sign-in to let us know and win ProCoins!</a>
                        </div>
                    </div>
                </form>
               
            </section>
        </div>
        <!-- mid section end-->
    </div>
@stop
@section('script')

<script src="{{ asset('frontend/js/jquery.autocomplete.min.js') }}"></script>

<?php
$finalSearchArray = '';
$suggestion = '';
if (!empty($allProfessions)) {
    foreach ($allProfessions as $value) {
        $searchArray[] = array('value' => $value->pf_name, 'slug' => $value->pf_slug);
    }
    $finalSearchArray = json_encode($searchArray);
}
?>

<script>
    $(window).bind("load", function() {
    var currencies = <?php echo $finalSearchArray ?>
        // setup autocomplete function pulling from currencies[] array
        $('#autocomplete').autocomplete({
            lookup: currencies,
            onSelect: function(suggestion) {
                window.location.href = "<?php echo url('career-detail/') ?>/" + suggestion.slug;
            }
        });
    });
</script>
@stop