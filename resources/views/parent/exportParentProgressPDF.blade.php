<html>
<head>
<style>
    @page { margin: 90px 50px; }
    #header { position: fixed; top: -80px; right: 0px;  height: 60px; border-bottom: 1px solid ;padding-bottom: 15px;}
    .clearfix {
        clear: both;
    }
    #footer { position: fixed; left: 0px; bottom: -80px;  right: 0px; height: 40px;  border-top: 1px solid;}
    #footer .page:after { content: counter(page); padding-left : 380px;}
    .pagebreak {
        page-break-after:always;
        position: relative;
    }

</style>
</head>
<body>

<div id="header">
    <table>
        <tr>
            <td width="80px">
                <div><img src="{{ Storage::url('frontend/images/proteen_logo.png')}}" alt="" width="58px"/></div>
            </td>
            <td>
                <span><h2>TEEN ANALYTICS</h2></span>
            </td>
        </tr>
    </table>
</div>

<div id="footer" class="clearfix">
     <p class="page">Copyright &copy; <?php echo date('Y');?> <span style="color:#E66A45;"> ProTeen</span>. All rights reserved.</p>
</div>

<div class="clearfix"></div>
<div>
    <table>
        <tr>
            <td width="50px">
                <div><img src="{{$basicDetail['photo']}}" alt="" width="60px" height="60px"/></div>
            </td>
            <td width="600px">
                <div style="text-align:center; font-size: 24px;font-weight: bold; padding-top:10px;"> {{$basicDetail['name']}}</div>
            </td>
        </tr>
    </table>
</div>

<div style="text-align:right;"><h3>Date :&nbsp;<?php echo date('F jS, Y');?></h3></div>

<div class="clearfix">
    <div style="width: 500px; float:left;">
        @if(isset($basicDetail) && !empty($basicDetail))
        <table width="600px" cellspacing='6'>
            <tr>
                <td><b>Name</b></td>
                <td>{{$basicDetail['name']}}</td>
            </tr>
            <tr>
                <td><b>Nick Name</b></td>
                <td>{{$basicDetail['nickname']}}</td>
            </tr>
            <tr>
                <td><b>Email Id</b></td>
                <td>{{$basicDetail['email']}}</td>
            </tr>
            <tr>
                <td><b>ProTeen Id</b></td>
                <td>{{$basicDetail['unique_id']}}</td>
            </tr>
            @else
            <tr><td colspan="3">Not Attempted Level 1 yet...</td></tr>
            @endif
        </table>
    </div>
    <div style="float:left; margin-right:50px;">
        @if(isset($booster) && !empty($booster))
        <table width="240px" cellpadding="10">
            <tr bgcolor="#f58634">
                <td>Level 1</td>
                <td>{{ $booster['Level1'] }}</td>
            </tr>
            <tr bgcolor="#5cc6d0">
                <td>Level 2</td>
                <td>{{ $booster['Level2'] }}</td>
            </tr>
            <tr bgcolor="#cc93ad">
                <td>Level 3</td>
                <td>{{ $booster['Level3'] }}</td>
            </tr>
            <tr bgcolor="#fdd1a1">
                <td>Level 4</td>
                <td>{{ $booster['Level4'] }}</td>
            </tr>
            <tr bgcolor="#f58634">
                <td>Total Points</td>
                <td>{{ $booster['total'] }}</td>
            </tr>
        </table>
        @endif
    </div>
</div>

<div class="clearfix">
    <h2>L-1 Results &amp; Trends</h2>
</div>

<div class="clearfix">
    <table width="720px" border="1" cellpadding="5" style="font-family:Open Sans;">
        <tr>
            <th>Questions</th>
            <th>Teen Response</th>
            <th>Teen Trends</th>
        </tr>
        @if(isset($level1result) && !empty($level1result))
        @foreach($level1result as $key=>$val)
        <tr align="center">
            <td>{{$val['question_text']}}</td>
            <td>{{$val['teen_anwer']}}</td>
            <td>{{$val['trend']}}</td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="3">Not Attempted Level 1 yet...</td></tr>
        @endif
    </table>
</div>

<div class="pagebreak">
    <h2>L-1 ICONS Voted</h2>
    @if(isset($teenagerMyIcons) && !empty($teenagerMyIcons))
    <div>
        <?php $column_count = 0; ?>
        @foreach($teenagerMyIcons as $key=>$image)
            <div style="width:150px; text-align:center; display:inline-block; margin:0 10px 10px 0; ">
                <img src="{{$image}}" alt="" width="75px" height="75px" >
            </div>
            <?php
                $column_count++;
                if ($column_count == 4) {
                    $column_count = 0;
                    echo '<br>';
                }
            ?>
        @endforeach
    </div>
    @else
    <div style="margin: 40px 0px;">No Selected Icon</div>
    @endif
