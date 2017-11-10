@for ($i = 0; $i < 12; $i++)
<div class="form-group">
    <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
    <div class="col-sm-3">
        <textarea name="answer_option_text[{{$i}}]" class="form-control" placeholder="Draggable Text"></textarea>
    </div>
    <div class="col-sm-1">        
        <input type="textbox" name="answer_order[{{$i}}]" class="form-control" placeholder="Order">
    </div>
    <div class="col-sm-2">
        <span class="multi_image_setup">
            <input type='file' data-imgsel="#{{$i+1}}" name="answer_option_image[{{$i}}]" class="img_select"/>
            <span class="img_replace">
                <img class="img_view" id="{{$i+1}}" alt="Display Image" />
            </span>
        </span>        
    </div>
    <div class="col-sm-3">
        <textarea name="answer_image_description[{{$i}}]" class="form-control" placeholder="Display Text"></textarea>
    </div>       
</div>
@endfor



