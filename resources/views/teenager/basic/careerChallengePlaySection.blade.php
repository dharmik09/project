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
            </div>
        </div>
        <div class="col-sm-4">
            <button id="parentChallenge" class="btn btn-submit btn-default" type="button" onclick="challengeToParentAndMentor();" title="Add">Submit</button>
        </div>
        <div class="challenge_message"></div>
    </div>
</form>