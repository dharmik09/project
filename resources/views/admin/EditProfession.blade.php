@extends('layouts.admin-master')

@section('content')
<link rel="stylesheet" href="{{ URL::asset('backend/css/jquery.tag-editor.css') }}">
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professions')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($professionDetail) && !empty($professionDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.profession')}}</h3>
                </div><!-- /.box-header -->
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="addProfession" class="form-horizontal" method="post" action="{{ url('/admin/saveProfession') }}" enctype="multipart/form-data">
                    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail->pf_logo : '' ?>">
                    <input type="hidden" name="hidden_video" value="<?php echo (isset($professionDetail) && !empty($professionDetail)) ? $professionDetail->pf_video : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                        <?php
                        if (old('pf_name'))
                            $pf_name = old('pf_name');
                        elseif ($professionDetail)
                            $pf_name = $professionDetail->pf_name;
                        else
                            $pf_name = '';
                        ?>
                       <div class="form-group">
                            <label for="pf_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id=pf_name"" name="pf_name" placeholder="{{trans('labels.formlblname')}}" value="{{$pf_name}}" minlength="3" maxlength="100"/>
                            </div>
                        </div>

                        <?php
                        if (old('pf_basket'))
                            $pf_basket = old('pf_basket');
                        elseif ($professionDetail)
                            $pf_basket = $professionDetail->pf_basket;
                        else
                            $pf_basket = '';
                        ?>
                        <div class="form-group">
                            <label for="pf_basket" class="col-sm-2 control-label">Primary Basket</label>
                            <div class="col-sm-10">
                                <?php $baskets = Helpers::getActiveBaskets();?>
                                <select class="form-control" id="pf_basket" name="pf_basket">
                                  <option value="">{{trans('labels.formlblselectbasket')}}</option>
                                     <?php foreach ($baskets as $key => $value) {
                                        ?>
                                            <option value="{{$value->id}}" <?php if($pf_basket == $value->id) echo 'selected'; ?>>{{$value->b_name}}</option>
                                        <?php
                                     }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php
                        if (isset($professionDetail) && !empty($professionDetail))
                        {
                            if($professionDetail->pf_related_basket != '' && $professionDetail->pf_related_basket != 0){
                                $pf_basket_arr = explode(',',$professionDetail->pf_related_basket);
                            }else{
                                $pf_basket_arr = array(); 
                            }
                        }
                        else
                        {
                            $pf_basket_arr = array(); 
                        }
                                                   
                        ?>
                        <div class="form-group">
                            <label for="pf_basket" class="col-sm-2 control-label">Secondary Basket</label>
                            <div class="col-sm-10">
                                <?php $baskets = Helpers::getActiveBaskets();?>
                                <select class="form-control chosen-select" id="pf_basket" name="pf_related_basket[]" multiple="multiple" data-placeholder="Choose a Secondary basket...">
                                  <option value="">{{trans('labels.formlblselectbasket')}}</option>
                                     <?php foreach ($baskets as $key => $value) {
                                        ?>
                                  <option value="{{$value->id}}" <?php if(in_array($value->id, $pf_basket_arr)) echo 'selected'; ?>>{{$value->b_name}}</option>
                                        <?php
                                     }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php
                        if (old('pf_profession_alias'))
                            $pf_profession_alias = old('pf_profession_alias');
                        elseif ($professionDetail)
                            $pf_profession_alias = $professionDetail->pf_profession_alias;
                        else
                            $pf_profession_alias = '';
                        ?>
                        
                        <div class="form-group">
                            <label for="pf_basket" class="col-sm-2 control-label">Enter alias</label>
                            <div class="col-sm-10">
                                <textarea name="pf_profession_alias" class="form-control" id="pf_profession_alias" >{{$pf_profession_alias}}</textarea>
                            </div>
                        </div>
                        

                        <!--<?php
                        if (old('pf_intro'))
                            $pf_intro = old('pf_intro');
                        elseif ($professionDetail)
                            $pf_intro = $professionDetail->pf_intro;
                        else
                            $pf_intro = '';
                        ?>
                        <div class="form-group">
                            <label for="pf_intro" class="col-sm-2 control-label">{{trans('labels.formlblprofessionintro')}}</label>
                            <div class="col-sm-10">
                                <textarea name="pf_intro" id="pf_intro" >{{$pf_intro}}</textarea>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="pf_logo" class="col-sm-2 control-label">{{trans('labels.formlbllogo')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="pf_logo" name="pf_logo" onchange="readURL(this);"/>
                                <?php
                                if(isset($professionDetail->id) && $professionDetail->id != '0'){
                                    if(File::exists(public_path($uploadProfessionThumbPath.$professionDetail->pf_logo)) && $professionDetail->pf_logo != '') { ?><br>
                                        <img src="{{ url($uploadProfessionThumbPath.$professionDetail->pf_logo) }}" alt="{{$professionDetail->pf_logo}}" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    <?php }else{ ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php   }
                                    }
                                ?>
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>

                        <?php
                        if (old('pf_video_type'))
                            $pf_video_type = old('pf_video_type');
                        elseif ($professionDetail)
                            $pf_video_type = $professionDetail->pf_video_type;
                        else
                            $pf_video_type = '';
                        $style = '';
                        $styleYoutube = '';
                        $styleVimeo = '';
                        if($pf_video_type != "1")
                        {
                            $style = 'style="display:none;"';
                        }
                        if($pf_video_type != "2")
                        {
                            $styleYoutube = 'style="display:none;"';
                        }
                        if($pf_video_type != "3")
                        {
                            $styleVimeo = 'style="display:none;"';
                        }

                        ?>
                        <div class="form-group" id="video">
                            <label for="pf_video_type" class="col-sm-2 control-label">{{trans('labels.formlblvideo')}}</label>
                            <div class="col-sm-6">
                                 <label class="radio-inline"><input type="radio" name="pf_video_type" id="pf_video_type" value="2" <?php if($pf_video_type == "2")  echo 'checked="checked"'; ?>/>{{trans('labels.formblyoutube')}}</label>
                            </div>
                        </div>

                        
                        <?php
                          if (old('pf_video'))
                              $pf_video = old('pf_video');
                          elseif ($professionDetail)
                              $pf_video = $professionDetail->pf_video;
                          else
                              $pf_video = '';
                         ?>
                        <div class="form-group" id="youtube" <?php echo $styleYoutube;?>>
                            <label for="youtube" class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="youtube" name="youtube" placeholder="{{trans('labels.formlblyoutube')}}" value="<?php if($pf_video_type == '2') {?>{{$pf_video}} <?php }?>" />
                                <?php
                                if(isset($professionDetail->id) && $professionDetail->id != '0' && $professionDetail->pf_video != ''){
                                    $videoCode = Helpers::youtube_id_from_url($professionDetail->pf_video); 
                                    
                                     if(($pf_video_type == '2')) {?><br> 
                                     @if($videoCode == '')
                                     <video width="640" height="360" controls autoplay>
                                            <!-- MP4 must be first for iPad! -->
                                            <source src="{{$professionDetail->pf_video}}" type="video/mp4"  /><!-- Safari / iOS, IE9 -->
                                            <!-- fallback to Flash: -->
                                            <object width="640" height="360" type="application/x-shockwave-flash" data="player.swf">
                                                    <!-- Firefox uses the `data` attribute above, IE/Safari uses the param below -->
                                                    <param name="movie" value="player.swf" />
                                                    <param name="flashvars" value="autostart=true&amp;controlbar=over&amp;image=poster.jpg&amp;file=http://clips.vorwaerts-gmbh.de/VfE_flash.mp4" />
                                                    <!-- fallback image -->
                                                    <img src="poster.jpg" width="640" height="360" alt="Big Buck Bunny"
                                                             title="No video playback capabilities, please download the video below" />
                                            </object>
                                    </video>
                                     @else
                                     <iframe  id="pf_video" width="500" height="300"
                                      	     src="https://www.youtube.com/embed/{{$videoCode}}" >
                                      	</iframe >
                                     @endif
                                         
                                    <?php }
                                    }
                            ?>
                            </div>
                        </div>

                        
                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($professionDetail)
                            $deleted = $professionDetail->deleted;
                        else
                            $deleted = '';
                        ?>
                        <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professions') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script src="{{ URL::asset('backend/js/jquery.caret.min.js') }}"></script>
<script src="{{ URL::asset('backend/js/jquery.tag-editor.js') }}"></script>

<script type="text/javascript">
    /*CKEDITOR.replace( 'pf_intro' );   */
    jQuery(document).ready(function() {
        $('#pf_profession_alias').tagEditor({
                placeholder: 'Enter alias ...',
                maxLength : 255
            });
            
        var config = {
            '.chosen-select'           : {},
            '.chosen-select-deselect'  : {allow_single_deselect:true},
            '.chosen-select-no-single' : {disable_search_threshold:10},
            '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            '.chosen-select-width'     : {width:"95%"}
          }
          for (var selector in config) {
            $(selector).chosen(config[selector]);
          }
    
        <?php if(isset($professionDetail->id) && $professionDetail->id != '0') { ?>
            var validationRules = {
                pf_name : {
                    required : true
                },
                pf_basket : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                pf_name : {
                    required : true
                },
                pf_basket : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addProfession").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                pf_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pf_basket : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });

    $("#video input:radio").click(function() {

            var pf_video = this.value;
            if (pf_video == '1')
            {
                $('#normal').show();
                $('#youtube').hide();
                $('#vimeo').hide();
            }
            else if (pf_video == '2' )
            {
                $('#youtube').show();
                $('#normal').hide();
                $('#vimeo').hide();
            }
            else if (pf_video == '3' )
            {
                $('#vimeo').show();
                $('#normal').hide();
                $('#youtube').hide();
            }
            else
            {
                $('#normal').hide();
                $('#youtube').hide();
                $('#vimeo').hide();
            }
    });

</script>
@stop