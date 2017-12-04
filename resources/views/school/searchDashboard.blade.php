<div class="table_container">
    <table class="sponsor_table table_ckbx nobopd" id="table1">
        <tr class="cst_status">
            <th>{{trans('labels.stuname')}}</th>
            <th>Student ID</th>
            <th>{{trans('labels.class')}}</th>
            <th>{{trans('labels.division')}}</th>
            <th>Email</th>
            <th class="school_dashboard_column" title="Select all students on this page">
                Initiate Verification
                <span class="user_select_mail cst_user_select_mail">
                    <input type="checkbox" id="checkall" name="checkall" class="checkbox checkall custom_checkbox">
                    <label for="checkall"><em></em><span></span></label>                                  
                </span>
            </th>
            <th class="school_dashboard_column">Verified Status</th>
            <th>Active Status</th>
            <th>Gift</th>
        </tr>
        <?php $checkValue = 0;?>
        @forelse($teenDetailSchoolWise as $teenDetail)
        <tr>
            <td>{{$teenDetail->t_name}}</td>
            <td>{{$teenDetail->t_rollnum}}</td>
            <td>{{$teenDetail->t_class}}</td>
            <td>{{$teenDetail->t_division}}</td>
            <td>{{$teenDetail->t_email}}</td>
            <td>
                <?php
                if ($teenDetail->email_sent == "no") {
                    $checkValue++;
                    ?>
                <input type="hidden" id="isDataAvailable"  value="<?php echo $checkValue?>" />
                    <span class="user_select_mail cst_user_select_mail">
                        <input type="checkbox" name="email[]" value="{{$teenDetail->t_email}}" value="2" id="mail_{{$teenDetail->id}}" class="indi_checkboc custom_checkbox">
                        <label for="mail_{{$teenDetail->id}}"><em></em><span></span></label>
                    </span>
                    <?php
                } else {
                    echo "<i class='fa fa-check rightCheckColor' aria-hidden='true'></i>";
                }
                ?>
            </td>
            <td><?php echo ($teenDetail->t_isverified == 1)?"<span class='yes0'>Yes</span>":"<span class='no0'>No</span>"; ?></td>
            <?php $active = $teenDetail->t_school_status; ?>
            <td>
                <a class="btn primary_btn mid_btn cst_sponsor_dash" href="<?php if ($active == 0) { ?> {{url('/school/inactive')}}/{{$teenDetail->id}}/1 <?php } else { ?> {{url('/school/inactive')}}/{{$teenDetail->id}}/0 <?php } ?> " title="<?php if ($active == 0) {echo "Click to make Active"; }else{ echo "Click to make Inactive";}?> " class="btn primary_btn">
                    <?php if ($active == 0) { ?> No <?php } else { ?> Yes <?php } ?></a>
            </td>
            <td>
                <div class="coupon_control">
                    <span class="tool-tip" <?php if($schoolData['sc_coins'] == 0) echo 'data-toggle="tooltip" data-placement="bottom" title="Register as Enterprise to avail ProCoins. If already registered please buy ProCoins package from your Enterprise login"';?>>
                        <a href="javascript:void(0);" class="gift no_ani <?php if($schoolData['sc_coins'] == 0){ echo 'disabled';}?>" onclick="giftCoins({{$teenDetail->id}});" <?php if($schoolData['sc_coins'] == 0) { echo 'disabled="disabled"';}?>>
                        Gift</a>
                    </span>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9"><center>{{trans('labels.norecordfound')}}</center></td>
        </tr>
        @endforelse
        <tr>
            <td colspan="9" class="sub-button">
                @if($checkValue > 0) <input type="submit" id="mail_submit" name="submit" class="btn primary_btn mid_btn cst_sponsor_dash" value="Send Mail"> @endif
                @if (isset($teenDetailSchoolWise) && !empty($teenDetailSchoolWise))
                <div class="pull-right">
                    <?php echo $teenDetailSchoolWise->render(); ?>
                </div>
                @endif
            </td>
        </tr>
    </table>

</div>