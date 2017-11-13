@for ($i = 0; $i < 16; $i++)
<div class="form-group">  
    <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
    <div class="col-sm-3">
        <input type="file" name="answer_option_image[{{$i}}]" onchange="readURL(this);">
    </div>
    <label for="category_type" class="col-sm-2 control-label">Correct Order</label>
    <div class="col-sm-1">        
        <input type="textbox" name="answer_order[{{$i}}]" class="form-control" placeholder="Order">
    </div>
</div>
@endfor

