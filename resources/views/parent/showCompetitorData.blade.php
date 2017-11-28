<div class="modal-content" style="overflow: hidden;">
    <button type="button" class="close close_next" data-dismiss="modal">Close</button>
    <div class="default_logo"><img src="{{ Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
	<div class="sticky_pop_head">
        <br/><h2 class="title">{{$profession_name}}</h2><br/><br/></div>
    <div class="modal-body body_sticky challengers_vs">
        <div class="table-responsive challengers_table">
             <?php
                 $photo = $parentDetail->p_photo;
                 $profilePicUrlParent = '';
                 if ($photo != '' && isset($photo)) {
                    $profilePicUrlParent = Storage::url(Config::get('constant.PARENT_ORIGINAL_IMAGE_UPLOAD_PATH') . $photo);
                 } else {
                    $profilePicUrlParent = Storage::url(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                 }

                 $imageTeen = Helpers::getTeenagerImageUrl($teenDetail['t_photo'], 'thumb');
                ?>
            <table>
                <tr>
                    <th></th>
                    <th><img src="{{$profilePicUrlParent}}" alt=""><span>{{$parentDetail->p_first_name}}</span></th>
                    <th><img src="{{$imageTeen}}" alt=""><span>{{$teenDetail['t_name']}}</span></th>
                </tr>
                <tr class="score">
                    <td>Score    :</td>
                    <td>{{$level4ParentBooster['yourScore']}}</td>
                    <td>{{$level4Booster['yourScore']}}</td>
                </tr>
                <tr class="rank">
                    <td>Rank    :</td>
                    <td>{{$level4ParentBooster['yourRank']}}</td>
                    <td>{{$rank}}</td>
                </tr>
                <tr class="point">
                    <td>Point    :</td>
                    <td>{{$level4ParentBooster['yourScore']}} / {{$level4ParentBooster['totalPobScore']}}</td>
                    <td>{{$level4Booster['yourScore']}} / {{$level4Booster['totalPobScore']}}</td>
                </tr>
            </table>
        </div>
    </div>
</div>