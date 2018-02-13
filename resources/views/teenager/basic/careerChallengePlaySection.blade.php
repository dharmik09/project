<form>
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group custom-select">
                <select class="form-control">
                    <option value="Select a parent or mentor">Select a parent or mentor</option>
                    @forelse($teenagerParents as $parent)
                        <option value="{{$parent->id}}">{{$parent->p_first_name}}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <button class="btn btn-submit" type="submit" title="a=Add">Submit</button>
        </div>
    </div>
</form>