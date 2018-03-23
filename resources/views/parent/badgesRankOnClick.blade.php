<div class="front_page">
    <!-- <div class="loader card_loader {{$response['professionId']}} init_loader">
        <div class="cont_loader">
            <div class="img1"></div>
            <div class="img2"></div>
        </div>
    </div> -->

    <div class="{{$response['professionId']}} loading-screen loading-wrapper-sub" style="display:none;">
        <div class="loading-text">
            <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
        </div>
        <div class="loading-content"></div>
    </div>

    <div id="badges_rank_data_{{$response['professionId']}}">
        <span class="img_container">
            <img src="{{$response['profession_logo']}}" alt="">
        </span>
        <span class="title dashboard_profession">
            <span class="profession_text" style="font-size: 14px;" title="{{$response['professionName']}}">
                <span>{{$response['professionName']}}</span>
            </span>
        </span>

        <div class="rank_local">
            <span style="font-size: 14px;"><?php echo ($teenagerId == Auth::guard('parent')->user()->id)?"My":''; ?> Rank : {{$response['rank']}}</span>
            <span style="font-size: 14px;"><?php echo ($teenagerId == Auth::guard('parent')->user()->id)?"My":''; ?> Points : {{$response['yourscore']}}</span>
        </div>

        <div class="ahchivement">
            <?php
            if (isset($response['badges'][0]['newbie']) && $response['badges'][0]['newbie'] != '') {
                ?>
                <img src="{{$response['badges'][0]['newbie']}}" alt="" />
            <?php } else {
                ?>
                <img src="{{Storage::url('frontend/images/newbie_default.png')}}" alt="" />
                <?php
            }
            if (isset($response['badges'][0]['apprentice']) && $response['badges'][0]['apprentice'] != '') {
                ?>
                <img src="{{$response['badges'][0]['apprentice']}}" alt="" />
            <?php } else {
                ?>
                <img src="{{Storage::url('frontend/images/apprentice_default.png')}}" alt="" />
                <?php
            }
            if (isset($response['badges'][0]['wizard']) && $response['badges'][0]['wizard'] != '') {
                ?>
                <img src="{{$response['badges'][0]['wizard']}}" alt="" />
            <?php } else {
                ?>
                <img src="{{Storage::url('frontend/images/wizard_default.png')}}" alt="" />
            <?php } ?>
        </div>
    </div>

    <div class="dashboard_edu_text">
        <a href="javascript:void(0);" onclick="getProfessionEducationPath({{$response['professionId']}})" class="btn primary_btn">Education Path</a>
    </div>

    @if ($response['remainingDays'] == 0)
    <div id="days_{{$response['professionId']}}">
        <div class="promisebtn">
            <a href="javascript:void(0);" class="promise btn_golden_border" title="" onclick="getPromisePlus({{$response['professionId']}}, {{$response['teenagerId']}}, {{$response['remainingDays']}}, {{$response['required_coins']}});" data-ref="#{{$response['professionId']}}">
                <span class="promiseplus">PROMISE Plus</span>
                <span class="coinouter">
                    <span class="coinsnum">{{$response['required_coins']}}</span>
                    <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}">
                    </span>
                </span>
            </a>
        </div>
    </div>
    @else
        <div class="promisebtn timer_btn">
            <a href="javascript:void(0);" class="promise btn_golden_border" title="" onclick="getPromisePlus({{$response['professionId']}}, {{$response['teenagerId']}}, {{$response['remainingDays']}}, {{$response['required_coins']}});" data-ref="#{{$response['professionId']}}">
                <span class="promiseplus">PROMISE Plus</span>
                <span class="coinouter">
                    <span class="coinsnum">{{$response['remainingDays']}} Days Left</span>
                </span>
            </a>
        </div>
    @endif
</div>

<div class="back_page first_page">
    <!-- <div class="loader card_loader cst_{{$response['professionId']}} init_loader">
        <div class="cont_loader">
            <div class="img1"></div>
            <div class="img2"></div>
        </div>
    </div> -->
    <div id="" class="cst_{{$response['professionId']}} loading-screen loading-wrapper-sub intermediate-first-question-loader" style="display:none;">
        <div class="loading-text">
            <img src="{{ Storage::url('img/ProTeen_Loading_edit.gif') }}" alt="loader img">
        </div>
        <div class="loading-content"></div>
    </div>
    <div class="full_path">
        <div class="inner_path" id="education_path_{{$response['professionId']}}">
            <!--<div class="h2_container"><h2>Education Path</h2></div>-->

        </div>
    </div>
</div>

<div class="back_page second_page">
    <div class="full_path">
        <div class="inner_path">
            <!--<div class="h2_container"><h2>Education Path</h2></div>-->
            <span class="title dashboard_profession">
                <span class="profession_text" style="font-size: 14px;" title="Actors">
                    <span>{{$response['professionName']}}</span>
                </span>
            </span>
            <a href="javascript:void(0);" class="close_next" id="close_{{$response['professionId']}}"><i class="fa fa-times" aria-hidden="true"></i></a>
            <div class="userData" id="{{$response['professionId']}}">

            </div>
        </div>
    </div>
    <div class="loader card_loader {{$response['professionId']}}" style="display:none;" >
        <div class="cont_loader">
            <div class="img1"></div>
            <div class="img2"></div>
        </div>
    </div>
</div>

