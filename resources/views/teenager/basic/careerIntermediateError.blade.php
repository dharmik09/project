<div class="quiz_view">
    <div class="clearfix time_noti_view">
        <span class="help_noti pull-right">
            <span class="pull-right close">
                <i class="icon-close"></i>
            </span>
        </span>
    </div>
    <div class="cong-block">
        <div class="row">
            <div class="col-xs-12">
                <h2>{{ ( isset($response['title']) && $response['title'] != "" ) ? $response['title'] : "Error!" }}</h2>
                <p><strong>{{ ucwords($response['message']) }} </strong></p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var intermediateCount = 0;
    var ansTypeSet = "";
    var setPopupTime = 0;
    var optionType = "";
    var optionName = "";
    var limitSelect = 0;
</script>