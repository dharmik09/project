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

        
    </div>

    <div class="dashboard_edu_text">
        <a href="javascript:void(0);" onclick="getProfessionEducationPath({{$response['professionId']}})" class="btn primary_btn">Education Path</a>
    </div>

   
</div>

<div class="back_page first_page">
    <!-- <div class="loader card_loader cst_{{$response['professionId']}} init_loader">
        <div class="cont_loader">
            <div class="img1"></div>
            <div class="img2"></div>
        </div>
    </div> -->
    <div id="" class="cst_{{$response['professionId']}} loading-screen loading-wrapper-sub intermediate-first-question-loader" style="display:none;">
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

