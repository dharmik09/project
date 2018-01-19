<div class="level-selection">
    <h3>Vote</h3>
    <div class="level-container">
        <div class="row">
            <div class="col-xs-6 border-right">
                <a href="javascript:void(0)" class="level-block" title="Opinions">
                    <a href="<?php if($isQuestionCompleted == 0) { ?> {{ url('/teenager/playLevel1Activity') }} <?php } else { echo "javascript:void(0)"; }?>" class="landing_box landing_l1" onclick="checkLevel1Questions({{$isQuestionCompleted}});">
                    <figure class="landing_icon">
                        <span class="vote"></span>
                    </figure>
                        <span>Opinions</span>
                </a>
            </div>
            <div class="col-xs-6 border-bottom">
                <a href="javascript:void(0)" onClick="playFirstLevelWorldType(1)" class="level-block" title="Fictional World">
                    <figure class="landing_icon">
                        <span class="fiction"></span>
                    </figure>
                        <span>Fictional World</span>
                </a>
            </div>
            <div class="col-xs-6 border-top">
                <a href="javascript:void(0)" onClick="playFirstLevelWorldType(2)" class="level-block" title="Real World">
                    <figure class="landing_icon">
                        <span class="real"></span>
                    </figure>
                        <span>Real World</span>
                </a>
            </div>
            <div class="col-xs-6 border-left">
                <a href="javascript:void(0)" onClick="playFirstLevelWorldType(3)" class="level-block" title="Your World">
                    <figure class="landing_icon">
                        <span class="family"></span>
                    </figure>
                    <span>Your World</span>
                </a>
            </div>
        </div>
    </div>
</div>            