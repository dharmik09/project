@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
                <a href="{{url('parent/my-challengers')}}" class="back_me"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Back</span></a>
                <?php
                if (!empty($response['data']['profession_name'])) {
                    ?>
                    <div id="changeOnResponse">

                        <div class="accordion_video">
                            <h2>{{$response['data']['profession_name'] or ''}}</h2>
                            @if(isset($response['data']['video_url']) && $response['data']['video_url'] != '')
                            <?php $videoCode = Helpers::youtube_id_from_url($response['data']['video_url']);?>
                            @if($videoCode == '')
                            <div class="basket_iframe_video">
                                <video oncontextmenu="return false;"  class="non_youtube_video" controls>
                                        <!-- MP4 must be first for iPad! -->
                                        <source src="{{$response['data']['video_url']}}" type="video/mp4"  /><!-- Safari / iOS, IE9 -->
                                </video>
                            </div>
                            @else
                            <div class="basket_iframe_video">
                                <div id="ytplayer"></div>
                            </div>
                            @endif

                            @endif
                        </div>

                        <div class="accordion_content">
                            <div class="accordion_inner">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Job & Workplace</a>
                                            </h4>
                                        </div><!-- panel-heading End -->
                                        <div id="collapseOne" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                {!! $response['data']['job_workplace'] or 'No Any Info Available Yet' !!}
                                            </div><!-- panel-body End -->
                                        </div><!-- panel-collapse collapse End -->
                                    </div><!-- panel panel-default End -->
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Skill & Personality</a>
                                            </h4>
                                        </div><!-- panel-heading End -->
                                        <div id="collapseTwo" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                {!! $response['data']['skill_personality'] or 'No Any Info Available Yet' !!}
                                            </div><!-- panel-body End -->
                                        </div><!-- panel-collapse collapse End -->
                                    </div><!-- panel panel-default End -->
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Path & Growth</a>
                                            </h4>
                                        </div><!-- panel-heading End -->
                                        <div id="collapseThree" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                {!! $response['data']['path_growth'] or 'No Any Info Available Yet' !!}
                                            </div><!-- panel-body End -->
                                        </div><!-- panel-collapse collapse End -->
                                    </div><!-- panel panel-default End -->
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" title="You are now being redirected to an external link. We do not take responsibility for the accuracy of the contents in those sites. The hyperlink given to external sites do not constitute an endorsement of information, products or services offered by these websites." data-parent="#accordion" href="#collapseFour">Trends & Info Links</a>
                                            </h4>
                                        </div><!-- panel-heading End -->
                                        <div id="collapseFour" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                {!! $response['data']['trends_infolinks'] or 'No Any Info Available Yet' !!}
                                            </div><!-- panel-body End -->
                                        </div><!-- panel-collapse collapse End -->
                                    </div><!-- panel panel-default End -->
                                </div><!-- accordion_inner End -->
                            </div><!-- accordion_content End -->
                        </div>
                    </div>


                    <?php
                } else {
                    ?>
                    <div class="no_data_page">
                        <span class="nodata_outer">
                            <span class="nodata_middle">
                                No Any Professions Available Yet...
                            </span>
                        </span>
                    </div>
                    <?php
                }
                ?>
                </div>
            </div>
        </div>

        <?php
        $hint = Helpers::getHint('profession-detail', $professionId);
        $hintArray = $hint->toArray();
        shuffle($hintArray);
        $hintImagePath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        ?>
        @if(!empty($hint->toArray()))
        <div class="proteen_hint">
            <div class="container">
                <div class="hero_div clearfix">
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                        <p class="hint_bottom">&nbsp;</p>
                    </div>
                    <span class="hero">
                        <span class="hero_outer">
                            <span class="hero_inner">
                                <img src="{{ Storage::url($hintImagePath.$hint[0]->hint_image)}}" alt="">
                            </span>
                        </span>

                    </span>
                </div>
            </div>
        </div>
        @else
        <div class="proteen_hint hide_bg_hint"></div>
        @endif

        <div class="loader ajax-loader" style="display:none;">
            <div class="cont_loader">
                <div class="img1"></div>
                <div class="img2"></div>
            </div>
        </div>
</div>

@stop

@section('script')

<script>
    jQuery(document).ready(function($) {
        $('.panel-body').mCustomScrollbar();
    });

    var videocode = '<?php echo isset($response['data']['video_url']) && ($response['data']['video_url']) ? Helpers::youtube_id_from_url($response['data']['video_url']) : ''; ?>';

    if (videocode != '')
    {
        var isyoutube = 1;
        var tag = document.createElement('script');

        tag.src = "https://www.youtube.com/player_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        function onYouTubePlayerAPIReady() {
            player = new YT.Player('ytplayer', {
                height: '',
                width: '',
                videoId: videocode,
                playerVars: {rel: 0, showinfo: 1},
                events: {
                    'onStateChange': onPlayerStateChange,
                    onReady: onPlayerReady
                }
            });
        }
        function onPlayerReady(event) {
            event.target.setVolume(50);
        }
        function onPlayerStateChange(event)
        {
            if (event.data == YT.PlayerState.ENDED) {
                //Save bboster points for video
                saveBoosterPoints(teenagerId, professionId, 1,isyoutube);
            }
        }
    }else{
        var isyoutube = 0;
    }
</script>
@stop
