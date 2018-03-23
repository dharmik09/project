<form>
    <div class="sec-parents">
        <div class="mentor-list">
            <ul class="row owl-carousel">
                @forelse($getCompetingUserList as $teenager)
                <li id="{{$teenager['teenager_id']}}" class="col-sm-3 col-xs-6" onclick="getChallengeScoreDetails({{$teenager['teenager_id']}});">
                    <figure>
                        <div class="mentor-img" style="background-image: url('{{ Storage::url($teenager['profile_pic']) }}')"></div>
                        <figcaption>{{ $teenager['name'] }}</figcaption>
                    </figure>
                </li>
                @empty
                    No Records found.
                @endforelse
            </ul>
        </div>
    </div>
</form>