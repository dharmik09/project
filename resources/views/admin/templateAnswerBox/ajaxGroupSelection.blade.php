@for ($i = 0; $i < 8; $i++)
<div class="form-group">  
    <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
    <div class="col-sm-3">
        <input type="file" name="answer_option_image[{{$i}}]">
    </div>
    <label for="category_type" class="col-sm-2 control-label">Group</label>
    <div class="col-sm-1">        
        <input type="textbox" name="answer_group[{{$i}}]" class="form-control" placeholder="Group">
    </div>
</div>
@endfor