</div>

<div class="pagebreak">
    <div>
        <h2>L-2 PROMISE Interests </h2>
    </div>
    @if(isset($teenagerInterest) && !empty($teenagerInterest))
    <div>
        @foreach($teenagerInterest as $ket=>$val)
        <div style="margin:10px 0px 10px 0;" class="clearfix">
            <div style="width: 120px; float:left; padding: 10px; align: center; margin: 3px;border:2px solid #ff6b45; border-radius: 10px;">
                <img src="{{$val['image']}}" alt="" width="100px" height="100px">
            </div>
            <div style="width: 200px; float:left; margin: 3px; padding-left: 10px;">
                {{ $val['interest'] }}
            </div>
        </div>
        </br>
        @endforeach
    </div>

    @else
    <div  style="margin: 40px 0px;text-align: center;">No Interest data found</div>
    @endif
</div>

<div class="clearfix pagebreak">
    <div>
        <h2>L-2 PROMISE Multiple Intelligences</h2>
    </div>
    @if(isset($teenagerMI) && !empty($teenagerMI))

    <div>
        @foreach($teenagerMI as $ket=>$val)
        <div style="margin:10 0px 10px 0;" class="clearfix">
              <div style="width:120px; padding: 10px; float:left; align: center; margin: 3px; border:2px solid #ff6b45; border-radius: 10px;">
                  <img src="{{$val['image']}}" alt="" width="100px" height="100px">
              </div>
              <div style="width:200px; float:left;margin: 3px; padding-left: 10px;">
                  <span style="font-weight:bold;">{{ $val['aptitude'] }}</span><br/>
                  <span>Score : &nbsp; </span><span >{{ $val['scale'] }}</span>
              </div>
              <div style="width: 300px; float:left;margin: 3px;">
                  <span >{{ $val['info'] }}</span>
              </div>
        </div>
        <br/>
        @endforeach
    </div>
    @else
    <div >No data found</div>
    @endif
</div>

@if(isset($attempted_profession) && !empty($attempted_profession))
    <div class="clearfix pagebreak">
    <div class="clearfix">
        <table cellspacing="15px" cellpadding="5px">
        <tr align="left">
            <th colspan="2"><h2>L3 & L4 Professions Explored</h2></th>
            <th><h2>Academic Path</h2></th>
        </tr>
        @foreach($attempted_profession as $key => $value)
        <tr>
            <td width="200px" style="border-bottom:2px solid #E66A45;">
                <?php
                    $badges = [];
                    if (isset($value['badges'][0]['newbie']) && $value['badges'][0]['newbie'] != '') {
                        $badges[] = $value['badges'][0]['newbie'];
                    }
                    if (isset($value['badges'][0]['apprentice']) && $value['badges'][0]['apprentice'] != '') {
                        $badges[] = $value['badges'][0]['apprentice'];
                    }
                    if (isset($value['badges'][0]['wizard']) && $value['badges'][0]['wizard'] != '') {
                        $badges[] = $value['badges'][0]['wizard'];
                    }
                    foreach($badges as $key => $image) {
                    ?>
                        <span>&nbsp;&nbsp;</span><span><img src="{{$image}}" alt="" width="60px" height="60px"/></span>
                    <?php
                    }
                ?>
            </td>
            <td width="180px" style="border-bottom:2px solid #E66A45;">
                <span>Profession &nbsp; : &nbsp;{{ $value['name'] }}</span>
            </td>
            <td width="150px" style="border-bottom:2px solid #E66A45;">
                {!! strip_tags($value['profession_acadamic_path'], "<p></p><br/><ul></ul><li></li><sub></sub><sup></sup><span></span>") !!}
            </td>
        </tr>
        @endforeach
        </table>
    </div>
    @else
    <div>No professions attempted</div>
</div>
@endif

