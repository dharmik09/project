<div class="inner_container">
    <div class="landing_container">
        <div>
            <div class="col-md-6 col-sm-6 col-xs-6 borderright">
                <a href="<?php if($isQuestionCompleted == 0) { ?> {{ url('/teenager/playLevel1Activity') }} <?php } else { echo "javascript:void(0)"; }?>" class="landing_box landing_l1" onclick="checkLevel1Questions({{$isQuestionCompleted}});">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="vote"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Opinions</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 borderbottom">
                <a href="javascript:void(0);" onClick="playFirstLevelWorldType(1)" class="landing_box landing_l2">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="fiction"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Fictional World</span>
                    </span>
                </a>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 bordertop">
                    <a onClick="playFirstLevelWorldType(2)" href="javascript:void(0);" class="landing_box landing_l3 right">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="real"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Real World</span>
                        </span>
                    </a>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 borderleft">
                    <a onClick="playFirstLevelWorldType(3)" href="javascript:void(0);" class="landing_box landing_l4">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="family"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Your World</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>