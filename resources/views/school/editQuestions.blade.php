@extends('layouts.school-master')

@section('content')
<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script>      
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ask Your Students
    </h1>
</section>

<div class="centerlize">
    <div class="container">
        <div class="container_padd detail_container add-question-sec">
        <?php
        if($activityDetail)
        {
           foreach($activityDetail as $value)
            {
                $id = $value->id;
                $l2ac_text = $value->l2ac_text;
                $l2ac_points = $value->l2ac_points;
                $deleted = $value->deleted;
                $l2op_option = $value->l2op_option;
                $l2op_fraction = $value->l2op_fraction;
                $apt_name=$value->l2ac_apptitude_type;
                $it_name=$value->l2ac_interest;
                $mit_name=$value->l2ac_mi_type;
                $pt_name=$value->l2ac_personality_type;
                $l2ac_image = $value->l2ac_image;
                $section = $value->section_type;

                $option = explode("," , $l2op_option);
                $l2op_fraction = explode("," , $l2op_fraction);
                 //print_r($option);die;
            }
        }
        else
        {
            $id = '';
            $l2ac_text = '';
            $l2ac_points = '';
            $deleted = '';
            $l2op_option = '';
            $l2op_fraction = '';
            $apt_name='';
            $it_name='';
            $mit_name='';
            $pt_name='';
            $l2ac_image = '';
            $section = '';
            
            $option=array();
            $option[0]='';
            $option[1]='';
            $option[2] = '';
            $l2op_fraction =array();
            $l2op_fraction[0]='';
            $l2op_fraction[1]='';
            $l2op_fraction[2]='';
        }
        ?>
        <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
        <form id="addLevel2Activity" class="form-horizontal" method="post" action="{{ url('/school/save-level2-questions') }}" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0'?>">
            <input type="hidden" name="hidden_logo" value="<?php echo (isset($l2ac_image) && !empty($l2ac_image)) ? $l2ac_image : '' ?>">
            <input type="hidden" name="hidden_points" value="<?php echo (isset($l2ac_points) && !empty($l2ac_points)) ? $l2ac_points : '0'?>">
            <input type="hidden" name="pageRank" value="<?php echo $page ?>">
            <div class="box-body">
                <div class="form-group">
                    <label for="l2ac_text" class="col-sm-2 control-label">Question</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="l2ac_text" name="l2ac_text" placeholder="Question">{{$l2ac_text}}</textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label for="l2ac_points" class="col-sm-2 control-label">{{trans('labels.formlblpoint')}}</label>
                    <div class="col-sm-6">
                       <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="200" data-slider-step="5" data-slider-value="{{$l2ac_points}}"  name="l2ac_points" class="boot_slider"/>
                       <span class="badge bg-green" id="label_point"> {{$l2ac_points }} </span>
                    </div>
                </div>
                        
                <div class="form-group">
                    <label for="l2ac_image" class="col-sm-2 control-label">{{trans('labels.formlblimage')}}</label>
                    <div class="col-sm-6 upload_image">
                        <input type="file" id="l2ac_image" name="l2ac_image" onchange="readURL(this);"/>
                        <?php
                            if(isset($id) && $id != '0' && isset($uploadLevel2ActivityThumbPath)) {
                                $image = ($l2ac_image != "" && Storage::disk('s3')->exists($uploadLevel2ActivityThumbPath.$l2ac_image)) ? Config::get('constant.DEFAULT_AWS').$uploadLevel2ActivityThumbPath.$l2ac_image : asset('/backend/images/proteen_logo.png'); ?>
                                <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                        <?php } ?>
                    </div>
                </div>
                            
                <div id="addoption" class="addoption">
                  <div class="form-group">
                      <label for="l2op_option" class="col-sm-2 control-label">Answer Choices</label>
                      <div class="col-sm-5">
                           <input type="text" class="form-control" id="l2op_option" name="l2op_option[]" placeholder="Answer Choices" value="{{$option[0]}}" />
                      </div>
                       <div class="col-sm-1">
                           <input type="radio" name="l2rad_option" value="0" <?php if($l2op_fraction[0]==1){ ?> checked="checked" <?php } ?> />
                           {{trans('labels.formblfraction')}}
                      </div>
                  </div>

                  <div class="form-group">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">
                           <input type="text" class="form-control" id="l2op_option" name="l2op_option[]" placeholder="Answer Choices" value="<?php if($option[0]) echo $option[1]; ?>" />
                        </div>
                        <div class="col-sm-1">
                           <input type="radio" name="l2rad_option" value="1" <?php if($l2op_fraction[1]==1){ ?> checked="checked" <?php } ?>/>
                           {{trans('labels.formblfraction')}}
                        </div>

                        <input type="hidden" name="countRadio" value="2" id="countRadio"/>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">
                           <input type="text" class="form-control" id="l2op_option" name="l2op_option_03" placeholder="Answer Choices" value="<?php if(isset($option[2]) && !empty($option[2])) echo $option[2]; ?>" />
                        </div>
                        <div class="col-sm-1">
                           <input type="radio" name="l2rad_option" value="2" <?php if(isset($l2op_fraction[2]) && $l2op_fraction[2]==1){ ?> checked="checked" <?php } ?>/>
                           {{trans('labels.formblfraction')}}
                        </div>

                        <input type="hidden" name="countRadio" value="2" id="countRadio"/>
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-sm-2 control-label"></label>
                    <!-- <div class="col-sm-2">
                        <a href="javascript:void(0);" class="btn btn-success " name="add" id="add">
                            <span class="glyphicon glyphicon-plus"> </span>
                        </a>
                    </div> -->
                </div>
                <div>
                    <span class="sec-popup help_noti"><a id="school-questions-page" href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom" onmouseover="getHelpText('school-questions-page')"><i class="icon-question"></i></a></span>
                    <div id="pop1" class="hide popoverContent">
                        <span class="school-questions-page"></span>
                    </div>
                </div>
                <div class="form-group">
                        <label for="pf_parent" class="col-sm-2 control-label">{{trans('labels.formlblapptitude')}}</label>
                        <div class="col-sm-10 school-select">
                             <?php $apptitude = Helpers::getActiveApptitude(); ?>
                            <select class="form-control" id="l2ac_apptitude" name="l2ac_apptitude">
                                    <option value="" >{{trans('labels.formlblselectapptitude')}}</option>
                                    <?php foreach ($apptitude as $key => $value) {                                          
                                    ?>
                                    <option value="{{$value->id}}" <?php if($apt_name != ''){if($value->id == $apt_name) echo 'selected';}?>> {{$value->apt_name}}</option>                                          
                            <?php } ?>
                            </select>
                        </div>
                </div>
                <div class="form-group">
                    <label for="pf_parent" class="col-sm-2 control-label">{{trans('labels.formlblinterest')}}</label>
                    <div class="col-sm-10 school-select">
                         <?php $interest = Helpers::getActiveInterest();  ?> 
                        <select class="form-control" id="l2ac_interest" name="l2ac_interest">
                                <option value="" >{{trans('labels.formlblselectinterest')}}</option>
                                <?php foreach ($interest as $key => $value) {
                                  
                                    ?>
                                     <option value="{{$value->id}}" <?php if($it_name != ''){if($value->id == $it_name){ echo 'selected'; } }?>>{{$value->it_name}}</option>

                        <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pf_parent" class="col-sm-2 control-label">{{trans('labels.formlblmi')}}</label>
                    <div class="col-sm-10 school-select">
                         <?php $multipleinterest = Helpers::getActiveMultipleIntelligent();  ?>
                        <select class="form-control" id="l2ac_multipleintelligent" name="l2ac_multipleintelligent">
                                <option value="" >{{trans('labels.formlblselectmi')}}</option>
                                <?php foreach ($multipleinterest as $key => $value) {
                                                                            
                                ?>
                                <option value="{{$value->id}}" <?php if($mit_name != ''){if($value->id == $mit_name) echo 'selected';}?>>{{$value->mit_name}}</option>                                        
                        <?php } ?>
                        </select>
                    </div>
                </div> 
                <div class="form-group">
                    <label for="pf_parent" class="col-sm-2 control-label">{{trans('labels.formlblpersonality')}}</label>
                    <div class="col-sm-10 school-select">
                         <?php  $personality = Helpers::getActivePersonality();  ?>
                        <select class="form-control" id="l2ac_personality" name="l2ac_personality">
                                <option value="" >{{trans('labels.formlblselectpersonality')}}</option>
                                <?php foreach ($personality as $key => $value) {
                                  
                                 ?>
                                     <option value="{{$value->id}}" <?php if($pt_name != ''){if($value->id == $pt_name) echo 'selected';}?>>{{$value->pt_name}}</option>                                       
                        <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblsection')}}</label>
                    <div class="col-sm-6 school-select">
                        <select class="form-control" id="section_type" name="section_type">
                            <option value="{{Config::get('constant.LEVEL2_SECTION_4')}}">Section 4</option>
                        </select>
                    </div>
                </div> -->
                <div class="form-group">
                    <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                    <div class="col-sm-6 school-select">
                        <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
                                <?php foreach ($staus as $key => $value) { ?>
                                    <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>

                            </select>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                <a class="btn btn-danger btn-flat pull-right" href="{{ url('school/questions') }}{{$page}}"> {{trans('labels.cancelbtn')}}</a>
            </div><!-- /.box-footer -->
        </form>
        </div>
    </div>
</div>

@stop
 <script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
@section('script')

<script type="text/javascript">
   
   function delete_action(no)
     {
            $('#delete_action_'+no).remove();
            return false;
       }
    $(document).ready(function()
    {
        $validate=jQuery.noConflict();
        
        $('#ex1').slider({
            formatter: function(value) 
            {
               return 'Current value: ' + value;
            },
            
        });
        var originalVal;

        $('#ex1').slider().on('slideStart', function(ev){
            originalVal = $('#ex1').data('slider').getValue();
        });

        $('#ex1').slider().on('slideStop', function(ev){
            var newVal = $('#ex1').data('slider').getValue();
            if(originalVal != newVal) {
                $('#label_point').text(newVal);
            }
        });
        
        $('.slider-track').click(function(){
            var sliderval = $('.tooltip-inner').html();
            $("#dSAnalysisSliderValue").text(sliderval);
         });
       
        $('#add').click(function()
         {
            var countRadio=$('#countRadio').val();
            var countRaw=$('#countRaw').val();
            var divid='delete_action_'+countRaw;
            
            var option = '<div class="form-group" id="'+divid+'">'+
                          '<div class="col-sm-2"></div>'+
                          '<div class="col-sm-5">' +
                               '<input type="text" class="form-control" id="l2op_option" name="l2op_option[]" placeholder="{{trans("labels.formlbloption")}}" value="" />' +
                          '</div>'+
                          '<div class="col-sm-1">'+
                               '<input type="radio" name="l2rad_option" value="'+countRadio+'">' +
                               '<?php  echo trans('labels.formblfraction'); ?>'+
                          '</div>'+
                          '<div class="col-sm-1" onclick="delete_action('+countRaw+');">'+
                            '<a href="javascript:void(0);" class="btn btn-success " name="minus">'+
                                '<span class="glyphicon glyphicon-minus"> </span>'+
                            '</a>'+
                            '</div>'+
                      '</div>';
              
            $('#addoption').append(option);
            
            countRadio=parseInt(countRadio)+1; 
            $('#countRadio').val(countRadio); 
            countRaw=parseInt(countRaw)+1; 
            $('#countRaw').val(countRaw); 
            
         });


        var validationRules =
        {
                l2ac_text : {
                    required : true
                },
                l2ac_points : {
                    required : true
                },
                'l2op_option[]' : {
                    required : true
                },
                section_type : {
                    required : true
                },
                deleted : {
                    required : true
                }

        }


        $validate("#addLevel2Activity").validate({
            rules : validationRules,
            messages : {
                l2ac_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                l2ac_points : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                'l2op_option[]' : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                section_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        })

    });
    function getHelpText(helpSlug)
    {
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('school/get-help-text')}}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'helpSlug':helpSlug},
            success: function(response) {
                $("."+helpSlug).text(response);    
                showPopover(helpSlug);
            }
        });
    }

    function showPopover(helpSlug) {
        $('#'+helpSlug).popover({
            html:true,
            content : function() { 
                return $( $(this).data("popover-content") ).html();
            }
        });
    }
</script>
@stop