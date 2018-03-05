@if(isset($traitQuestion[0]) && count($traitQuestion) > 0)
    <div class="loading-wrapper-sub" style="display: none;" class="loading-screen bg-offwhite">
        <div class="loading-text">
            <img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img">
        </div>
        <div class="loading-content"></div>
    </div>
    <div class="survey-list">
        <div class="qualities-sec">
            <p>{{$traitQuestion[0]->tqq_text}}</p>
            <input type="hidden" id="traitQue" value="{{$traitQuestion[0]->activityID}}" />
            <div class="row flex-container">
                @foreach ($traitQuestion[0]->options as $key => $value)
                    <div class="col-md-4 col-sm-6 col-xs-6 flex-items">
                        <div class="ck-button">
                            <label>
                                <input type="checkbox" name="traitAns" value="{{$value['optionId']}}" onclick="checkAnswerChecked();" />
                                <span>{{$value['optionText']}}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-btn">
            <span class="icon"><i class="icon-arrow-spring"></i></span>
            <button onclick="saveLevel1TraitQuestion();" id="btnSaveTrait" title="Next" class="btn btn-primary" disabled="disabled">Next</button>
        </div>
    </div>
@else 
    <div class="sec-forum"><span>No traits completed</span></div>
@endif