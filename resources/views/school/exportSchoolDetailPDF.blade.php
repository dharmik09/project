<html>
<head>
<style>
    @page { margin: 90px 50px; }
    #header { position: fixed; top: -80px; right: 0px;  height: 60px; border-bottom: 1px solid;padding-bottom: 15px;}
    .clearfix {
        clear: both;
    }
    #footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 40px; border-top: 1px solid;}
    #footer .page:after { content: counter(page); padding-left : 390px;}
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
                <span><h2>SCHOOL ANALYTICS</h2></span>
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
                <div><img src="{{Storage::url($logo)}}" alt="" width="60px" height="60px"/></div>
            </td>
            <td width="600px">
                <div style="text-align:center; font-size: 24px;font-weight: bold; padding-top:10px;"> {{Auth::guard('school')->user()->sc_name}}</div>
            </td>
        </tr>
    </table>
</div>

<div style="text-align:right;"><h3>Date :&nbsp;<?php echo date('F jS, Y');?></h3></div>

<div style="text-align:center;">
    <span style="font-size: 20px; font-weight:bold;">Class :&nbsp;{{$cid}}</span>
</div>

<div style="margin-top: 10px;">
    <div style="text-align: center;">
        <span style="font-size: 20px; font-weight:bold;">Students</span>
    </div>
    <div style="margin-top: 10px;">
        <table border="1" width="700px" cellpadding="10">
            <tr>
                <th>{{trans('labels.stuname')}}</th>
                <th>Student ID</th>
                <th>{{trans('labels.class')}}</th>
                <th>{{trans('labels.division')}}</th>
                <th>Email</th>
            </tr>
            @forelse($studentData as $teenDetail)
            <tr align="center">
                <td>{{$teenDetail->t_name}}</td>
                <td>{{$teenDetail->t_rollnum}}</td>
                <td>{{$teenDetail->t_class}}</td>
                <td>{{$teenDetail->t_division}}</td>
                <td>{{$teenDetail->t_email}}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5"><center>{{trans('labels.norecordfound')}}</center></td>
            </tr>
            @endforelse
        </table>
    </div>
</div>

<div style="margin-top: 30px;" class="clearfix">
    <h2>L-1 VOTE Trends</h2>
    <div >
       <table width="720px" border="1" cellpadding="5" style="font-family:Open Sans;">
        <tr>
            <th>Questions</th>
            <th>Response/Trends</th>
        </tr>
        @forelse($allQuestion as $key => $value)
            <tr>
                <td>{{$value['text']}}</td>
                <td cellspacing="10px">
                <?php
                    $trend = $value['trenddata'];
                    foreach ($trend as $trend => $trendsValue) {
                        ?>
                        <table>
                        <tr>
                            <td width="150px">{{$trend}}</td>
                            <td>&nbsp;&nbsp;<?php echo round($trendsValue, 2)." %";?></td>
                        </tr>
                        </table>
                        <?php
                    }
                ?>
            </td>
            </tr>
        @empty
            <tr><td colspan="3">No Record Found....</td></tr>
        @endforelse
        </table>
    </div>
</div><!-- dashboard_inner_box End -->

<div class="clearfix pagebreak">
    <h2>L-1 ICONS Voted</h2>
    @if(isset($teenagerMyIcons) && !empty($teenagerMyIcons))
    <div>
        <?php $column_count = 0; ?>
        @foreach($teenagerMyIcons as $key=>$image)
            <div style="width:150px; text-align:center; display:inline-block; margin:0 10px 10px 0; ">
                <img src="{{Storage::url($image)}}" alt="" width="75px" height="75px" class="circular" >
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



<div style="margin-top: 30px;" class="clearfix pagebreak">
    <div >
        <table cellspacing="10px">
        <tr>
            <th colspan="2"><h3>L3 & L4 Professions Explored</h3></th>
            <th><h3>No. of Students</h3></th>
            <!-- <th><h3>Academic Path</h3></th> -->
        </tr>
        @forelse($professionAttempted['profession'] as $key => $value)
        <tr>
            <td style="border-bottom:2px solid #E66A45;" width="90px">
                <div style="width:60px; padding: 10px; align: center; margin: 3px; border:2px solid #ff6b45; border-radius: 10px;">
                  <img src="{{$value->pf_logo}}" alt="" width="50px" height="50px">
              </div>
            </td>
            <td  width="180px" style="border-bottom:2px solid #E66A45;text-align:center;">
                {{$value->pf_name}}
            </td>
            <td style="border-bottom:2px solid #E66A45; text-align:center;" width="100px">
                <?php
                    $sid = Auth::guard('school')->user()->id;
                    $pf_id = $value->id;
                    $totalProfessionByClass = Helpers::getCountForAttemptedProfession($pf_id,$sid,$cid);
                    echo $totalProfessionByClass;
                ?>
            </td>
            <!-- <td style="border-bottom:2px solid #E66A45;">
                {!! strip_tags($value->profession_acadamic_path, "<p></p><br/><ul></ul><li></li><sub></sub><sup></sup><span></span>") !!}
            </td> -->
        </tr>
        @empty
            <tr><td colspan="3">No Record Found....</td></tr>
        @endforelse
        </table>
    </div>
</div><!-- dashboard_inner_box End -->

