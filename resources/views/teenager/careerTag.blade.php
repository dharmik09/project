@extends('layouts.teenager-master')

@push('script-header')
    <title>Keyword Tag</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="inner-banner">
            <div class="container">
                <div class="sec-banner tag-banner" style="background-image:url({{Storage::url(Config::get('constant.PROFESSION_TAG_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsTagData->pt_image)}})">
                    <!-- -->
                </div>
            </div>
        </div>
        <!--introduction text-->
        <div class="container">
            <section class="introduction-text tag-text">
                <div class="heading-sec clearfix">
                    <h1>{{$professionsTagData->pt_name}}</h1>
                    <div class="sec-popup">
                        <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom">
                            <i class="icon-question"></i>
                        </a>
                        <div class="hide" id="pop1">
                            <div class="popover-data">
                                <a class="close popover-closer"><i class="icon-close"></i></a>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                            </div>
                        </div>
                    </div>
                </div>
                {!!html_entity_decode($professionsTagData->pt_description)!!}
            </section>
        </div>
        <!--introduction text end-->
        <!--related careers section-->
        <div class="related-careers careers-tag">
            <div class="container">
                <div class="bg-white">
                    <div id="related-careers"></div>
                </div>
            </div>
        </div>
        <!--related careers section end-->
        <!-- mid section end-->
    </div>
@stop
@section('script')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        fetchTagRelatedProfession();
    });
    function fetchTagRelatedProfession() {
        $("#related-careers").html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen bg-offwhite"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
        $("#related-careers").addClass('loading-screen-parent loading-large');

        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/tag-related-careers')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'slug':'<?php echo $slug;?>'},
            success: function (response) {
                $("#related-careers").removeClass('loading-screen-parent loading-large');
                $("#related-careers").html(response);
            }
        });
    }
</script>
@stop