@if(isset($allProPromisePlus) && !empty($allProPromisePlus))
<div class="pagebreak"></div>
    <div class="clearfix">
        <h2>PROMISE Plus</h2>
    </div>
    <?php $column_count = 0;$page_count = 0; $page = 0;$countArray = count($allProPromisePlus);?>
     @foreach($allProPromisePlus AS $k => $p_value)
        @if ($page_count == 0)
        <table>
        @endif
        @if($column_count == 0)
        <tr>
        @endif
            <td>
                <div style="display:block;border:1px solid #ff6b45;width:199px;padding:7px;height:360px;margin:5px 10px;">
                    <div style="margin:5px;padding-bottom:10px;text-align:center;">
                    <div ><span style="border-bottom: 2px solid #e66a45;padding:5px 0;display:inline-block;">{{$p_value['profession_name']}}</span></div>
                </div>
                <div style="text-align:center;">
                    <div>L-2 PROMISE</div>
                    <div style="padding-top: 10px;padding-bottom: 10px;">
                        <?php  if($p_value['level2Promise'] == 'nomatch') {
                        ?>
                            <img src="{{ Storage::url('frontend/images/Look_Elsewhere_LL.png')}}" width="50px" height="50px" alt="">
                        <?php
                        }?>
                        <?php if($p_value['level2Promise'] == 'moderate') {
                        ?>
                            <img src="{{ Storage::url('frontend/images/Possible_Choice_MM.png')}}" width="50px" height="50px" alt="">
                        <?php
                        }?>
                        <?php  if($p_value['level2Promise'] == 'match') {
                        ?>
                            <img src="{{ Storage::url('frontend/images/Fitting_Choice_HH.png')}}" width="50px" height="50px" alt="">
                        <?php
                        }?>
                    </div>
                </div>
                <?php  if ($p_value['promisePlus'] != '') {?>
                  <div style="text-align:center;">
                      <div style="padding-bottom:10px;"><span style="border-top: 2px solid #e66a45;padding:5px 0;display:inline-block;">&nbsp;&nbsp;L-4 PROMISE Plus&nbsp;&nbsp;</span></div>
                      <div>
                      <?php  if($p_value['level2Promise'] == 'match') {
                      ?>

                          <table style="width:100%;text-align:center;">
                            <tr>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Fitting_Choice_HH.png')}}" alt="" <?php if($p_value['promisePlus'] == 'match') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Stretch_Yourself_HM.png')}}" alt="" <?php if($p_value['promisePlus'] == 'moderate') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Secondary_Choice_HL.png')}}" alt="" <?php if($p_value['promisePlus'] == 'nomatch') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                            </tr>
                          </table>
                      <?php
                      } if($p_value['level2Promise'] == 'moderate'){
                      ?>
                          <table style="width:100%;text-align:center;">
                            <tr>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Growth_Option_MH.png')}}" alt=""  <?php if($p_value['promisePlus'] == 'match') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Possible_Choice_MM.png')}}" alt="" <?php if($p_value['promisePlus'] == 'moderate') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Stretch_Yourself_ML.png')}}" alt="" <?php if($p_value['promisePlus'] == 'nomatch') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                            </tr>
                          </table>
                      <?php
                      } if($p_value['level2Promise'] == 'nomatch') {
                      ?>
                          <table style="width:100%;text-align:center;">
                            <tr>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Surprise_Match_LH.png')}}" alt="" <?php if($p_value['promisePlus'] == 'match') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?> ></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Secondary_Choice_LM.png')}}" alt="" <?php if($p_value['promisePlus'] == 'moderate') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?> ></td>
                              <td style="vertical-align: middle;"><img src="{{ Storage::url('frontend/images/Look_Elsewhere_LL.png')}}" alt=""  <?php if($p_value['promisePlus'] == 'nomatch') { echo 'width="70px" height="70px"';} else { echo 'width="40px" height="40px"'; }?>></td>
                            </tr>
                          </table>
                      <?php
                      }?>
                      </div>
                  </div>
                  <?php  if (!empty($p_value['level4Data'])) {?>
                  <div>
                      <div style="font-weight:bold;padding-top: 10px;">
                          {{$p_value['level4Data'][0]->ps_text}}
                      </div>
                      <div style="font-size:13px;">
                          {{$p_value['level4Data'][0]->ps_description}}
                      </div>
                  </div>
                  <?php
                  }
                  } else {
                  ?>
                  <div style="border-top: 2px solid #e66a45;padding:5px;">
                      <span>{{trans('labels.nodatainps')}}</span>
                  </div>
                  <?php
                  }?>
                </div>
                <?php $column_count++;$page_count++;$page++?>
            </td>
        @if ($column_count == 3)
        </tr>
            @if ($page == $countArray && $page_count != 6)
            </table>
            <div class="pagebreak"></div>
            @endif
        @endif
        @if($page_count == 6)
        </table>
        <div class="pagebreak"></div>
        @endif
        @if ($page == $countArray && $column_count != 3 && $page_count != 6)
            </tr>
            </table>
            <div class="pagebreak"></div>
        @endif
        <?php if($column_count == 3) {$column_count = 0;}  if($page_count == 6) {$page_count = 0;$column_count = 0;}?>
     @endforeach
     @else
     <div>No Data Found</div>
