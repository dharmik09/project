@extends('layouts.parent-master') @section('content')

<div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif @if($message = Session::get('error'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable danger">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif @if($message = Session::get('success'))
    <div class="clearfix">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-succ-msg alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button> {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="centerlize">
    <div class="container">
        <div class="container_padd detail_container">
            <form class="registration_form" id="invite_teen" role="form" enctype="multipart/form-data" method="POST" action="{{ url('/parent/save-pair') }}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                <h1><span class="title_border">Invite Teen</span></h1>
                <div style="text-align: center;margin-bottom: 10px;">(Enter unique Teen ID to send invitation)</div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="clearfix">
                    <div class="col-md-offset-3 col-sm-offset-2 col-md-6 col-sm-8 pair_with_teen input_icon">
                        <div class="mandatory">*</div>
                        <input type="text" name="p_teenager_reference_id" maxlength="100" class="cst_input_primary" placeholder="Teen Pair">
                        <!--<span class="info_popup_open" style="right: 22px;top: 10px; cursor:pointer;" title="Enter unique Teen ID. Teen will receive an email once you submit the form. Once Teen verifies your invitation, you can see their progress through the ProTeen levels. If you are not aware of unique Teen ID, please contact the Teen or you can find it in their Profile section."><i aria-hidden="true" class="fa fa-question-circle"></i></span>-->
                        <span class="sec-popup help_noti"><a href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                        <div id="pop1" class="hide popoverContent">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                        </div>
                    </div>

                    <div class="button_container col-md-12">
                        <div class="submit_register reset_password">
                            <input type="submit" value="Submit" name="save" id="submitInvitation" class="btn primary_btn">
                            <a href="{{url('/parent/home')}}" class="btn primary_btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>

@stop @section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var validationRules = {
            p_teenager_reference_id: {
                required: true
            }
        };
        $("#invite_teen").validate({
            rules: validationRules,
            messages: {
                p_teenager_reference_id: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                }
            }
        });
    });
    $("#submitInvitation").click(function() {
        var form = $("#invite_teen");
        form.validate();
        if (form.valid()) {
            form.submit();
            $('.ajax-loader').show();
            $("#submitInvitation").attr("disabled", 'disabled');
        } else {
            $('.ajax-loader').hide();
            $("#submitInvitation").removeAttr("disabled", 'disabled');
        }
    });

</script>
@stop
