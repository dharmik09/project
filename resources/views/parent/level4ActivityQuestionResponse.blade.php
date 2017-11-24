@include('teenager/teenagerLevelPointBox')
<a class="back_me" href="{{url('parent/my-challengers-accept')}}/{{$response['profession_id']}}/{{$response['teen_id']}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Back</span></a>
<?php
if (!empty($response['data'])) {
    $isShare = false;
?>
    <div class="question_container">
        <form id = "level4_activity_ans" role = "form" enctype = "multipart/form-data" method = "POST" autocomplete = "off" autocorrect = "off" autocapitalize = "off" spellcheck = "false">
            <input type = "hidden" name = "_token" value = "{{ csrf_token() }}">
            <input type = "hidden" id = "questionID" name = "questionID" value = "{{$response['data']->activityID}}" >
            <input type ="hidden" id="blackhole" name="timer" />
            <div class="clearfix">
                <div class="col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10">
                    <p class="que"><?php echo $response['data']->question_text; ?></p>
                    @if($response['data']->totalCorrectOptions > 1)
                    <span style="color:yellow">(You can select multiple answers for this question)</span><br/><br/>
                    @endif
                </div>
            </div>
            <div class="clearfix">
                <div class="col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10">
                    <?php
                    if (!empty($response['data']->options)) {
                        foreach ($response['data']->options as $key => $value) {
                            if ($response['data']->type == 1) {
                                $optionType = "radio";
                                $optionName = "answerID[0]";
                            } else if ($response['data']->type == 0) {
                                if ($response['data']->totalCorrectOptions > 1) {
                                    $optionType = "checkbox";
                                    $optionName = "answerID[]";
                                } else {
                                    $optionType = "radio";
                                    $optionName = "answerID[0]";
                                }
                            } else {
                                $optionType = "radio";
                                $optionName = "answerID[0]";
                            }
                            ?>
                            <div class="l4_basic_simplex_question_answer">
                                <input class='multiCast' type="<?php echo $optionType; ?>" id="check<?php echo $value['optionId']; ?>" name="<?php echo $optionName; ?>" value="<?php echo $value['optionId']; ?>" />
                                <label for="check<?php echo $value['optionId']; ?>" class="class<?php echo $value['optionId']; ?> right_answer"><?php echo $value['optionText']; ?></label>
                            </div>
                            <?php
                        }
                    } else {
                        echo "No Any Options For This Question.";
                    }
                    ?>
                    <div class="answer_submit_btn">
                        <button type="button"  onClick="saveAnswer();" class="saveMe btn primary_btn" data-dismiss="modal">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
} else {
    $isShare = true;
    $parentName = $careerName = '';

    $parentName = Auth::guard('parent')->user()->p_first_name;
    $getCareerDataDetail = Helpers::getProfessionName($response['profession_id']);
    if (isset($getCareerDataDetail[0]) && !empty($getCareerDataDetail[0])) {
        $careerName = $getCareerDataDetail[0]->pf_name;
    }
    $desc = "Congratulations! " . ucfirst($parentName) . " You are now a rookie ProTeen " . ucfirst($careerName) . " ";
    $image = Helpers::getParentOriginalImageUrl(Auth::guard('parent')->user()->p_photo);
    ?>
    <div class="cong_container animation-element in-view">
        <canvas id="canvas">Canvas is not supported in your browser.</canvas>
        <div class="cong cong_hero"><img src="{{ Storage::url('frontend/images/jumping.gif') }}" alt=""></div>
        <div class="cong cong_top"><p>Congratulations!</p></div>
        <div class="cong cong_bottom">
            <div class="cong_outer">
                <div class="cong_middle">
                    <p> {{ucfirst($parentName)}}! You are now a rookie ProTeen {{ucfirst($careerName)}}.</p>
                    <p> Share and let the world know! Keep going!
                        <a href="javascript:void(0);" onclick="shareFacebook('{{url(' / ')}}', '{{$desc}}', '{{$desc}}', '{{$image}}')" class="fb_congratulation"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>
                        <a href="https://plus.google.com/share?url={{url('/')}}&image={{$image}}" target="_blank"  class="google_congratulation"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="cong cong_btn" style="margin-top: 30px;">
            <a class="rlink" style="font-size: 20px;font-weight: bold;"  href="https://goo.gl/forms/sOFyvbWqzBH98S2A3" target="_blank">Please give us feedback | Earn 5000 ProCoins&nbsp;&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></a>
        </div>
        <div class="cong cong_btn">
            <a class="button3d social_button play" href="{{url('parent/my-challengers-accept')}}/{{$response['profession_id']}}/{{$response['teen_id']}}">Play more</a>
            <a href="{{url('parent/my-challengers')}}" class="button3d social_button try">Try another</a>
        </div>
    </div>
    <?php
}
?>

<script>
    var optionType = '<?php echo (isset($optionType) && $optionType != '') ? $optionType : 0; ?>';
    var optionName = '<?php echo (isset($optionName) && $optionName != '') ? $optionName : "radio"; ?>';
    var limitSelect = <?php echo (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 ?>;
</script>
<?php
if (isset($response['timer']) && $response['timer'] != '') {
    $response['timer'] = $response['timer'];
} else {
    $response['timer'] = 0;
}
?>
<script src="{{ asset('backend/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script>
    var timeCount = '<?php echo $response['timer']; ?>';
    var count = timeCount;
    $(window).bind("load", function() {    
});
</script>
<?php if ($isShare) { ?>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: FACEBOOK_CLIENT_ID,
                xfbml: true,
                version: 'v2.3'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function shareFacebook(url, title, desc, image)
        {
            var obj = {method: 'feed', link: url, name: title, description: desc, picture: image};
            function callback(response) {
                if (response) {
                    console.log(response);
                }
            }
            FB.ui(obj, callback);
        }
    </script>
<?php } ?>


