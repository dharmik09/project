@for ($i = 0,$k=20; $i < 6; $i++,$k++)
<div class="form-group">
    <div class="col-sm-12">
        <hr>
    </div>
</div>

<div class="form-group">
    <div class="clearfix">
        <label for="category_type" class="col-sm-2 control-label">Answer Option {{$i+1}}</label>
        <div class="col-sm-3">
            <textarea name="answer_option_text[{{$i}}]" class="form-control answer_option_area" placeholder="Answer Text..."></textarea>
        </div>
        <div class="col-sm-2">
            <span class="multi_image_setup">
                <input type='file' data-imgsel="#{{$i+1}}" name="answer_option_image[{{$i}}]" class="img_select" />
                <span class="img_replace">
                    <img class="img_view" id="{{$i+1}}" alt="Add Answer image" />
                </span>
            </span>
        </div>
        <div class="col-sm-3">
            <textarea name="answer_image_description[{{$i}}]" class="form-control answer_option_area" placeholder="Image Description..."></textarea>
        </div>  
    </div>     
    <div class="clearfix response_text">
        <label for="category_type" class="col-sm-2 control-label">Response Text {{$i+1}}</label>
        <div class="col-sm-3"></div>
        <div class="col-sm-2">
            <!--<input type="file" name="answer_response_image[0]">-->
            <span class="multi_image_setup">
                <input type='file' data-imgsel="#{{$k+1}}" name="answer_response_image[{{$i}}]" class="img_select" />
                <span class="img_replace">
                    <img class="img_view" id="{{$k+1}}" alt="Add Response image" />
                </span>
            </span>
        </div>
        <div class="col-sm-3">
            <textarea name="answer_response_image_description[{{$i}}]" class="form-control answer_option_area" placeholder="Response Image Description..."></textarea>
        </div> 
        <div class="col-sm-1">
            <input type="checkbox" class="l4optiontype" value="1" name="correct_answer[{{$i}}]" title="Make Check if this is right option" style="cursor:pointer;">
        </div>   
    </div>
</div>
@endfor