@endif

@if(isset($userLearningData) && !empty($userLearningData))
    <div class="clearfix">
        <h2>Learning Guidance</h2>
    </div>
    <div style="width: 60%;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td style="vertical-align: middle;"><span style="background-color: #33CC00;height: 10px;width: 10px;margin-left: 10px;display: inline-block;vertical-align: middle;">&nbsp;</span></td>
                            <td style="vertical-align: middle;"><span style="vertical-align: middle;">Easy</span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td style="vertical-align: middle;"><span style="background-color: #0051BA;height: 10px;width: 10px;margin-left: 10px;display: inline-block;vertical-align: middle;">&nbsp;</span></td>
                            <td style="vertical-align: middle;"><span style="vertical-align: middle;">Medium</span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td style="vertical-align: middle;"><span style="background-color: #FF6600;height: 10px;width: 10px;margin-left: 10px;display: inline-block;vertical-align: middle;">&nbsp;</span></td>
                            <td style="vertical-align: middle;"><span style="vertical-align: middle;">Tough</span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td style="vertical-align: middle;"><span style="background-color: #6d6d6d;height: 10px;width: 10px;margin-left: 10px;display: inline-block;vertical-align: middle;">&nbsp;</span></td>
                            <td style="vertical-align: middle;"><span style="vertical-align: middle;">N/A</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <table style="width:100%;border-collapse: collapse;" border="1" cellpadding="5">
    @foreach($userLearningData AS $k => $l_value)
        @if (stripos($l_value->ls_name, "factual") !== false)
        @if (stripos($l_value->ls_name, "remembering") !== false)
        <tr style="background: #cecece;">
            <td style="vertical-align: middle;text-align: center;">
                <div><img src="{{ Storage::url('frontend/images/Factual.png')}}" alt="" height="50" width="50" ></div>
                <div><span style="font-size: 17px;font-weight:bold;">Factual</span></div>
            </td>
            <td>
              <div style="text-align: left;">Basic elements an individual must know to be acquainted with a subject or  solve problems in it</div>
            </td>
        </tr>
        @endif
        <tr style="color:<?php if (($l_value->interpretationrange) == 'Low') {echo '#f60;';} else if (($l_value->interpretationrange) == 'Medium') {echo '#0051ba;';} else if (($l_value->interpretationrange) == 'High' ) {echo '#33cc00;';} else { echo '#6d6d6d;';}?>">
            <td style="vertical-align: middle;text-align: center;">
                 <div><img src="{{$l_value->ls_image}}" alt="" height="50" width="50"></div>
                 <div><span style="font-size: 17px;font-weight:bold;">  <?php $l_value->ls_name = str_replace('factual',' ',$l_value->ls_name);?>{{ucwords (str_replace('_',' ',$l_value->ls_name))}} </span></div>
            </td>
            <td>
                <div style="text-align: left;">{!! nl2br(e($l_value->ls_description)) !!}</div>
            </td>
        </tr>
        @endif
        @if (stripos($l_value->ls_name, "conceptual") !== false)
        @if (stripos($l_value->ls_name, "remembering") !== false)
        <tr height="20"><td></td><td></td></tr>
        <tr style="background: #cecece;">
            <td style="vertical-align: middle;text-align: center;">
                <div><img src="{{ Storage::url('frontend/images/Conceptual.png')}}" alt="" height="50" width="50" ></div>
                <div><span style="font-size: 17px;font-weight:bold;">Conceptual</span></div>
            </td>
            <td>
              <div style="text-align: left;">The inter-relationships among the basic elements within a larger structure that enable them to function together</div>
            </td>
        </tr>
        @endif
        <tr style="color:<?php if (($l_value->interpretationrange) == 'Low') {echo '#f60;';} else if (($l_value->interpretationrange) == 'Medium') {echo '#0051ba;';} else if (($l_value->interpretationrange) == 'High' ) {echo '#33cc00;';} else { echo '#6d6d6d;';}?>">
            <td style="vertical-align: middle;text-align: center;">
                 <div><img src="{{$l_value->ls_image}}" alt="" height="50" width="50"></div>
                 <div><span style="font-size: 17px;font-weight:bold;">  <?php $l_value->ls_name = str_replace('conceptual',' ',$l_value->ls_name);?>{{ucwords (str_replace('_',' ',$l_value->ls_name))}} </span></div>
            </td>
            <td>
                <div style="text-align: left;">{!! nl2br(e($l_value->ls_description)) !!}</div>
            </td>
        </tr>
        @endif
        @if (stripos($l_value->ls_name, "procedural") !== false)
        @if (stripos($l_value->ls_name, "remembering") !== false)
        <tr height="20"><td></td><td></td></tr>
        <tr style="background: #cecece;">
            <td style="vertical-align: middle;text-align: center;">
                <div><img src="{{ Storage::url('frontend/images/Procedural.png')}}" alt="" height="50" width="50" ></div>
                <div><span style="font-size: 17px;font-weight:bold;">Procedural</span></div>
            </td>
            <td>
              <div style="text-align: left;">How to do something, methods of enquiry and criteria for using skills, algorithms, techniques and methods</div>
            </td>
        </tr>
        @endif
        <tr style="color:<?php if (($l_value->interpretationrange) == 'Low') {echo '#f60;';} else if (($l_value->interpretationrange) == 'Medium') {echo '#0051ba;';} else if (($l_value->interpretationrange) == 'High' ) {echo '#33cc00;';} else { echo '#6d6d6d;';}?>">
            <td style="vertical-align: middle;text-align: center;">
                 <div><img src="{{$l_value->ls_image}}" alt="" height="50" width="50"></div>
                 <div><span style="font-size: 17px;font-weight:bold;">  <?php $l_value->ls_name = str_replace('procedural',' ',$l_value->ls_name);?>{{ucwords (str_replace('_',' ',$l_value->ls_name))}} </span></div>
            </td>
            <td>
                <div style="text-align: left;">{!! nl2br(e($l_value->ls_description)) !!}</div>
            </td>
        </tr>
        @endif
        @if (stripos($l_value->ls_name, "meta_cognitive") !== false)
        @if (stripos($l_value->ls_name, "remembering") !== false)
        <tr height="20"><td></td><td></td></tr>
        <tr style="background: #cecece;">
            <td style="vertical-align: middle;text-align: center;">
                <div><img src="{{ Storage::url('frontend/images/Metacognitive.png')}}" alt="" height="50" width="50" ></div>
                <div><span style="font-size: 17px;font-weight:bold;">Meta-Cognitive</span></div>
            </td>
            <td>
              <div style="text-align: left;">Knowledge of cognition - the mental process of acquiring knowledge and understanding through thought, experience, and the senses in general, as well as awareness and knowledge of one's own cognition.</div>
            </td>
        </tr>
        @endif
        <tr style="color:<?php if (($l_value->interpretationrange) == 'Low') {echo '#f60;';} else if (($l_value->interpretationrange) == 'Medium') {echo '#0051ba;';} else if (($l_value->interpretationrange) == 'High' ) {echo '#33cc00;';} else { echo '#6d6d6d;';}?>">
            <td style="vertical-align: middle;text-align: center;">
                 <div><img src="{{$l_value->ls_image}}" alt="" height="50" width="50"></div>
                 <div><span style="font-size: 17px;font-weight:bold;">  <?php $l_value->ls_name = str_replace('meta_cognitive',' ',$l_value->ls_name);?>{{ucwords (str_replace('_',' ',$l_value->ls_name))}} </span></div>
            </td>
            <td>
                <div style="text-align: left;">{!! nl2br(e($l_value->ls_description)) !!}</div>
            </td>
        </tr>
        @endif
    @endforeach
    </table>
    @else
    <div>No Data Found</div>
