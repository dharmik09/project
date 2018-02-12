<div class="modal-header">
    <h1></h1>
    <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
</div>
<div class="modal-body">
    <div class="table_container_outer">
        <table cellspacing="100">
            <tr>
                <th class="icon_title">Icon</th>
                <th></th>
                <th>Name</th>
            </tr>
            <?php
            if (isset($teenAppliedForScholarship) && count($teenAppliedForScholarship) > 0) {
                foreach ($teenAppliedForScholarship as $teen) {
                    ?>        
                    <tr>
                        <td>
                            <span class="img_contianer_outer">
                                <?php
                                    if ($teen->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teen->t_photo) > 0) {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teen->t_photo);
                                    } else {
                                        $t_photo = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                    }
                                ?>
                                <img src="{{$t_photo}}" title="Proteen-Coupon-User"/>
                            </span>
                        </td>
                        <td></td>
                        <td>{{ucfirst($teen->t_name)}}</td>
                    </tr>

                    <?php
                }
            } else {
                echo "<tr><td colspan='3'><center><h3>No records found</h3></center></td></tr>";
            }
            ?>
        </table>
    </div>
</div>