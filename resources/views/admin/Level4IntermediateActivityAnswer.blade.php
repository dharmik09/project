@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        Answer of : {!!$level4IntermediateActivityDetail->l4ia_question_text!!}    
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/editlevel4IntermediateActivity') }}/{{$level4IntermediateActivityDetail->id}}">Back</a>                       
        </div>
        <div class="box-body">
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <form id="intermediateActivity" class="form-horizontal" method="post" action="{{ url('/admin/updatelevel4IntermediateOption') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="question_id" value="{{$level4IntermediateActivityDetail->id}}">
                    <input type="hidden" name="questionTemplateAnsType" value="{{$level4IntermediateActivityDetail->gt_temlpate_answer_type}}">
                <div class="box-body">
    
                </div>

                @forelse($level4IntermediateActivityAnswerDetail as $option)
                @if($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'option_choice' || $level4IntermediateActivityDetail->gt_temlpate_answer_type == 'filling_blank' || $level4IntermediateActivityDetail->gt_temlpate_answer_type == 'true_false')
                <!--Option Choice Template-->
                <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">Answer Option</label>
                    <div class="col-sm-3">
                        <textarea name="answer_option_text[{{$option->id}}]" class="form-control" placeholder="Answer Text...">{{isset($option->l4iao_answer_text)?$option->l4iao_answer_text:''}}</textarea>
                    </div>
                    <div class="col-sm-3">
                    <span class="multi_image_setup">
                        <input type='file' name="answer_option_image[{{$option->id}}]" data-imgsel="#{{$option->id}}" class="img_select" />
                        <span class="img_replace">
                            @if($option->l4iao_answer_image != "" && Storage::disk('s3')->exists($intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image) )
                                <?php
                                    $image = Config::get('constant.DEFAULT_AWS').$intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image;
                                ?>
                                <input type="hidden" name='edit_answer_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_image)?$option->l4iao_answer_image:''}}">
                                <img class="img_view" src="{{$image}}" id="{{$option->id}}"/>
                            @else
                                <img class="img_view" src="" id="{{$option->id}}" alt="Add Answer image" />
                            @endif
                        </span>
                    </span>
                    </div>
                    <div class="col-sm-3">
                        <textarea name="answer_image_description[{{$option->id}}]" class="form-control" placeholder="Image Description...">{{isset($option->l4iao_answer_image_description)?$option->l4iao_answer_image_description:''}}</textarea>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" name="correct_answer[{{$option->id}}]" <?php if(isset($option->l4iao_correct_answer) && $option->l4iao_correct_answer == 1){echo "checked";} ?> value="1" title="" style="cursor:pointer;">
                    </div>
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'image_reorder')
                <!--Image Drag and Drop Template-->
                <div class="form-group">  
                    <label for="category_type" class="col-sm-2 control-label">Answer Option 1</label>
                    <div class="col-sm-3">
                        @if($option->l4iao_answer_image != "" && Storage::disk('s3')->exists($intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image) )
                            <img src="{{Config::get('constant.DEFAULT_AWS').$intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image}}" width="70px" height="70px" />
                            <input type="hidden" name='edit_answer_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_image)?$option->l4iao_answer_image:''}}">
                        @endif
                        <input type="file" name="answer_option_image[{{$option->id}}]">
                    </div>
                    <label for="category_type" class="col-sm-2 control-label">Correct Order</label>
                    <div class="col-sm-1">        
                        <input type="textbox" value="{{isset($option->l4iao_answer_order)?$option->l4iao_answer_order:''}}" name="answer_order[{{$option->id}}]" class="form-control" placeholder="Order">
                    </div>
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'group_selection')
                <!-- Group selection Template-->
                <div class="form-group">  
                    <label for="category_type" class="col-sm-2 control-label">Answer Option</label>
                    <div class="col-sm-3">
                        @if($option->l4iao_answer_image != "" && Storage::disk('s3')->exists($intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image) )
                            <img src="{{Config::get('constant.DEFAULT_AWS').$intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image}}" width="70px" height="70px" />
                            <input type="hidden" name='edit_answer_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_image)?$option->l4iao_answer_image:''}}">
                        @endif
                        <input type="file" name="answer_option_image[{{$option->id}}]">
                    </div>
                    <label for="category_type" class="col-sm-2 control-label">Correct Order</label>
                    <div class="col-sm-1">        
                        <input type="textbox" value="{{isset($option->l4iao_answer_group)?$option->l4iao_answer_group:''}}" name="answer_group[{{$option->id}}]" class="form-control" placeholder="Order">
                    </div>
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'option_reorder')
                <!--Option Reorder Template-->
                <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">Answer Option</label>
                    <div class="col-sm-3">
                        <textarea name="answer_option_text[{{$option->id}}]" class="form-control" placeholder="Draggable Text">{{isset($option->l4iao_answer_text)?$option->l4iao_answer_text:''}}</textarea>
                    </div>
                    <div class="col-sm-1">        
                        <input type="textbox" value="{{isset($option->l4iao_answer_order)?$option->l4iao_answer_order:''}}" name="answer_order[{{$option->id}}]" class="form-control" placeholder="Order">
                    </div>
                    <div class="col-sm-2">
                    <span class="multi_image_setup">
                        <input type='file' name="answer_option_image[{{$option->id}}]" data-imgsel="#{{$option->id}}" class="img_select" />
                        <span class="img_replace">
                            @if($option->l4iao_answer_image != "" && Storage::disk('s3')->exists($intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image) )
                                <input type="hidden" name='edit_answer_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_image)?$option->l4iao_answer_image:''}}">
                                <img class="img_view" src="{{Config::get('constant.DEFAULT_AWS').$intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image}}" id="{{$option->id}}" width="70px" height="70px"/>
                            @else
                                <img class="img_view" src="" id="{{$option->id}}" alt="Display Image" />
                            @endif
                        </span>
                    </span>
                    </div>
                    <div class="col-sm-3">
                        <textarea name="answer_image_description[{{$option->id}}]" class="form-control" placeholder="Display Text">{{isset($option->l4iao_answer_image_description)?$option->l4iao_answer_image_description:''}}</textarea>
                    </div>
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'single_line_answer')
                <!--Single line answer Template-->
                <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">Right Answer</label>
                    <div class="col-sm-6">
                        <input type="text" value="{{isset($option->l4iao_correct_answer)?$option->l4iao_correct_answer:''}}" class="form-control" name="correct_answer[{{$option->id}}]">
                    </div>   
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'option_choice_with_response')
                <!-- Option with response -->
                <div class="form-group">
                    <div class="col-sm-12">
                        <hr>
                    </div>
                </div>
                <div class="form-group">
                    <div class="clearfix">
                    <label for="category_type" class="col-sm-2 control-label">Answer Option</label>
                    <div class="col-sm-3">
                        <textarea name="answer_option_text[{{$option->id}}]" class="form-control" placeholder="Answer Text...">{{isset($option->l4iao_answer_text)?$option->l4iao_answer_text:''}}</textarea>
                    </div>
                    <div class="col-sm-2">
                    <span class="multi_image_setup">
                        <input type='file' name="answer_option_image[{{$option->id}}]" data-imgsel="#{{$option->id}}" class="img_select" />
                        <span class="img_replace">
                            @if($option->l4iao_answer_image != "" && Storage::disk('s3')->exists($intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image))
                                <input type="hidden" name='edit_answer_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_image)?$option->l4iao_answer_image:''}}">
                                <img class="img_view" src="{{Config::get('constant.DEFAULT_AWS').$intermediateAnswerOriginalImageUploadPath.$option->l4iao_answer_image}}" id="{{$option->id}}"/>
                            @else
                                <img class="img_view" src="" id="{{$option->id}}" alt="Add Answer image" />
                            @endif
                        </span>
                    </span>                                                                         
                    </div>
                    <div class="col-sm-3">
                        <textarea name="answer_image_description[{{$option->id}}]" class="form-control answer_option_area" placeholder="Image Description...">{{isset($option->l4iao_answer_image_description)?$option->l4iao_answer_image_description:''}}</textarea>
                    </div>            
                    </div>
                    <div class="clearfix response_text">
                    <label for="category_type" class="col-sm-2 control-label">Response Text</label>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-2">
                        @if($option->l4iao_answer_response_image != "" && Storage::disk('s3')->exists($intermediateResponseOriginalImageUploadPath.$option->l4iao_answer_response_image) )
                            <?php 
                                $image =  Config::get('constant.DEFAULT_AWS').$intermediateResponseOriginalImageUploadPath.$option->l4iao_answer_response_image;
                            ?>
                            <input type="hidden" name='edit_response_image[{{$option->id}}]' value="{{isset($option->l4iao_answer_response_image)?$option->l4iao_answer_response_image:''}}">
                            <span class="multi_image_setup">
                                <input type='file' data-imgsel="#{{$option->id}}{{$option->l4iao_question_id}}" name="answer_response_image[{{$option->id}}]" class="img_select" />
                                <span class="img_replace">
                                    <img class="img_view" src="{{$image}}" id="{{$option->id}}{{$option->l4iao_question_id}}" alt="Add Response image" />
                                </span>
                            </span>
                        @endif
                    </div>
                    <div class="col-sm-3">
                        <textarea name="answer_response_image_description[{{$option->id}}]" class="form-control answer_option_area" placeholder="Response Image Description...">{{isset($option->l4iao_answer_response_text)?$option->l4iao_answer_response_text:''}}</textarea>
                    </div> 
                    <div class="col-sm-1">
                        <input type="checkbox" value="1" name="correct_answer[{{$option->id}}]" <?php if(isset($option->l4iao_correct_answer) && $option->l4iao_correct_answer == 1){echo "checked";} ?> title="Make Check if this is right option" style="cursor:pointer;">
                    </div>       
                    </div>
                </div>
                @elseif($level4IntermediateActivityDetail->gt_temlpate_answer_type == 'select_from_dropdown_option')
                <!--Option Reorder Template-->
                <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">Answer Option</label>
                    <div class="col-sm-6">
                        <textarea name="answer_option_text[{{$option->id}}]" class="form-control" placeholder="Answer Text...">{{isset($option->l4iao_answer_text)?$option->l4iao_answer_text:''}}</textarea>
                    </div>
                    <label for="category_type" class="col-sm-2 control-label">Correct Order</label>
                    <div class="col-sm-1">        
                        <input type="textbox" value="{{isset($option->l4iao_answer_order)?$option->l4iao_answer_order:''}}" name="answer_order[{{$option->id}}]" class="form-control" placeholder="Order">
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" value="1" name="correct_answer[{{$option->id}}]" <?php if(isset($option->l4iao_correct_answer) && $option->l4iao_correct_answer == 1){echo "checked";} ?> title="Make Check if this is right option" style="cursor:pointer;">
                    </div> 
                </div>
                @else
                <div>Wrong Answer Template</div>
                @endif
                @empty
                <div>No record found</div>
                @endforelse
                @if(isset($level4IntermediateActivityAnswerDetail) && !empty($level4IntermediateActivityAnswerDetail) && $level4IntermediateActivityDetail->gt_temlpate_answer_type != 'single_line_answer')
                <div class="form-group" id="shuffle_option_checkbox">
                    <label for="category_type" class="col-sm-2 control-label">Shuffle Options while display</label>
                    <div class="col-sm-3">
                        <input type="checkbox" name="shuffle_options" <?php if(isset($level4IntermediateActivityDetail->l4ia_shuffle_options) && $level4IntermediateActivityDetail->l4ia_shuffle_options == 1){echo "checked";} ?> value="1"  title="Check if you want to shuffle the options at display time" style="cursor:pointer;">
                    </div>
                </div>
                @endif
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    function deleteLevel4Media(media_id,media_name,media_type)
    {
        res = confirm('Are you sure you want to delete this record?');
        if(res){
        $.ajax({
            url: "{{ url('admin/deleteLevel4IntermediateMediaById') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "media_id": media_id,
                "media_name": media_name,
                "media_type": media_type
            },
            success: function(response) {
               location.reload();
            }
        });
        }else{
            return false;
        }
    }
</script>
@stop