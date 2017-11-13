@for ($i = 0; $i < 12; $i++)
<div class="form-group">
    <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
    <div class="col-sm-3">
        <textarea name="answer_option_text[{{$i}}]" class="form-control" placeholder="Answer Text..."></textarea>
    </div>
    <label for="category_type" class="col-sm-2 control-label">Correct Order</label>
    <div class="col-sm-2">        
        <input type="textbox" name="answer_order[{{$i}}]" class="form-control" placeholder="Order">
    </div>
    <div class="col-sm-2">
        <input type="checkbox" class="l4optiontype" name="correct_answer[{{$i}}]" value="1" title="Make Check if this is right option" style="cursor:pointer;">
    </div>
</div>
@endfor




