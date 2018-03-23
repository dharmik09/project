@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
                @include('teenager/teenagerLevelPointBox')
                <a class="back_me" href="{{url('parent/my-challengers-accept')}}/{{$response['profession_id']}}/{{$response['teen_id']}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Back</span></a>
                <span class="level_icon">
                    <h2><a href="">{{ucfirst($response['profession_name'])}}</a></h2>
                </span>
                @if(isset($response['questionTemplate']) && !empty($response['questionTemplate']))
                <div class="accordion_content">
                    <div class="accordion_inner">
                        <div class="panel-group textCenter" id="accordion">
                            @foreach($response['questionTemplate'] as $key => $value)
                            @if($value->gt_template_title != '')
                            <div class="cards_wrap">
                              <div class="loader card_loader init_loader" style="display: none;">
                                  <div class="cont_loader">
                                      <div class="img1"></div>
                                      <div class="img2"></div>
                                  </div>
                              </div>
                                <span class="outer_wrap">
                                    <span class="inner_wrap">
                                        <span class="img">
                                            <img src="{{ Storage::url($value->gt_template_image) }}" alt="">
                                        </span>
                                        <span class="title_card" title="{{ucfirst($value->gt_template_title)}}">{{ucfirst(str_limit($value->gt_template_title, $limit = '25', $end = '...' ))}}</span>
                                        <span class="base_detail">
                                            <?php
                                                if (strlen($value->gt_template_descritpion) >= 80) {
                                                    echo trim(strip_tags(substr($value->gt_template_descritpion, 0, 80)));
                                                    ?>
                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#collapseOne{{$key}}" class="more collapseOne{{$key}}">..more</a>
                                            <?php
                                            } else {
                                                echo $value->gt_template_descritpion;
                                            }
                                            ?>
                                        </span>
                                        @if ($value->remaningDays > 0)
                                              <div class="promisebtn timer_btn l4_play_more l4_promise_btn">
                                                  <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}},{{$response['teen_id']}},'{{$value->attempted}}');" >
                                                    <span class="promiseplus">Play now!</span>
                                                      <span class="coinouter">
                                                          <span class="coinsnum">@if($value->gt_coins > 0) {{$value->remaningDays}} Days Left @else this is free enjoy @endif</span>
                                                      </span>

                                                  </a>
                                              </div>
                                          @elseif($value->gt_coins == 0)
                                            <div class="promisebtn l4_play_more">
                                                <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}},{{$response['teen_id']}},'{{$value->attempted}}');" >
                                                    <span class="coinouter" style="width:100%;padding: 5px;">
                                                        <span class="coinsnum">Play now!</span>
                                                    </span>
                                                </a>
                                            </div>
                                          @else
                                            @if($value->attempted == 'yes')
                                                <div class="promisebtn l4_play_more">
                                                    <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}},{{$response['teen_id']}},'{{$value->attempted}}');" >
                                                        <span class="coinouter" style="width:100%;padding: 5px;">
                                                            <span class="coinsnum">Played!</span>
                                                        </span>
                                                    </a>
                                                </div>
                                            @else
                                              <div class="promisebtn l4_play_more">
                                                    <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}}, {{$response['teen_id']}},'{{$value->attempted}}');" >
                                                      <span class="promiseplus">Unlock me</span>
                                                        <span class="coinouter">
                                                            <span class="coinsnum">{{$value->gt_coins}}</span>
                                                            <span class="coinsimg"><img src="{{ Storage::url('frontend/images/coin-stack.png') }}">
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                              @endif
                                          @endif
                                    </span>
                                </span>
                            </div>
                            <div id="collapseOne{{$key}}" class="modal fade privacy" role="dialog">
                                <div class="loader card_loader init_loader" style="display: none;">
                                    <div class="cont_loader">
                                        <div class="img1"></div>
                                        <div class="img2"></div>
                                    </div>
                                </div>
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                                            <h4 class="modal-title">{{ucfirst($value->gt_template_title)}}</h4>
                                        </div>
                                        <div class="modal-body">
                                            @if(isset($value->gt_template_descritpion_popup_imge) && $value->gt_template_descritpion_popup_imge != '')
                                            <div class="pre_material_img" style="text-align: center;">
                                                <img style="max-width:300px;" src="{{$value->gt_template_descritpion_popup_imge}}" alt="">
                                            </div>
                                            @endif
                                            {!! $value->gt_template_descritpion or 'Coming soon. Play Advanced' !!}
                                            @if ($value->remaningDays > 0)
                                        <div style="text-align:center;">
                                            <div class="promisebtn timer_btn l4_play_more l4_promise_btn">
                                                <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}}, {{$response['teen_id']}},'{{$value->attempted}}');" >
                                                  <span class="promiseplus">Play now!</span>
                                                    <span class="coinouter">
                                                        <span class="coinsnum">@if($value->gt_coins > 0) {{$value->remaningDays}} Days Left @else this is free enjoy @endif</span>
                                                    </span>

                                                </a>
                                            </div>
                                        </div>
                                        @elseif($value->gt_coins == 0)
                                        <div style="text-align:center;">
                                          <div class="promisebtn l4_play_more">
                                                <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}}, {{$response['teen_id']}},'{{$value->attempted}}');" >
                                                    <span class="coinouter" style="width:100%; padding: 5px;">
                                                        <span class="coinsnum">Play now!</span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        @else
                                            @if($value->attempted == 'yes')
                                                <div style="text-align:center;">
                                                  <div class="promisebtn l4_play_more">
                                                        <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}}, {{$response['teen_id']}},'{{$value->attempted}}');" >
                                                            <span class="coinouter" style="width:100%; padding: 5px;">
                                                                <span class="coinsnum">Played!</span>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                              <div style="text-align:center;">
                                                <div class="promisebtn l4_play_more">
                                                      <a href="javascript:void(0);" class="redbox promise <?php if ($value->gt_coins >0) {echo 'btn_golden_border';}?>" title="" onclick="getConceptData({{$value->l4ia_profession_id}}, {{$value->gt_template_id}}, {{$value->remaningDays}}, {{$key}}, {{$response['teen_id']}},'{{$value->attempted}}');" >
                                                        <span class="promiseplus">Unlock me</span>
                                                          <span class="coinouter">
                                                              <span class="coinsnum">{{$value->gt_coins}}</span>
                                                              <span class="coinsimg"><img src="{{ Storage::url('frontend/images/coin-stack.png')}} ">
                                                              </span>
                                                          </span>
                                                      </a>
                                                  </div>
                                              </div>
                                            @endif
                                        @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div><!-- accordion_inner End -->
                    </div><!-- accordion_content End -->
                </div>
                @else
                <div class="no_data_page">
                    <span class="nodata_outer">
                        <span class="nodata_middle">
                            Coming soon.
                            <span class="play_now"><a href="{{url('parent/my-challengers-accept')}}/{{$response['profession_id']}}/{{$response['teen_id']}}" class="button3d social_button play_difi_advanced" style="min-width: 0px !important; padding: 0 10px !important;"><span>Next</span></a></span>
                        </span>
                    </span>
                </div>
                @endif
                <div class="width_container"></div>
            </div>
        </div>
    </div>
