<label for="question" class="col-sm-2 control-label">Select Concepts</label>
<div class="col-sm-9">  
    <select name="concept{{(isset($all) && $all == 0)?'[]':''}}" id="concept" {{(isset($all) && $all == 0)?'multiple':''}} class="form-control">  
        <?php if(isset($getQuestionTemplateForProfession) && !empty($getQuestionTemplateForProfession)){ ?>
        {{(isset($all) && $all == 1)?'<option value="0">All Concepts</option>':''}}
        <?php foreach($getQuestionTemplateForProfession as $key => $concept) { ?>                    
            <option value="{{$concept->gt_template_id}}" <?php if(isset($selectedconcept) && $selectedconcept > 0 && $selectedconcept == $concept->gt_template_id) {echo "selected='selected'"; }?>> {{$concept->gt_template_title}}</option>               
        <?php } }else{?>
        <option value="">No Concepts</option>        
        <?php }?>            
    </select>
</div>
