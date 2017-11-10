@for ($i = 0; $i < 6; $i++)
<div class="form-group">
    <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
    <div class="col-sm-3">
        <textarea name="answer_option_text[{{$i}}]" class="form-control" placeholder="Answer Text..."></textarea>
    </div>    
    <div class="col-sm-1">
        <input type="checkbox" class="l4optiontype" name="correct_answer[{{$i}}]" title="Make Check if this is right option" style="cursor:pointer;">
    </div>
</div>
@endfor




