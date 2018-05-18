@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.teenagers')}}
    </h1>
</section>
<section class="content">
    <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
    <div class="box-footer">
        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/teenagers') }}{{$page}}">{{trans('labels.backlbtn')}}</a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation"  <?php if ($type == "basicdetails"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/basicdetails" aria-controls="home">Basic Details</a></li>
                        <li role="presentation"  <?php if ($type == "level1"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/level1" aria-controls="profile">Level1 Details</a></li>
                        <li role="presentation"  <?php if ($type == "level2"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/level2" aria-controls="messages">Level2 Details</a></li>
                        <li role="presentation"  <?php if ($type == "promisescore"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/promisescore" aria-controls="api">PROMISE Score</a></li>
                        <li role="presentation"  <?php if ($type == "level3"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/level3" aria-controls="settings">Teen Career Suggestion</a></li>
                        <li role="presentation"  <?php if ($type == "level4"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/level4" aria-controls="level4">Level4 Details</a></li>
                        <li role="presentation"  <?php if ($type == "points"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/points" aria-controls="points">Booster Points</a></li>
                        <li role="presentation"  <?php if ($type == "learningstyle"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/learningstyle" aria-controls="learningstyle">Learning Guidance</a></li>
                        <li role="presentation"  <?php if ($type == "activityTimeline"){echo 'class="active"';} ?> ><a href="{{url('admin/view-teenager')}}/{{$id}}/activityTimeline" aria-controls="activityTimeline">Activity Timeline</a></li>
                    </ul>

                    <div class="tab-content">

                        <!-- Basic Details Start  -->
                        @if ($type == "basicdetails")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'basicdetails'){echo 'active';} ?>" id="home">
                            <div class="box-body">
                                <div class="form-group clearfix">
                                    <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblname')}}</label>
                                    <div class="col-sm-4">
                                        {{$viewTeenDetail->t_name}}
                                    </div>
                                </div>

                                <?php
                                    if(isset($viewTeenDetail->t_nickname))
                                        { ?>
                                            <div class="form-group clearfix">
                                                <label for="t_nickname" class="col-sm-6 control-label">{{trans('labels.formlblnickname')}}</label>
                                                <div class="col-sm-6">
                                                    {{$viewTeenDetail->t_nickname}}
                                                </div>
                                            </div>
                                <?php } ?>

                                <?php
                                    if(isset($viewTeenDetail->t_email))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblemail')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_email}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <div class="form-group clearfix">
                                   <label for="cat_name" class="col-sm-6 control-label">Gender</label>
                                    <div class="col-sm-4">
                                        {{($viewTeenDetail->t_gender == 1?'Male':'Female')}}
                                    </div>
                                </div>

                                <?php 
                                    if($viewTeenDetail->t_uniqueid != 0 && !empty($viewTeenDetail->t_uniqueid) && isset($viewTeenDetail->t_uniqueid))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlbluniqueid')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_uniqueid}}
                                            </div>
                                        </div>
                                <?php } ?>
                                <div class="form-group clearfix">
                                    <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblphoto')}}</label>
                                    <div class="col-sm-6">
                                        @if(isset($viewTeenDetail->id) && $viewTeenDetail->id != '0')
                                            <?php
                                                $image_user = ($viewTeenDetail->t_photo != "" && Storage::disk('s3')->exists(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$viewTeenDetail->t_photo)) ? Config::get('constant.DEFAULT_AWS').Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$viewTeenDetail->t_photo : asset('/backend/images/proteen_logo.png');
                                            ?>
                                            <img src="{{$image_user}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                        @endif
                                    </div>
                                </div>
                                <?php
                                    if($viewTeenDetail->t_school != 0 && !empty($viewTeenDetail->t_school) && isset($viewTeenDetail->t_school))
                                        { ?>
                                            <div class="form-group clearfix">
                                                <?php $school = Helpers::getActiveSchoolById($viewTeenDetail->t_school); ?>
                                                <label for="category_type" class="col-sm-6 control-label">{{trans('labels.formlblschool')}}</label>
                                                <div class="col-sm-6">
                                                    <?php echo $school[0]['sc_name']; ?>
                                                </div>
                                            </div>
                                <?php } ?>

                                <?php
                                    if($viewTeenDetail->t_social_identifier != 0 && !empty($viewTeenDetail->t_social_identifier) && isset($viewTeenDetail->t_social_identifier))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblsocialidentifier')}}</label>
                                                <div class="col-sm-6">
                                                    {{$viewTeenDetail->t_social_identifier}}
                                                </div>
                                            </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_phone != 0 && !empty($viewTeenDetail->t_phone) && isset($viewTeenDetail->t_phone))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblphone')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_phone}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_birthdate != 0 && !empty($viewTeenDetail->t_birthdate) && isset($viewTeenDetail->t_birthdate))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblbdate')}}</label>
                                            <div class="col-sm-6">
                                                {{date('d/m/Y',strtotime($viewTeenDetail->t_birthdate))}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_country != 0 && !empty($viewTeenDetail->t_country) && isset($viewTeenDetail->t_country))
                                    { ?>
                                        <div class="form-group clearfix">
                                        <?php
                                            $country = Helpers::getActiveCountryById($viewTeenDetail->t_country);
                                        ?>
                                        <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblCountry')}}</label>
                                            <div class="col-sm-6">
                                                <?php echo $country[0]->c_name; ?>
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_pincode != 0 && !empty($viewTeenDetail->t_pincode) && isset($viewTeenDetail->t_pincode))
                                    { ?>
                                    <div class="form-group clearfix">
                                        <label for="t_pincode" class="col-sm-6 control-label">{{trans('labels.formlblpincode')}}</label>
                                        <div class="col-sm-6">
                                            {{$viewTeenDetail->t_pincode}}
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_location != 0 && !empty($viewTeenDetail->t_location) && isset($viewTeenDetail->t_location))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlbllocation')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_location}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if(isset($viewTeenDetail->t_credit) && $viewTeenDetail->t_credit != 0 && !empty($viewTeenDetail->t_credit) && isset($viewTeenDetail->t_credit))
                                    { ?>
                                        <div class="form-group clearfix">
                                            <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblcredit')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_credit}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_boosterpoints != 0 && !empty($viewTeenDetail->t_boosterpoints) && isset($viewTeenDetail->t_boosterpoints))
                                    { ?>
                                            <div class="form-group clearfix">
                                                <label for="cat_name" class="col-sm-6 control-label">{{trans('labels.formlblboosterpoints')}}</label>
                                                <div class="col-sm-6">
                                                    {{$viewTeenDetail->t_boosterpoints}}
                                                </div>
                                            </div>
                                <?php } ?>

                                <?php
                                    if($viewTeenDetail->t_sponsor_choice != 0 && !empty($viewTeenDetail->t_sponsor_choice) && isset($viewTeenDetail->t_sponsor_choice))
                                    { ?>
                                        <div class="form-group clearfix" id="sponsor">
                                            <label for="t_sponsor_choice" class="col-sm-6 control-label">{{trans('labels.formlblsponsorchoice')}} Choice</label>
                                            <div class="col-sm-6">
                                                {!!($viewTeenDetail->t_sponsor_choice == 1)?'<span style="color:#dd4b39;font-weight: bold;">Self<span>':'Self'!!}&nbsp;&nbsp;
                                                {!!($viewTeenDetail->t_sponsor_choice == 2)?'<span style="color:#dd4b39;font-weight: bold;">Sponsor</span>':'Sponsor'!!}&nbsp;&nbsp;
                                                {!!($viewTeenDetail->t_sponsor_choice == 3)?'<span style="color:#dd4b39;font-weight: bold;">None</span>':'None'!!}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php
                                    if(isset($viewTeenDetail->t_sponsors) && !empty($viewTeenDetail->t_sponsors)){
                                        $sponsor = '';
                                        foreach($viewTeenDetail->t_sponsors as $key1=>$spon)
                                        {
                                            $sponsor .= $spon->sp_company_name."<br/>";
                                        }
                                    }else{
                                        $sponsor = '';
                                    }
                                ?>
                                <?php
                                    if(isset($viewTeenDetail->parentcounsellor) && !empty($viewTeenDetail->parentcounsellor)){
                                        $parent = '';
                                        $counsellor = '';
                                        foreach($viewTeenDetail->parentcounsellor as $key2=>$data2)
                                        {
                                            if($data2->p_user_type == 1){
                                            $parent .= $data2->p_first_name.' '.$data2->p_last_name.' -- '.$data2->p_email."<br/>";
                                            }else{
                                            $counsellor .= $data2->p_first_name.' '.$data2->p_last_name.' -- '.$data2->p_email."<br/>";
                                            }
                                        }
                                    }else{
                                        $parent = '-';
                                        $counsellor = '-';
                                    }
                                ?>

                                <div class="form-group clearfix">
                                    <label for="t_isverified" class="col-sm-6 control-label">Selected Sponsor</label>
                                    <div class="col-sm-6">
                                        {!!$sponsor!!}
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <label for="t_isverified" class="col-sm-6 control-label">Selected Parent</label>
                                    <div class="col-sm-6">
                                        {!!$parent!!}
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <label for="t_isverified" class="col-sm-6 control-label">Selected Counsellor</label>
                                    <div class="col-sm-6">
                                        {!!$counsellor!!}
                                    </div>
                                </div>

                                <?php 
                                    if($viewTeenDetail->t_device_token != 0 && !empty($viewTeenDetail->t_device_token) && isset($viewTeenDetail->t_device_token))
                                      { ?>
                                        <div class="form-group clearfix">
                                            <label for="t_device_token" class="col-sm-6 control-label">{{trans('labels.formlbldevicetoken')}}</label>
                                            <div class="col-sm-6">
                                                {{$viewTeenDetail->t_device_token}}
                                            </div>
                                        </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_device_type != 0 && !empty($viewTeenDetail->t_device_type) && isset($viewTeenDetail->t_device_type))
                                    { ?>
                                      <div class="form-group clearfix">
                                        <label for="t_device_type" class="col-sm-6 control-label">{{trans('labels.formlbldevicetype')}}</label>
                                            <div class="col-sm-6">
                                                {!!($viewTeenDetail->t_device_type == 1)?'<span style="color:#dd4b39;font-weight: bold;">iOS</span>':'iOS'!!}&nbsp;&nbsp;
                                                {!!($viewTeenDetail->t_device_type == 2)?'<span style="color:#dd4b39;font-weight: bold;">Android</span>':'Android'!!}&nbsp;&nbsp;
                                                {!!($viewTeenDetail->t_device_type == 3)?'<span style="color:#dd4b39;font-weight: bold;">Web</span>':'Web'!!}
                                            </div>
                                      </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_social_provider != '' && !empty($viewTeenDetail->t_social_provider) && isset($viewTeenDetail->t_social_provider))
                                    { ?>
                                    <div class="form-group clearfix">
                                        <label for="t_isverified" class="col-sm-6 control-label">Registration Type</label>
                                            <div class="col-sm-6">
                                            <?php echo $viewTeenDetail->t_social_provider; ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php 
                                    if($viewTeenDetail->t_isverified != 0 && !empty($viewTeenDetail->t_isverified) && isset($viewTeenDetail->t_isverified))
                                    { ?>
                                            <div class="form-group clearfix">
                                                <label for="t_isverified" class="col-sm-6 control-label">Verified Status</label>
                                                    <div class="col-sm-6">
                                                    <?php echo "Yes"; ?>
                                                </div>
                                            </div>
                                <?php } ?>

                                <?php
                                    if ($viewTeenDetail->deleted == '1') {
                                        $active_status = 'Yes';
                                    } else if (($viewTeenDetail->deleted == '2')) {
                                        $active_status = 'No';
                                    }
                                ?>
                                    
                                <div class="form-group clearfix">
                                    <label for="t_isverified" class="col-sm-6 control-label">Active Status</label>
                                    <div class="col-sm-6">
                                        <?php echo $active_status; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- Basic Details End  -->

                        <!-- Level1 Details Start  -->
                        @if ($type == "level1")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'level1'){echo 'active';} ?>" id="profile">
                            <div class="interest pull-right">
                                <strong>L1 total points : <?php echo $boosterPoints['Level1']; ?></strong>
                            </div>
                            
                            @forelse($l1Activity as $l1)
                                <ul>
                                    <li>
                                        <div class="teen_base_question">
                                            {{$l1->l1ac_text}}
                                        </div>
                                        <div class="teen_base_answer">
                                            - &nbsp;{{$l1->l1op_option}}
                                        </div>
                                    </li>
                                </ul>
                            @empty
                                <div><?php echo "no records found"; ?></div>
                            @endforelse

                            <div class="interest" style="width: 600px;">
                                <h3>L1 selected Icon's</h3>
                                @forelse($teenagerMyIcons as $key=>$image)
                                   <div class="" style="float: left;margin:5px;padding:5px;border: 1px solid #dddddd;">
                                        <img src="{{$image['image']}}" alt="" width="80px" height="80px"><br/>
                                        <span>Icon Name : </span>{{$image['iconname']}}<br/>
                                        <span>Category : </span>{{$image['category']}}
                                    </div>
                                @empty
                                    <div><?php echo "no records found"; ?></div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                        <!-- Level1 Details End  -->

                        <!-- Level2 Details Start  -->
                        @if ($type == "level2")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'level2'){echo 'active';} ?>" id="messages">
                            <div class="interest pull-right">
                                <strong>L2 total points : <?php echo $boosterPoints['Level2']; ?></strong>
                            </div>
                            <div>
                                <h4><strong>&nbsp;&nbsp;Profile Builder 1</strong></h4>
                            </div>
                            <div>
                                @forelse($l2ActivitySection1 as $l2)
                                    <ul>
                                        <li>
                                            <div class="teen_base_question">
                                                {{$l2->l2ac_text}}
                                            </div>
                                            <div class="teen_base_answer">
                                                - &nbsp;{{$l2->l2op_option}}
                                            </div>
                                        </li>
                                    </ul>
                                @empty
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "No records found"; ?></div>
                                @endforelse
                            </div>
                            <div>
                                <h4><strong>&nbsp;&nbsp;Profile Builder 2</strong></h4>
                            </div>
                            <div>
                                @forelse($l2ActivitySection2 as $l2)
                                    <ul>
                                        <li>
                                            <div class="teen_base_question">
                                                {{$l2->l2ac_text}}
                                            </div>
                                            <div class="teen_base_answer">
                                                - &nbsp;{{$l2->l2op_option}}
                                            </div>
                                        </li>
                                    </ul>
                                @empty
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;No records found</div>
                                @endforelse
                            </div>
                            <div>
                                <h4><strong>&nbsp;&nbsp;Profile Builder 3</strong></h4>
                            </div>
                            <div>
                                @forelse($l2ActivitySection3 as $l2)
                                    <ul>
                                        <li>
                                            <div class="teen_base_question">
                                                {{$l2->l2ac_text}}
                                            </div>
                                            <div class="teen_base_answer">
                                                - &nbsp;{{$l2->l2op_option}}
                                            </div>
                                        </li>
                                    </ul>
                                @empty
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;No records found</div>
                                @endforelse
                            </div>
                            <div>
                                <h4><strong>&nbsp;&nbsp;Profile Builder 4</strong></h4>
                            </div>
                            <div>
                                @forelse($l2ActivitySection4 as $l2)
                                    <ul>
                                        <li>
                                            <div class="teen_base_question">
                                                {{$l2->l2ac_text}}
                                            </div>
                                            <div class="teen_base_answer">
                                                - &nbsp;{{$l2->l2op_option}}
                                            </div>
                                        </li>
                                    </ul>
                                @empty
                                    <div>&nbsp;&nbsp;&nbsp;&nbsp;No records found</div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                        <!-- Level2 Details End -->
    
                        <!-- PROMISE Score Start -->
                        @if ($type == "promisescore")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'promisescore'){echo 'active';} ?>" id="api">
                            @if(isset($finalMIParameters) && !empty($finalMIParameters))
                                @foreach($finalMIParameters as $ket=>$val)
                                    <div class="interest">
                                        <img src="{{$val['image']}}" alt="" style="width: 80px; height: 80px;">
                                        <div class="detail_container">
                                            <span class="title">{{$val['aptitude']}}</span>
                                            <div class="inner_detail_container">
                                                <span>{{$val['scale']}}</span>
                                                <span>{{$val['score']}}</span>
                                            </div>
                                         </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no_data">No data found</div>
                            @endif
                        </div>
                        @endif
                        <!-- PROMISE Score End -->
                        
                        <!-- Level3 Details Start -->
                        @if ($type == "level3")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'level3'){echo 'active';} ?>" id="settings">
                            <div role="tabpanel" class="tab-pane" id="messages">
                                
                                @forelse($careerConsideration as $professionArray)

                                    <div class="interest">
                                        <?php
                                            $professionImage = Storage::url($professionImagePath.$professionArray['pf_logo']);
                                        ?>
                                        <img src="{{$professionImage}}" class="user-image" alt="Default Image" style="width: 80px; height: 80px;">
                                        <div class="detail_container">
                                            <span class="title">{{$professionArray['pf_name']}}</span><br/>
                                            <span class="title">
                                                Color Code :
                                                 @if($professionArray['match_scale'] == 'match')
                                                <strong style="color:#33cc00;">{{$professionArray['match_scale']}}</strong>
                                                @elseif($professionArray['match_scale'] == 'moderate')
                                                <strong style="color:#0051ba;">{{$professionArray['match_scale']}}</strong>
                                                @else
                                                <strong style="color:#ff6600;">{{$professionArray['match_scale']}}</strong>
                                                @endif
                                            </span><br/>
                                            
                                            <div class="inner_detail_container">

                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div><?php echo "no records found"; ?></div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                        <!-- Level3 Details End -->

                        <!-- Level4 Details Start -->
                        @if ($type == "level4")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'level4'){echo 'active';} ?>" id="level4">
                            <div role="tabpanel" class="tab-pane" id="level4">
                                <div class="interest pull-right">
                                    <strong>L4 total points : <?php echo $boosterPoints['Level4']; ?></strong>
                                    <!-- <div class="detail_container">
                                        <span class="title">&nbsp;</span>
                                        <div class="inner_detail_container"></div>
                                    </div> -->
                                </div>
                                @forelse($level4Data as $level4)
                                    <div class="interest">
                                        <?php
                                            $level4_image = ($level4['pf_logo'] != "" && Storage::disk('s3')->exists($professionOriginalImageUploadPath.$level4['pf_logo'])) ? Config::get('constant.DEFAULT_AWS').$professionOriginalImageUploadPath.$level4['pf_logo'] : asset('/backend/images/proteen_logo.png');
                                        ?>
                                        <img src="{{$level4_image}}" class="user-image" style="width: 80px; height: 80px;" />
                                        <div class="detail_container">
                                            <span class="title" style="font-weight: bold;">{{$level4['pf_name']}}</span>
                                            <div class="" style="border-top: 1px solid #dddddd;">
                                                <span style="font-size: 14px;">Basic : </span>
                                                <span style="font-size: 14px;font-weight: bold;">{{$level4['level4Basic']['earnedPoints']}}</span>
                                            </div>
                                            <div class="" style="border-top: 1px solid #dddddd;">
                                                <span style="font-weight: normal;font-size: 14px;">Intermediate : </span>
                                                <span style="font-size: 14px;font-weight: bold;">{{$level4['level4Intermediate']['earnedPoints']}}</span>
                                                </div>
                                            <div class="" style="border-top: 1px solid #dddddd;">
                                                 <span style="font-weight: normal;font-size: 14px;">Advanced : </span>
                                                <span style="font-size: 14px;font-weight: bold;">{{$level4['level4Advance']['earnedPoints']}}</span>
                                            </div>

                                        </div>
                                    </div>
                                @empty
                                    <div><?php echo "no records found"; ?></div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                        <!-- Level4 Details End -->

                        <!-- Booster Points Start -->
                        @if ($type == "points")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'points'){echo 'active';} ?>" id="points">
                            <div role="tabpanel" class="tab-pane" id="displaypoints">
                                <ul>
                                    <li>
                                        <div class="teen_base_question">
                                            <?php echo "Level1"; ?>
                                        </div>
                                        <div class="teen_base_answer">
                                            <?php echo $boosterPoints['Level1']; ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="teen_base_question">
                                            <?php echo "Level2"; ?>
                                        </div>
                                        <div class="teen_base_answer">
                                            <?php echo $boosterPoints['Level2']; ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="teen_base_question">
                                            <?php echo "Level3"; ?>
                                        </div>
                                        <div class="teen_base_answer">
                                            <?php echo $boosterPoints['Level3']; ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="teen_base_question">
                                            <?php echo "Level4"; ?>
                                        </div>
                                        <div class="teen_base_answer">
                                            <?php echo $boosterPoints['Level4']; ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="teen_base_question">
                                            <?php echo "Total Points:"; ?>
                                        </div>
                                        <div class="teen_base_answer">
                                            <?php echo $boosterPoints['Total']; ?>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @endif
                        <!-- Booster Points End -->

                        <!-- Learning Guidance Start -->
                        @if ($type == "learningstyle")
                        <div role="tabpanel" class="tab-pane <?php if ($type == 'learningstyle'){echo 'active';} ?>" id="learningstyle">
                            <div role="tabpanel" class="tab-pane" id="displaylearningstyle">
                                <div class="clearfix">
                                    @foreach($userLearningData as $key=>$data)
                                        @if (stripos($data->ls_name, "factual") !== false)
                                        @if (stripos($data->ls_name, "remembering") !== false)
                                        <div class="box-header with-border col-xs-12">
                                            <div class="products-list product-list-in-box">
                                                <div class="item">
                                                    <div class="col-md-1 col-sm-3 col-xs-3">
                                                        <img src="{{ asset('frontend/images/Factual.png')}}" alt="" style="display: block;max-width:70px;margin:0 auto;">
                                                    </div>
                                                    <div class="col-md-11 col-sm-9 col-xs-9" style="">
                                                        <div><h3 class="box-title" style="font-weight:bold;">Factual</h3></div>
                                                        <div><h3 class="box-title">Basic elements an individual must know to be acquainted with a subject or  solve problems in it</h3></div>
                                                    </div>
                                                </div>
                                             <!-- /.item -->
                                            </div>
                                        </div>
                                        @endif
                                        <div class="box-body col-md-6 col-sm-6 col-xs-12">
                                            <div class="products-list">
                                                <div class="well clearfix <?php if (($data->interpretationrange) == 'Low') {echo 'cst_low';} else if (($data->interpretationrange) == 'Medium') {echo 'cst_mid';} else if (($data->interpretationrange) == 'High' ) {echo 'cst_high';} else { echo 'cst_normal';}?>" style="margin:0;">
                                                    <div class="product-img col-md-2 col-sm-3 col-xs-3">
                                                       <img src="{{$data->ls_image}}" alt="" height="50" width="50">
                                                    </div>
                                                    <div class="product-info col-md-10 col-sm-9 col-xs-9" style="margin:0px;">
                                                        <div style="font-weight:bold;"><?php $data->ls_name = str_replace('factual',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}}</div>
                                                        <div>{!! nl2br(e($data->ls_description)) !!}</div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                        </div>
                                        @endif
                                        @if (stripos($data->ls_name, "conceptual") !== false)
                                        @if (stripos($data->ls_name, "remembering") !== false)
                                        <div class="box-header with-border col-xs-12">
                                            <div class="products-list product-list-in-box">
                                                <div class="item">
                                                    <div class="col-md-1 col-sm-3 col-xs-3">
                                                        <img src="{{ asset('frontend/images/Conceptual.png')}}" alt="" style="display: block;max-width:70px;margin:0 auto;">
                                                    </div>
                                                    <div class="col-md-11 col-sm-9 col-xs-9" style="">
                                                       <div><h3 class="box-title" style="font-weight:bold;">Conceptual</h3></div>
                                                       <div><h3 class="box-title">The inter-relationships among the basic elements within a larger structure that enable them to function together</h3></div>
                                                    </div>
                                                </div>
                                             <!-- /.item -->
                                            </div>
                                        </div>
                                        @endif
                                        <div class="box-body col-md-6 col-sm-6 col-xs-12">
                                            <div class="products-list">
                                                <div class="well clearfix <?php if (($data->interpretationrange) == 'Low') {echo 'cst_low';} else if (($data->interpretationrange) == 'Medium') {echo 'cst_mid';} else if (($data->interpretationrange) == 'High' ) {echo 'cst_high';} else { echo 'cst_normal';}?>" style="margin:0;">
                                                    <div class="product-img col-md-2 col-sm-3 col-xs-3">
                                                        <img src="{{$data->ls_image}}" alt="" height="50" width="50">
                                                    </div>
                                                    <div class="product-info col-md-10 col-sm-9 col-xs-9" style="margin:0px;">
                                                        <div style="font-weight:bold;"><?php $data->ls_name = str_replace('conceptual',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}}</div>
                                                        <div>{!! nl2br(e($data->ls_description)) !!}</div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                         </div>
                                        @endif
                                        @if (stripos($data->ls_name, "procedural") !== false)
                                        @if (stripos($data->ls_name, "remembering") !== false)
                                        <div class="box-header with-border col-xs-12">
                                            <div class="products-list product-list-in-box">
                                                <div class="item">
                                                    <div class="col-md-1 col-sm-3 col-xs-3">
                                                       <img src="{{ asset('frontend/images/Procedural.png')}}" alt="" style="display: block;max-width:70px;margin:0 auto;">
                                                    </div>
                                                    <div class="col-md-11 col-sm-9 col-xs-9" style="">
                                                       <div><h3 class="box-title" style="font-weight:bold;">Procedural</h3></div>
                                                       <div><h3 class="box-title">How to do something, methods of enquiry and criteria for using skills, algorithms, techniques and methods</h3></div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                        </div>
                                        @endif
                                        <div class="box-body col-md-6 col-sm-6 col-xs-12">
                                            <div class="products-list">
                                               <div class="well clearfix <?php if (($data->interpretationrange) == 'Low') {echo 'cst_low';} else if (($data->interpretationrange) == 'Medium') {echo 'cst_mid';} else if (($data->interpretationrange) == 'High' ) {echo 'cst_high';} else { echo 'cst_normal';}?>" style="margin:0;">
                                                    <div class="product-img col-md-2 col-sm-3 col-xs-3">
                                                        <img src="{{$data->ls_image}}" alt="" height="50" width="50">
                                                    </div>
                                                    <div class="product-info col-md-10 col-sm-9 col-xs-9" style="margin:0px;">
                                                        <div style="font-weight:bold;"><?php $data->ls_name = str_replace('procedural',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}}</div>
                                                        <div>{!! nl2br(e($data->ls_description)) !!}</div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                         </div>
                                        @endif
                                        @if (stripos($data->ls_name, "meta_cognitive") !== false)
                                        @if (stripos($data->ls_name, "remembering") !== false)
                                        <div class="box-header with-border col-xs-12">
                                            <div class="products-list product-list-in-box">
                                                <div class="item">
                                                    <div class="col-md-1 col-sm-3 col-xs-3">
                                                        <img src="{{ asset('frontend/images/Metacognitive.png')}}" alt="" style="display: block;max-width:70px;margin:0 auto;">
                                                    </div>
                                                    <div class="col-md-11 col-sm-9 col-xs-9" style="">
                                                        <div><h3 class="box-title" style="font-weight:bold;">Meta-Cognitive</h3></div>
                                                        <div><h3 class="box-title">Knowledge of cognition - the mental process of acquiring knowledge and understanding through thought, experience, and the senses in general, as well as awareness and knowledge of one's own cognition.</h3></div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                        </div>
                                        @endif
                                        <div class="box-body col-md-6 col-sm-6 col-xs-12">
                                            <div class="products-list">
                                                <div class="well clearfix <?php if (($data->interpretationrange) == 'Low') {echo 'cst_low';} else if (($data->interpretationrange) == 'Medium') {echo 'cst_mid';} else if (($data->interpretationrange) == 'High' ) {echo 'cst_high';} else { echo 'cst_normal';}?>" style="margin:0;">
                                                    <div class="product-img col-md-2 col-sm-3 col-xs-3">
                                                        <img src="{{$data->ls_image}}" alt="" height="50" width="50">
                                                    </div>
                                                    <div class="product-info col-md-10 col-sm-9 col-xs-9" style="margin:0px;">
                                                        <div style="font-weight:bold;"><?php $data->ls_name = str_replace('meta_cognitive',' ',$data->ls_name);?>{{ucwords (str_replace('_',' ',$data->ls_name))}}</div>
                                                        <div>{!! nl2br(e($data->ls_description)) !!}</div>
                                                    </div>
                                                </div>
                                               <!-- /.item -->
                                            </div>
                                         </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- Learning Guidance End -->

                        <!-- Activity Timeline start -->
                        @if ($type == "activityTimeline")
                            <table border="1px solid black">
                                <thead>
                                    <th>Date</th>
                                    <th>Activity</th>
                                </thead>
                                <tbody>
                                <?php
                                    $classArray = array('alpha', 'beta', 'gamma', 'delta');
                                    ?>
                                    @if(isset($timeLine) && !empty($timeLine))
                                    <?php $flag = 0; ?>
                                    @foreach($timeLine as $line=>$date)

                                    <tr>
                                        <td style="padding: 5px;  border: 1px solid black;">{{date('d, F Y',strtotime($date))}}</td>
                                        <td style="padding: 5px;  border: 1px solid black;">{{$line}}</td>
                                    </tr>
                                    <?php
                                    $flag++;
                                    if ($flag > 3) {
                                        $flag = 0;
                                    }
                                    ?>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        @endif
                        <!-- Activity Timeline End -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@stop