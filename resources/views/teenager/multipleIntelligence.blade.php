@extends('layouts.teenager-master')

@push('script-header')
    <title>Multiple Intelligence : {{ $multipleIntelligence->title }}</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="inner-banner">
            <div class="container">
                <?php           
                    $getTeenagerHML = Helpers::getTeenagerMatchScale(Auth::guard('teenager')->user()->id);
                    $videoCode = Helpers::youtube_id_from_url($multipleIntelligence->video);
                    if ($videoCode != '') {
                        $videoId = $videoCode;
                    }
                    else
                    {
                        $videoId = 'WoelVRjFO4A';
                    }
                ?>
                <div class="sec-banner banner-landing" style="background-image: url('{{ Storage::url($miThumbImageUploadPath . $multipleIntelligence->logo) }}');">
                    <div class="container">
                        <div class="play-icon">
                            <a href="javascript:void(0);" class="play-btn" id="iframe-video-click">
                                <img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon">
                            </a>
                        </div>
                    </div>
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{$videoId}}?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
                </div>
            </div>
        </div>
        <!--introduction text-->
        <div class="container">
            <section class="introduction-text">
                <div class="heading-sec clearfix">
                    <h1>{{ $multipleIntelligence->title }}</h1>
                    <div class="sec-popup">
                        <a id="strength-details" href="javascript:void(0);" onmouseover="getHelpText('strength-details')" data-trigger="hover" data-popover-content="#strength-sec" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                        <div class="hide" id="strength-sec">
                            <div class="popover-data">
                                <a class="close popover-closer"><i class="icon-close"></i></a>
                                <span class="strength-details"></span>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="custompop" rel="popover" data-popover-content="#pop2" data-placement="bottom"><i class="icon-share"></i></a>
                        <div class="hide" id="pop2">
                            <div class="socialmedia-icon">
                                <p>Share  on:</p>
                                <ul class="social-icon clearfix">
                                    <li><a href="#" title="facebook" class="facebook"><i class="icon-facebook"></i></a></li>
                                    <li><a href="#" title="Twitter" class="twitter"><i class="icon-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <p>{{ $multipleIntelligence->description }}</p>
            </section>
        </div>
        <!--introduction text end-->
        <!--related careers section-->
        <div class="related-careers">
            <div class="container">
                <div class="bg-white">
                    <div class="career-heading clearfix">
                        <h4>Related careers:</h4>
                        <div class="pull-right">
                            <div class="sec-popup">
                                 <a href="javascript:void(0);" class="custompop" rel="popover" data-popover-content="#pop3" data-placement="bottom"><i class="icon-share"></i></a>
                                <div class="hide" id="pop3">
                                    <div class="socialmedia-icon">
                                        <p>Share  on:</p>
                                        <ul class="social-icon clearfix">
                                            <li><a href="#" title="facebook" class="facebook"><i class="icon-facebook"></i></a></li>
                                            <li><a href="#" title="Twitter" class="twitter"><i class="icon-twitter"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="match-list paddng-right">
                            <li><span class="number match-strong"></span> Strong match</li>
                            <li><span class="number match-potential"></span> Potential match</li>
                            <li><span class="number match-unlikely"></span> Unlikely match</li>
                        </ul>
                    </div>
                    <div class="new-career">
                        @include('teenager/relatedCareers')
                    </div>
                </div>
            </div>

        </div>
        <!--</div>-->
        <!--related careers section end-->
        <!--team section-->
        <section class="sec-team">
            <div class="container">
                <div class="bg-white new-gurus userData">
                    <h4>Meet the {{ $multipleIntelligence->title }} gurus:</h4>
                    @include('teenager/listingGurus')
                </div>
            </div>
        </section>
        <!--team section end-->
        <!-- mid section end-->
    </div>
@stop
@section('script')
    <script>
        $('.play-icon').click(function() {
            $(this).hide();
            $('iframe').show();
        });
        $('#iframe-video-click').on('click', function(ev) {            
            $('img').hide();
            $('iframe').show();
            $("#iframe-video")[0].src += "&autoplay=1";
            ev.preventDefault();                       
        });
        $(document).on('click','#see-more',function(){
            $(".new-career .loader_con").show();
            var lastCareerId = $(this).data('id');
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var slug = '{{$multipleIntelligence->slug}}';
            var form_data = 'lastCareerId=' + lastCareerId + '&slug=' + slug;
            $.ajax({
                url : '{{ url("teenager/see-more-related-careers") }}',
                method : "POST",
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                dataType : "text",
                success : function (data) {
                    $(".new-career .loader_con").hide();
                    if(data != '') {
                        $('.new-career .remove-row').remove();
                        $('.new-career').append(data);
                    }
                }
            });
        });
        var slotCount = 1;
        $(document).on('click','#see-more-guru', function(){
            $(".new-gurus .loader_con").show();
            var slot = slotCount++;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var slug = '{{$multipleIntelligence->slug}}';
            var form_data = 'slot=' + slot + '&slug=' + slug;
            $.ajax({
                url : '{{ url("teenager/see-more-gurus") }}',
                method : "POST",
                data: form_data,
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                dataType : "text",
                success : function (data) {
                    $(".new-gurus .loader_con").hide();
                    if(data != '') {
                        $('.new-gurus .remove-row').remove();
                        $('.new-gurus').append(data);
                    } 
                }
            });
        });
    </script>
@endsection
    