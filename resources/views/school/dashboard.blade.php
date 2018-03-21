@extends('layouts.school-master')

@section('content')

@if(Session::has('invalidemails'))
<?php $invalidEmails = Session::get('invalidemails'); ?>
@if(!empty($invalidEmails))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.whoops')}}</h4>Below are the invalid emails so not imported into database
            <ul>
                @foreach($invalidEmails as $key=>$email)
                <li>{{ $email }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<?php Session::forget('invalidemails'); ?>
@elseif($message = Session::get('success'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-success alert-succ-msg alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
            {{ $message }}
        </div>
    </div>
</div>
@endif
@if($message = Session::get('error'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            {{ $message }}
        </div>
    </div>
</div>
@endif
<div class="col-md-12">
    <div class="row" id="errorGoneMsg"> </div>
</div>
<div class="centerlize">
    <div class="container">
        <div class="container_padd">
            <div class="header">
                <div class="credit"></div>

                <div class="button_container coins_button_container">
                    <div class="coin_summary cst_dsh clearfix">
                        <div class="right col-md-3 col-sm-4 col-xs-12">
                            <a href="{{ url('school/bulk-import') }}" class="btn primary_btn space_btm">Student Bulk Import</a>
                        </div>
                        <div class="left col-md-6 col-sm-4 col-xs-12">
                            <span class="coin_img"><img src="{{Storage::url('frontend/images/available_coin.png')}}" alt=""></span>
                            <span>{{trans('labels.availablecoins')}}</span>
                            <span class="coin_count_ttl">@if(!empty($schoolData)) <?php echo number_format($schoolData['sc_coins']);?> @endif</span>
                        </div>
                        <div class="dashboard_page col-md-3 col-sm-4 col-xs-12">
                            <span class="tool-tip" <?php if($schoolData['sc_coins'] == 0) echo 'data-toggle="tooltip" data-placement="bottom" title="Register as Enterprise to avail ProCoins. If already registered please buy ProCoins package from your Enterprise login"';?>>
                                <a href="javascript:void(0);" rel="tooltip" onclick="giftCoinsToAll();" class="btn primary_btn space_btm <?php if($schoolData['sc_coins'] == 0) echo 'disabled';?>">Gift ProCoins To All</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
                <div class="table_title">
                <div class="row">
                  <div class="dashboard_page_title">
                    <h1><span class="title_border">{{Auth::guard('school')->user()->sc_name}}-Students</span></h1>
                    @if(!empty($teenDetailSchoolWise))<div style="padding-top: 20px;text-align:center;">(Click checkbox to send verification code)</div>@endif
                  </div>
                  <div class="dashboard_page_title clearfix">
                        <div class="search_container desktop_search gift_coin_search pull-right">
                            <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search here..." onkeyup="userSearch(this.value, {{Auth::guard('school')->user()->id}},1)">
                            <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                  </div>
                </div>
                <form method="get" action="/school/sendemail" id="mail_submit_form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="page" value="{{$currentPage}}" id="page" />
                <input type="hidden" name="totalPage" value="{{$totalPage}}" id="totalPage" />
                <div class="my_teens_inner gift_teen_inner">
                    <div id="user_search" style="display: none;" class="loading-screen-data loading-wrapper-sub bg-offwhite">
                        <div class="loading-text">
                            <img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img" />
                        </div>
                        <div class="loading-content"></div>
                    </div>
                <div>
                    
                </div>
                    
                <div class="table_container show_data">
                    <table class="sponsor_table table_ckbx nobopd" id="table1">
                        <tr class="cst_status">
                            <th>{{trans('labels.stuname')}}</th>
                            <th>{{trans('labels.formlblnickname')}}</th>
                            <th>Roll No</th>
                            <th>{{trans('labels.class')}}</th>
                            <th>{{trans('labels.division')}}</th>
                            <th>Email</th>
                            <th class="school_dashboard_column" title="Select all students on this page">Initiate Verification
                                <span class="user_select_mail cst_user_select_mail">
                                    <input type="checkbox" id="checkall" name="checkall" class="checkbox checkall custom_checkbox">
                                    <label for="checkall"><em></em><span></span></label>                                  
                                </span>
                            </th>
                            <th class="school_dashboard_column">Verified Status</th>
                            <th>Active Status</th>
                            <th>Gift</th>
                        </tr>
                        <?php $checkValue = 0;?>
                        @forelse($teenDetailSchoolWise as $teenDetail)                        
                        <tr>
                            <td title="{{$teenDetail->t_name}}">{{$teenDetail->t_name}}</td>
                            <td>{{$teenDetail->t_nickname}}</td>
                            <td>
                                <span title="click to edit roll number" data-toggle="modal" data-target="#myModal_{{$teenDetail->id}}" id="rollno_{{$teenDetail->id}}" style="cursor:pointer;">{{$teenDetail->t_rollnum}}</span>
                                <div class="modal fade default_popup" id="myModal_{{$teenDetail->id}}">
                                  <div class="modal-dialog">
                                      <div class="modal-content">
                                          <button type="button" class="close close_next" data-dismiss="modal">Close</button>
                                          <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
                              			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Edit Roll No</h2></div>
                                          <div id="userData">
                                                <div class="request_parent gift_coin">
                                                     <div class="row">
                                                        <div class="col-md-3 col-sm-3 col-xs-3 label_user">Name</div>
                                                        <div class="col-md-9 col-sm-9 col-xs-9 detail_user">{{$teenDetail->t_name}}</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-3 col-xs-3 label_user">Email Id</div>
                                                        <div class="col-md-9 col-sm-9 col-xs-9 detail_user">{{$teenDetail->t_email}}</div>
                                                    </div>
                                                    <div class="clearfix">
                                                        <div class="input_icon">
                                                            <input type="text" name="t_rollnum" id="rollnum_{{$teenDetail->id}}" class="cst_input_primary numeric" placeholder="Enter Roll No" value="{{$teenDetail->t_rollnum}}">
                                                        </div>
                                                    </div>
                                                    <div class="button_container gift_modal_page">
                                                        <div class="submit_register">
                                                            <input type="button" class="btn primary_btn" id="updateTeenData" value="Update" onClick="updateTeenagerData({{$teenDetail->id}})">
                                                        </div>
                                                        <div class="submit_register">
                                                            <a type="button" href="javascript:void(0)" class="btn primary_btn" data-dismiss="modal" id="cancel" value="Cancel">Cancel</a>
                                                        </div>
                                                    </div>
                                                </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </td>
                            <td>{{$teenDetail->t_class}}</td>
                            <td>{{$teenDetail->t_division}}</td>
                            <td>{{$teenDetail->t_email}}</td>
                            <td>
                                <?php                               
                                if ($teenDetail->email_sent == "no") {
                                    $checkValue++;
                                    ?>
                                <input type="hidden" id="isDataAvailable" value="<?php echo $checkValue?>" />
                                    <span class="user_select_mail cst_user_select_mail">
                                        <input type="checkbox" name="email[]" value="{{$teenDetail->t_email}}" id="mail_{{$teenDetail->id}}" class="indi_checkboc custom_checkbox">
                                        <label for="mail_{{$teenDetail->id}}"><em></em><span></span></label>
                                    </span>
                                    <?php
                                } else {
                                    echo "<i class='fa fa-check rightCheckColor' aria-hidden='true'></i>";
                                }
                                ?>
                            </td>
                            <td><?php echo ($teenDetail->t_isverified == 1)?"<span class='yes0'>Yes</span>":"<span class='no0'>No</span>"; ?></td>
                            <?php $active = $teenDetail->t_school_status; ?>
                            <td>
                                <a class="btn primary_btn mid_btn cst_sponsor_dash" href="<?php if ($active == 0) { ?> {{url('/school/inactive')}}/{{$teenDetail->id}}/1 <?php } else { ?> {{url('/school/inactive')}}/{{$teenDetail->id}}/0 <?php } ?> " title="<?php if ($active == 0) {echo "Click to make Active"; }else{ echo "Click to make Inactive";}?> " class="btn primary_btn">
                                    <?php if ($active == 0) { ?> No <?php } else { ?> Yes <?php } ?></a>
                            </td>
                            <td>
                                <div class="coupon_control">
                                    <span class="tool-tip" <?php if($schoolData['sc_coins'] == 0) echo 'data-toggle="tooltip" data-placement="bottom" title="Register as Enterprise to avail ProCoins. If already registered please buy ProCoins package from your Enterprise login"';?>>
                                        <a href="javascript:void(0);" class="gift no_ani <?php if($schoolData['sc_coins'] == 0){ echo 'disabled';}?>" onclick="giftCoins({{$teenDetail->id}});" <?php if($schoolData['sc_coins'] == 0) { echo 'disabled="disabled"';}?>>
                                        Gift</a>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
                        <tr>
                            <td colspan="10" class="sub-button">
                                @if($checkValue > 0) <input type="submit" id="mail_submit" name="submit" class="btn primary_btn mid_btn cst_sponsor_dash" value="Send Mail"> @endif
                                @if (isset($teenDetailSchoolWise) && !empty($teenDetailSchoolWise))
                                <div class="pull-right">
                                    <?php echo $teenDetailSchoolWise->render(); ?>
                                </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                </div>
                <div class="mySearch_area">
                    
                </div></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade default_popup" id="gift">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close close_next" data-dismiss="modal">Close</button> -->
            <div class="close close_next">
                <i class="icon-close"></i>
            </div>
            <div class="default_logo"><img src="{{Storage::url('frontend/images/proteen_logo.png')}}" alt=""></div>
			<div class="sticky_pop_head basket_iframe_video_h2"><h2 class="title" id="basketName" style="padding-top:10px;">Gift Procoins</h2></div>
            <div id="userDataGiftCoin">

            </div>
        </div>
    </div>
</div>


<!--</div>-->
@stop
@section('script')

<script type="text/javascript">
    jQuery(document).ready(function() {

        $("#checkall").on("change", function () {
            $(".custom_checkbox").prop("checked", this.checked);
        });
        
        $(".indi_checkboc").on("change", function () {
            $("#checkall").prop("checked", false);
        });

        $(".table_container").mCustomScrollbar({axis:"x"});
        $('input.indi_checkboc').on('change', function(evt) {
            if ($('[name="email[]"]:checked').length > 15)
            {
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">At a time you can send email to maximum 15 users.</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 5000);
                this.checked = false;
            }
        });
        $('.indi_checkboc').click(function() {
            $('#mail_submit').removeAttr('disabled');
            $("#errorMsg").text('');
        });
        $('body').on('click','.sub-button #mail_submit',function() {
            
            var the_list_array = $(".user_select_mail .indi_checkboc:checked");
            var checkVal = $('#isDataAvailable').val();
            if (the_list_array.length < 1 && checkVal > 0) {
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please select atleast one checkbox</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 3000);
                return false;
                $('#mail_submit_form').submit(function() {
                    return false;
                });
            } else if (checkVal > 0) {
                
                $('#mail_submit_form').submit(function() {
                    return true;
                });
                //$(".ajax-loader").show();
                $("#errorGoneMsg").html('');                
            }
            else
            {
                return false;
            }
        });

    });

    function giftCoins(id)
    {
        var coin = <?php echo $schoolData['sc_coins'];?>;
        if (coin == 0) {
          return false;
        }
        //$('.ajax-loader').show();
        $.ajax({
            url: "{{ url('school/gift-coins') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "teen_id": id
            },
            success: function(response) {
                //$('.ajax-loader').hide();
                $('#userDataGiftCoin').html(response);
                $('#gift').modal('show');
            }
        });
    }

    function giftCoinsToAll()
    {
        var coin = <?php echo $schoolData['sc_coins'];?>;
        if (coin == 0) {
          return false;
        }
        //$('.ajax-loader').show();
        $.ajax({
            url: "{{ url('school/gift-coins-to-all-teen') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
               //$('.ajax-loader').hide();
               $('#userDataGiftCoin').html(response);
               $('#gift').modal('show');
            }
        });
    }
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.pagination a', function (e) {
        var search = $("#searchForUser").val();
        var schoolid = <?php echo Auth::guard('school')->user()->id; ?>;
        var page = $(this).attr('href').split('page=')[1];
        userSearch(search,schoolid,page);
        e.preventDefault();
    });

    function userSearch(search_keyword, schoolId,page) {
        $('#user_search').parent().addClass('loading-screen-parent');
        $('#user_search').show();
        search_keyword = (search_keyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'search_keyword=' + search_keyword + '&schoolId=' +schoolId;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/school/user-search-for-school-data?page=') }}"+page,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.mySearch_area').html(data);
                $('.show_data').hide();
                $('#user_search').hide();
                $('#user_search').parent().removeClass('loading-screen-parent');
            }
        });
    }

    function updateTeenagerData(id) {
         //$('.ajax-loader').show();
         var rollnum = $('#rollnum_'+id).val();
         $.ajax({
            url: "{{ url('/school/edit-teen-roll-num') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "teenId": id,
                "rollnum" : rollnum
            },
            success: function(response) {
                //$('.ajax-loader').hide();
                $('#rollno_'+id).text(rollnum);


                $('#myModal_'+id).modal('hide');
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-success success"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Student roll no updated successfully</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 3000);
            }
        });
    }
    $(document).on('click','.icon-close', function(){
        $('#gift').modal('hide');
    });
</script>
@stop



