<form>
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group custom-select">
                <select id="listParent" class="form-control">
                    <option value="">Select a parent or mentor</option>
                    @forelse($teenagerParents as $parent)
                        <option value="{{$parent->id}}">{{$parent->p_first_name}}</option>
                    @empty
                    @endforelse
                </select>
                <em class="challenge_message clearfix invalid"></em>
            </div>
        </div>
        <div class="col-sm-4">
            <button id="parentChallenge" class="btn btn-submit btn-default" type="button" onclick="challengeToParentAndMentor();" title="Add">Submit</button>
        </div>
        <div id="challenge-text" class="favourite-text"></div>
    </div>
    <div class="sec-parents">
        <div class="mentor-list">
            <ul class="row owl-carousel">
                @forelse($challengedAcceptedParents as $parent)
                <?php 
                    if (isset($parent->p_photo) && $parent->p_photo != '' && Storage::size(Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parent->p_photo) > 0) {
                        $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . $parent->p_photo;
                    } else {
                        $parentPhoto = Config::get('constant.PARENT_THUMB_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
                    }
                ?>
                <li class="col-sm-3 col-xs-6">
                    <figure>
                        <div class="mentor-img" style="background-image: url('{{ Storage::url($parentPhoto) }}')"></div>
                        <figcaption>{{ $parent->p_first_name }}</figcaption>
                    </figure>
                </li>
                @empty
                    No Records found.
                @endforelse
            </ul>
        </div>
    </div>
</form>