<div style="margin-top: 30px;" class="clearfix">
    <h2>Students Role Play Performance Analytics</h2>
    <div >
        <table cellspacing="10px">
        <tr align="left">
            <th>Professions Name</th>
            <th>Quiz</th>
            <th>Profession Tasks</th>
            <th>Real World Tasks</th>
        </tr>
        <tr><td></td><td></td></tr>
        @forelse($totalBadges as $key => $value)
        <tr>
            <td>
                {{$value['pf_name']}}
            </td>
            <td style="text-align:center;">
                {{$value['bacisbadges']}}
            </td>
            <td style="text-align:center;">
                {{$value['intermediatebadges']}}
            </td>
            <td style="text-align:center;">
                {{$value['advancebadges']}}
            </td>
        </tr>
        @empty
            <tr><td colspan="2">No Record Found....</td></tr>
        @endforelse
        </table>
    </div>
</div><!-- dashboard_inner_box End -->

<div class="pagebreak"></div>
<div style="margin-top: 30px;">
    <div>
        <h2>Gifted ProCoins</h2>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1" width="650px" cellpadding="7px">
        <tr align="center">
            <th>{{trans('labels.blheadgiftedto')}}</th>
            <th>{{trans('labels.giftedcoins')}}</th>
            <th>{{trans('labels.gifteddate')}}</th>
        </tr>
        @if(!empty($teenCoinsDetail))
        @foreach($teenCoinsDetail as $key=>$data)
        <tr align="center">
            <td>
                {{$data->t_name}}
            </td>
            <td>
                <?php echo number_format($data->tcg_total_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->tcg_gift_date)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="3">No data found</td></tr>
        @endif
    </table>
</div>

<div style="margin-top: 30px;">
    <div>
        <h2>ProCoins</h2>
    </div>
</div>

<div style="margin-top: 30px;">
    <table border="1" width="650px" cellpadding="7px">
        <tr align="center">
            <th>{{trans('labels.component')}}</th>
            <th>{{trans('labels.consumedcoins')}}</th>
            <th>{{trans('labels.consumedcoinsdate')}}</th>
            <th>{{trans('labels.enddate')}}</th>
        </tr>
        @if(!empty($deductedCoinsDetail))
        @foreach($deductedCoinsDetail as $key=>$data)
        <tr align="center">
            <td>
                {{$data->pc_element_name}}
            </td>
            <td>
                <?php echo number_format($data->dc_total_coins); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_start_date)); ?>
            </td>
            <td>
                <?php echo date('d M Y', strtotime($data->dc_end_date)); ?>
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="4"><?php echo "No record found..."; ?></td></tr>
        @endif
    </table>
</div>

<div style="margin-top: 30px;">
    <div>
        <h2>Students Response to School Profile Builder</h2>
    </div>
</div>
<div style="margin-top: 30px;">
    <table border="1" width="650px" cellpadding="7px">
        <tr align="center">
            <th>Sr. No</th>
            <th>Questions</th>
            <th>Question Type</th>
            <th>Answer Choices</th>
            <th>Total Student Responses</th>
            <th>Total Correct Responses</th>
        </tr>
        @if(count($l2ActivityResponse) > 0)
        @foreach($l2ActivityResponse as $l2Activity)
        <tr align="center">
            <td>
                {{ $l2Activity['serialNo'] }}
            </td>
            <td>
                {{ $l2Activity['l2ac_text'] }}
            </td>
            <td>
                <?php
                $flag = false;
                if(isset($l2Activity->l2ac_apptitude_type) && !empty($l2Activity->l2ac_apptitude_type) && $l2Activity->l2ac_apptitude_type != '' )
                {
                    $flag = false;
                    ?> <div>{{$l2Activity->apt_name}}</div> <?php
                } else {
                    $flag = true;
                }
                
                if(isset($l2Activity->l2ac_personality_type) && !empty($l2Activity->l2ac_personality_type) && $l2Activity->l2ac_personality_type != '' )
                {
                    $flag = false;
                    ?> <div>{{$l2Activity->pt_name}}</div> <?php
                } else {
                    $flag = true;
                }
                
                if(isset($l2Activity->l2ac_mi_type) && !empty($l2Activity->l2ac_mi_type) && $l2Activity->l2ac_mi_type != '' )
                {
                    $flag = false;
                    ?> <div>{{$l2Activity->mit_name}}</div> <?php
                } else {
                    $flag = true;
                }
                
                if(isset($l2Activity->l2ac_interest) && !empty($l2Activity->l2ac_interest) && $l2Activity->l2ac_interest != '' )
                {
                    $flag = false;
                   ?> <div>{{$l2Activity->it_name}}</div> <?php
                } else {
                    $flag = true;
                }
                echo ($flag) ? '-' : '';
                ?>
            </td>
            <td>
                <?php 
                $explodeOption = explode(',', $l2Activity['l2op_option']);
                $explodeFraction = explode(',', $l2Activity['l2op_fraction']);
                foreach($explodeOption as $key => $option_name)
                {
                    if (count($explodeFraction) > 0 && $explodeFraction[$key] == 1) { ?> 
                        <strong><span class="font-blue" title="This is correct answer"> 
                        <?php
                            echo $option_name."<br/>"; ?>
                        </span></strong>
                    <?php } else { 
                        echo $option_name."<br/>";
                    }
                }
                ?>
            </td>
            <td>
                {{ $l2Activity['total_given_answer'] }}
            </td>
            <td>
                {{ $l2Activity['total_given_correct_answer'] }}
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="4"><center>No record found...</center></td></tr>
        @endif
    </table>
</div>


</body>
</html>