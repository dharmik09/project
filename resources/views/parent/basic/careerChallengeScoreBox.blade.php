<div class="modal-dialog">
    <div class="modal-content custom-modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
            <h4 class="modal-title">{{$professionName}}</h4>
        </div>
        <div class="modal-body">
            <div class="table-responsive challengers_table">
                <table>
                    <tbody>
                        <tr>
                            <?php
                            if (isset($parentDetail->p_photo) && $parentDetail->p_photo != '' && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parentDetail->p_photo) > 0) {
                                $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parentDetail->p_photo;
                            } else {
                                $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                            } ?>
                            <th></th>
                            <th><img src="{{Storage::url($parentPhoto)}}" alt=""><span>{{$parentDetail->p_first_name}} </span></th>
                            <?php
                            if (isset($teenDetail['t_photo']) && $teenDetail['t_photo'] != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenDetail['t_photo']) > 0) {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . $teenDetail['t_photo'];
                            } else {
                                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                            } ?>
                            <th><img src="{{Storage::url($teenPhoto)}}" alt=""><span>{{$teenDetail['t_name']}}</span></th>
                        </tr>
                        <tr class="score">
                            <td>Score :</td>
                            <td>{{$level4ParentBooster['yourScore']}}</td>
                            <td>{{$level4Booster['yourScore']}}</td>
                        </tr>
                        <tr class="rank">
                            <td>Rank :</td>
                            <td>{{$level4ParentBooster['yourRank']}}</td>
                            <td>{{$rank}}</td>
                        </tr>
                        <tr class="point">
                            <td>Point :</td>
                            <td>{{$level4ParentBooster['yourScore']}} / {{$level4ParentBooster['totalPobScore']}}</td>
                            <td>{{$level4Booster['yourScore']}} / {{$level4Booster['totalPobScore']}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--<div class="modal-footer"><button type="button" class="btn btn-primary btn-next" data-dismiss="modal">ok</button><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>-->
    </div>
</div>