</div>
<div id="confirm" title="Congratulations!" style="display:none;">
   <div class="confirm_coins"></div><br/>
  <div class="confirm_detail"></div>
</div>
<div class="loader" id="page_loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>
@stop
@section('script')
@if(isset($response['questionTemplate']) && !empty($response['questionTemplate']))
<script>
    $(".table_container_outer").mCustomScrollbar({
        axis: "yx"
    });
    function getConceptData(professionId, template_id, days, key, teenId,attempted)
    {
        if (attempted == 'yes') {
            getTemplateData(professionId, template_id,teenId,attempted);
        } else {
            $.ajax({
                url: "{{ url('/parent/get-available-coins-for-template') }}",
                type: 'POST',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "professionId": professionId,
                    "template_id": template_id
                },
                success: function(response) {
                    coins = response;
                    if (days == 0) {
                        if (coins == 0) {
                            getTemplateData(professionId, template_id,teenId,attempted);
                        } else {
                            $.ajax({
                                url: "{{ url('/parent/get-coins-for-template') }}",
                                type: 'POST',
                                data: {
                                    "_token": '{{ csrf_token() }}',
                                    "professionId": professionId,
                                    "template_id": template_id
                                },
                                success: function(response) {
                                    if (response == 1) {
                                        $(".confirm_coins").text('<?php echo 'You have '. number_format($response['available_coins']) .' ProCoins available.'; ?>');
                                        $(".confirm_detail").text('<?php echo 'Click OK to consume your ';?>' + format(coins) + '<?php echo ' ProCoins and play on'; ?>');
                                        $('#collapseOne'+key).modal('hide');
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
                                                        getTemplateData(professionId, template_id,teenId,attempted);
                                                        $( this ).dialog( "close" );
                                                    }
                                                },
                                                {
                                                    text: "Cancel",
                                                    class : 'btn primary_btn',
                                                    click: function() {
                                                      $( this ).dialog( "close" );
                                                      $(".confirm_coins").text(' ');
                                                      $('.loader').hide();
                                                    }
                                                }
                                              ]
                                        });
                                    } else {
                                        $("#confirm").attr('title', 'Notification!');
                                        $(".confirm_coins").text("You don't have enough ProCoins. Please Buy more.");
                                        $.ui.dialog.prototype._focusTabbable = function(){};
                                        $( "#confirm" ).dialog({

                                        resizable: false,
                                        height: "auto",
                                        width: 400,
                                        draggable: false,
                                        modal: true,
                                        close: function( event, ui ) {
                                           $('.cards_wrap .loader').hide();
                                           $('.modal .loader').hide();
                                        },
                                        buttons: [
                                                {
                                                    text: "Buy",
                                                    class : 'btn primary_btn',
                                                    click: function() {
                                                        var path = '<?php echo url('/parent/my-coins/'); ?>';
                                                        location.href = path;
                                                        $( this ).dialog( "close" );
                                                    }
                                                },
                                                {
                                                    text: "Cancel",
                                                    class : 'btn primary_btn',
                                                    click: function() {
                                                      $( this ).dialog( "close" );
                                                      $(".confirm_coins").text(' ');
                                                      $('.loader').hide();
                                                    }
                                                }
                                              ]
                                        });
                                    }
                                }
                            });
                        }
                    } else {
                        getTemplateData(professionId, template_id,teenId,attempted);
                    }

                }
            });
        }
    }
    function getTemplateData(professionId, template_id,teenId,attempted) {
        $.ajax({
            url: "{{ url('/parent/get-concept-data') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId": professionId,
                "template_id": template_id,
                "attempted":attempted
            },
            success: function(response) {
                    var url = '<?php echo url('/parent/level4-intermediate-activity/'.$value->l4ia_profession_id); ?>';
                    var path = url+'/'+template_id+'/'+teenId;
                    location.href = path;
            }
        });
    }
    function format(x) {
        return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $(document).ready(function(){
      $('.redbox').click(function(){
          $('.'+$(this).closest('.modal').attr('id')).parents('.cards_wrap').find('.loader').show();
      });
      $('.promise').click(function(){
          $(this).parents('.cards_wrap').find('.loader').show();
      });
        $('.modal .promise').click(function(){
          $(this).parents('.modal').find('.loader').show();
      });
      $('.modal-body').mCustomScrollbar();
      $('body').on("click",'.ui-dialog-titlebar-close',function(){
            $('.cards_wrap .loader').hide();
            $('.modal .loader').hide();
      });
    });
</script>
@endif
@stop
