@extends('layouts.school-master')

@section('content')
    <div class="centerlize">
        <div class="container">
            <div class="container_padd school_progress">
                <div class="row">
                    <div class="col-md-3 col-sm-4">
                        <div class="select-style">
                            <?php $sid = Auth::guard('school')->user()->id; ?>
                            <select id="standard">
                                <?php
                                foreach($classDetails as $classDetail)
                                { ?>
                                    <option value="{{$classDetail->t_class}}" <?php if($classDetail->t_class == $cid) { echo 'selected="selected"'; } ?> >Class - {{$classDetail->t_class}}</option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="teen_drop report_download">
                            @if ($days != 0)
                                <div class="promisebtn timer_btn">
                                    <a href="javascript:void(0);" class="promise btn_golden_border reportbtn" title="" id="report">
                                        <span class="promiseplus" title="Requires pop-up enabled in browser">Report<i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                                        <span class="coinouter">
                                            <span class="coinsnum">{{$days}} Days Left</span>
                                        </span>
                                    </a>
                                </div>
                            @else
                                <div id="RdaysReport" >
                                  <div class="promisebtn">
                                    <a href="javascript:void(0)" style="margin-top: 0 ;" class="promise btn_golden_border reportbtn" id="report">
                                      <span class="promiseplus" title="Requires pop-up enabled in browser">Report<i class="fa fa-download" style="padding-left: 10px;" aria-hidden="true"></i></span>
                                      <span class="coinouter">
                                          <span class="coinsnum">{{$coins}}</span>
                                          <span class="coinsimg"><img src="{{Storage::url('frontend/images/coin-stack.png')}}">
                                          </span>
                                      </span>
                                    </a>
                                  </div>
                              </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9 col-sm-8">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="card">
                                    <span class="title">No. of Students Voting</span>
                                    <span id="level1" class="count">{{$teenDetailsForLevel1}}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card">
                                    <span class="title">No. of Students Building Profile</span>
                                    <span id="level2" class="count">{{$teenDetailsForLevel2}}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card">
                                    <span class="title">No of Students Exploring Careers</span>
                                    <span id="level3" class="count">{{$teenDetailsForLevel3}}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card">
                                    <span class="title">No. of Students Role Playing</span>
                                    <span id="level4" class="count">{{$teenDetailsForLevel4}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Slider -->
                <div class="profession_attempted">
                    <h2>Professions Role Played by Students</h2>
                    <div class="profession_attempted_carousel owl-carousel school_pro_image profession-attempted-img">
                        @forelse($professionAttempted['profession'] as $key => $value)
                        <div class="item ">
                            <?php  $profession_logo = '';
                            if (isset($value->pf_logo) && $value->pf_logo != '') {
                                $profession_logo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH') . $value->pf_logo);
                            } else {
                                $profession_logo = Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                            }?>
                            <img src="{{$profession_logo}}" alt="">
                            <span class="title">{{$value->pf_name}}</span>
                            <span class="count">
                                <?php
                                    $pf_id = $value->id;
                                    $totalProfessionByClass = Helpers::getCountForAttemptedProfession($pf_id,$sid,$cid);
                                    echo $totalProfessionByClass;
                                ?>
                            </span>
                        </div>
                        @empty
                            No Record Found....
                        @endforelse
                    </div>
                </div><!-- dashboard_inner_box End -->
                <div class="clearfix col-md-12">
                    <div class="row">
                        <div class="parent_h2_header col-xs-12">
                            <h2>Level 2 response by students</h2>
                        </div>
                    </div>
                    @if(isset($totalL2SchoolQuestions) && count($totalL2SchoolQuestions) > 0)
                        <div class="table_container fixed_box_type" style="height:300px;">
                            <table class="sponsor_table sponsor-progress-table">
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Questions</th>
                                    <th>Total No. of Teen given answer</th>
                                    <th>Total No. of Teen given correct answer</th>
                                </tr>
                                <?php $serialNo = 1; ?>
                                @foreach($totalL2SchoolQuestions as $totalL2SchoolQuestion)
                                <tr>
                                    <td>
                                        {{ $serialNo }}
                                    </td>
                                    <td>{{ $totalL2SchoolQuestion->l2ac_text }}</td>
                                    <?php $totalTeen = Helpers::getStudentForSchoolL2($totalL2SchoolQuestion->id, Auth::guard('school')->user()->id, $cid); ?>
                                    <td>{{ ($totalTeen) ? $totalTeen : 0 }}</td>
                                    <?php
                                    $correctAns = Helpers::getCorrectAnswerByL2Activity($totalL2SchoolQuestion->id); 
                                    $numOfTeenWithCorrectAns = 0;
                                    if (isset($correctAns) && !empty($correctAns)) {
                                        $numOfTeenWithCorrectAns = Helpers::getTotalStudentGivenCorrectAnswer($totalL2SchoolQuestion->id, Auth::guard('school')->user()->id, $cid, $correctAns->id);
                                    }
                                    ?>
                                    <td>{{ (isset($numOfTeenWithCorrectAns) && count($numOfTeenWithCorrectAns) > 0) ? count($numOfTeenWithCorrectAns) : 0 }}</td>
                                </tr>
                                <?php $serialNo++; ?>
                                @endforeach
                            </table>
                        </div>
                    @else
                        <div class="no_data col-xs-12" style="margin: 40px 0px;text-align:center;">No questions found</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
<div id="confirm" title="Congratulations!" style="display:none;">
    <div class="confirm_coins"></div><br/>
    <div class="confirm_detail"></div>
</div>
@stop
@section('script')
<script>
        jQuery(document).ready(function($) {
            $(".table_container").mCustomScrollbar({axis:"x"});
            $('.profession_attempted_carousel').owlCarousel({

                loop:false,
                nav:true,
                responsive:{
                    0:{
                        items:1
                    },
                    400:{
                        items:2
                    },
                    600:{
                        items:3
                    },
                    768:{
                        mouseDrag:false
                    }
                }
            });
        });

        $('#standard').on('change', function() {
            var cid = $(this).val();
            var progressURL = '<?php echo url('/school/progress')?>';
            window.location.href = "/school/progress/" + cid;
        });

        $(document).on('click', '#report', function (e) {

            var days = <?php echo $days; ?>;
            if (days != 0) {
                showReport();
            } else {
                $.ajax({
                    url: "{{ url('/school/get-available-coins') }}",
                    type: 'POST',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "schoolId": <?php if (Auth::guard('school')->check()) { echo Auth::guard('school')->user()->id; } else { echo 0;}?>
                    },
                    success: function(response) {
                        coins = response;
                        $.ajax({
                            url: "{{ url('/school/get-coins-for-school') }}",
                            type: 'POST',
                            data: {
                                "_token": '{{ csrf_token() }}',
                                "schoolId": <?php if (Auth::guard('school')->check()) { echo Auth::guard('school')->user()->id; } else { echo 0;}?>
                            },
                            success: function(response) {
                                if (response > 1) {
                                    if (days == 0) {
                                        $(".confirm_coins").text('<?php echo 'You have '; ?>' + format(response) + '<?php echo ' ProCoins available.'; ?>');
                                        $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(coins) + '<?php echo ' ProCoins and play on'; ?>');
                                        $.ui.dialog.prototype._focusTabbable = function(){};
                                        $( "#confirm" ).dialog({

                                        resizable: false,
                                        height: "auto",
                                        width: 400,
                                        draggable: false,
                                        modal: true,
                                        buttons: [
                                        	{
                                        		text: "Ok",
                                        		class : 'btn primary_btn',
                                        		click: function() {
                                        		  showReport();
                                        		  $( this ).dialog( "close" );
                                        		}
                                        	},
                                        	{
                                        		text: "Cancel",
                                        		class : 'btn primary_btn',
                                        		click: function() {
                                        		  $( this ).dialog( "close" );
                                        		  $(".confirm_coins").text(' ');
                                        		}
                                        	}
                                          ],
                                          open: function(event, ui) {
                                                $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                            }
                                        });
                                    } else {
                                        showReport();
                                    }
                                } else {
                                    $("#confirm").attr('title', 'Notification!');
                                    $(".confirm_coins").text('Not enough ProCoins. Please get them! Register as an "Enterprise" and buy "ProCoins Package" OR contact ProTeen for one time purchase');
                                    $.ui.dialog.prototype._focusTabbable = function(){};
                                    $( "#confirm" ).dialog({

                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    draggable: false,
                                    modal: true,
                                    buttons: [
                                    	{
                                    		text: "Buy Package",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                                var windowName = 'Console'; 
                                                var popUp = window.open('{{url("/sponsor")}}', windowName, 'width=1000, height=700, left=24, top=24, scrollbars, resizable');
                                                if (popUp == null || typeof(popUp)=='undefined') {  
                                                    alert('Please disable your pop-up blocker and click the "Open" link again.'); 
                                                } 
                                                else {  
                                                    popUp.focus();
                                                }
                                    		    $( this ).dialog( "close" );
                                    		}
                                    	},
                                    	{
                                    		text: "Buy One-Time",
                                    		class : 'btn primary_btn',
                                    		click: function() {
                                    		    window.location.href = "mailto:info@proteenlife.com";
                                    		    $( this ).dialog( "close" );
                                    		    $(".confirm_coins").text(' ');
                                    		}
                                    	}
                                      ],
                                      open: function(event, ui) {
                                            $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });
    
        $(document).on('click','.icon-close', function(){
            $( "#confirm" ).dialog( "close" );
        });

        function showReport() {
            var days = <?php echo $days; ?>;
            $.ajax({
                  url: "{{ url('/school/purchased-coins-to-view-report') }}",
                  type: 'POST',
                  data: {
                      "_token": '{{ csrf_token() }}',
                      "schoolId": <?php echo Auth::guard('school')->user()->id;?>
                  },
                  success: function(response) {
                        var windowName = 'Console'; 
                        var popUp = window.open('{{url("/school/export-pdf/")}}/{{$cid}}', windowName, 'width=1000, height=700, left=24, top=24, scrollbars, resizable');
                        if (popUp == null || typeof(popUp)=='undefined') {  
                            alert('Please disable your pop-up blocker and click the "Open" link again.'); 
                        } 
                        else {  
                            popUp.focus();
                        }
                        if (days == 0) {
                            getRemaningDaysForReport(<?php echo Auth::guard('school')->user()->id;?>);
                        }
                  }
              });
        }

        function getRemaningDaysForReport(parent_id) {
            $.ajax({
                url: "{{ url('/school/get-remaining-days-for-school') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "schoolId": <?php echo Auth::guard('school')->user()->id; ?>
                },
                success: function(response) {
                   $('#RdaysReport').html(response);
                   $('#RdaysReport').show();
                }
            });
        }

        function format(x) {
            return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

    </script>

@stop
