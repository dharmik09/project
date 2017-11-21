<div class="modal-header">
    <h1>{{ucfirst($couponNameD)}} &nbsp; - &nbsp; Total {{count($coupons)}} coupons used</h1>
    <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
</div>
<div class="modal-body">
    <div class="table_container_outer">
        <table cellspacing="100">
            <tr>
                <th class="icon_title">Icon</th>
                <th></th>
                <th>Name</th>
                <th>Use Type</th>
            </tr>
            <?php
            if (isset($coupons) && !empty($coupons)) {
                foreach ($coupons as $competingValue) {
                    ?>        
                    <tr>
                        <td>
                            <span class="img_contianer_outer">
                                <?php
                                    $teenPhoto = $competingValue->t_photo;
                                    if (isset($teenPhoto) && $teenPhoto != '') {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenPhoto);
                                    } else {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                    }
                                ?>
                                <img src="{{$t_photo}}" title="Proteen-Coupon-User"/>
                            </span>
                        </td>
                        <td></td>
                        <td>{{ucfirst($competingValue->t_name)}}</td>
                        <td>
                            <?php 
                                if($competingValue->tcu_type == "gift"){ echo "Gifted"; }else{ echo "Own Use"; }
                            ?>
                        </td>
                    </tr>

                    <?php
                }
            } else {
                echo "<tr><td colspan='5'><h3>No record found</h3></td></tr>";
            }
            ?>
        </table>
    </div>
</div>