@endif

<div class="pagebreak"></div>
<div class="clearfix" style="margin-top:30px">
    <h2>Activity Timeline</h2>
    <div>
        <table cellpadding="10" width="400px">
            <?php
            $timeLine = Helpers::getTeenagerTimeLine($basicDetail['id']);
            ?>
            @if(isset($timeLine) && !empty($timeLine))
            <?php $flag = 0; ?>
            @foreach($timeLine as $data)

            <tr bgcolor="#f58634">
                <td>{{$data['date']}}</td>
                <td>{{$data['timeLineText']}}</td>
            </tr>
            <?php
            $flag++;
            if ($flag > 3) {
                $flag = 0;
            }
            ?>
            @endforeach
            @endif
        </table>
    </div>
</div>

<div>
    <h2>Achievement Record</h2>
    <div>
        @if(isset($achievement) && !empty($achievement))
            @foreach($achievement as $key => $value)
                <div>
                    <span>{{$value['meta_value']}}</span>
                </div>
                <br/>
            @endforeach
        @else
        <div>No Data Found</div>
        @endif
    </div>
</div>

<div>
    <h2>Academic Record</h2>
    <div>
        @if(isset($education) && !empty($education))
            @foreach($education as $key => $value)
                <div>
                    <span>{{$value['meta_value']}}</span>
                </div>
                <br/>
            @endforeach
        @else
        <div>No Data Found</div>
        @endif
    </div>
</div>

</body>
</html>