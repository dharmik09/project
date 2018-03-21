@if(isset($userDetail) && !empty($userDetail))
<div class="request_parent gift_coin">
    <form id="addTeenCoins" class="form-horizontal" method="post" action='{{ url("/school/save-coins-data-for-teen") }}'>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="id" value="{{$userDetail['id']}}">
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-3 label_user">Name</div>
        <div class="col-md-9 col-sm-9 col-xs-9 detail_user">{{$userDetail['t_name']}}</div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-3 label_user">Email Id</div>
        <div class="col-md-9 col-sm-9 col-xs-9 detail_user">{{$userDetail['t_email']}}</div>
    </div>
    <div class="clearfix">
        <div class="input_icon">
            <input type="text" name="t_coins" class="cst_input_primary numeric" placeholder="Enter ProCoins">
        </div>
    </div>
    <div class="button_container gift_modal_page">
        <div class="submit_register">
            <input type="submit" class="btn primary_btn" id="saveGiftProCoins" value="Gift">
        </div>
        <div class="submit_register">
            <a type="button" href="javascript:void(0)" class="btn primary_btn" data-dismiss="modal" id="cancel" value="Cancel">Cancel</a>
        </div>
    </div>
    </form>
</div>
@else
No data found
@endif
<script type="text/javascript">
    $(document).on("keyup", ".numeric", function () {
        this.value = this.value.replace(/[^0-9]/gi, "");
    });

    $("#saveGiftProCoins").click(function() {
        var form = $("#addTeenCoins");
        form.validate();
        if (form.valid()) {
            form.submit();
            $("#saveGiftProCoins").attr("disabled", 'disabled');
        } else {
            $("#saveGiftProCoins").removeAttr("disabled", 'disabled');
        }
    });

    jQuery(document).ready(function() {
        var loginRules = {
            t_coins: {
                required: true
            }
        };
        $("#addTeenCoins").validate({
            rules: loginRules,
            messages: {
                t_coins: {
                  required: '<?php echo trans("validation.requiredfield");?>'
                }
            }
        });
    });
</script>
