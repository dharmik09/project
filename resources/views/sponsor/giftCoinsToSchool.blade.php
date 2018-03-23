@if(isset($userDetail) && !empty($userDetail))
<div class="request_parent gift_coin">
    <form id="addSchoolCoins" class="form-horizontal" method="post" action={{ url("/sponsor/save-coins-data-for-school") }}>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @if($userDetail->sp_sc_uniqueid == '')
          <div class="clearfix">
              <div class="input_icon">
                  <input type="text" name="school_id" id="school_id" class="cst_input_primary" placeholder="Enter School Unique Id">
              </div>
          </div>
        @else
            <div class="clearfix">
              <div class="input_icon">
                  <input type="text" name="school_id" readonly="true" id="school_id" class="cst_input_primary" placeholder="Enter School Unique Id" value="{{$userDetail->sp_sc_uniqueid}}">
              </div>
          </div>
        @endif
        <div class="clearfix">
            <div class="input_icon">
                <input type="text" name="t_coins" id="t_coins" class="cst_input_primary numeric" placeholder="Enter ProCoins">
            </div>
        </div>
        <div class="button_container gift_modal_page">
            <div class="submit_register">
                <input type="submit" class="btn primary_btn" id="saveGiftProCoins" value="Gift">
            </div>
            <div class="submit_register">
                <a type="button" href="javascript:void(0)" data-dismiss="modal" class="btn primary_btn" id="cancel" value="Cancel">Cancel</a>
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
        var form = $("#addSchoolCoins");
        form.validate();
        if (form.valid()) {
            form.submit();
            $("#saveGiftProCoins").addClass('disable');
        } else {
            $("#saveGiftProCoins").removeClass('disable');
        }
    });

    jQuery(document).ready(function() {
        var loginRules = {
            t_coins: {
                required: true
            },
            school_id: {
                required: true
            }
        };
        $("#addSchoolCoins").validate({
            rules: loginRules,
            messages: {
                t_coins: {
                  required: '<?php echo trans("validation.requiredfield");?>'
                },
                school_id: {
                    required: 'School unique id is required'
                }
            }
        });
    });


</script>
