@extends('layouts.common-master')

@push('script-header')
    <title>{{ trans('labels.appname') }} : Forgot Password</title>
@endpush

@section('content')

<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form class="registration_form" method="POST" id="forgot-password-set-new" action="{{url('school/save-forgot-password')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="userid" value="{{$response['data']['userid']}}" />
                    <h1><span class="title_border">New Password</span></h1>
                    <p class="header_text">Please enter a new password</p>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon password">
                                <input type="password" name="newPassword" class="cst_input_primary" placeholder="Enter new password" id="password">
                                 <em style="color:red" id="pass_validation">  </em>
                            </div>
                        </div>
                        <div class="button_container">
                            <div class="submit_register"><input type="submit" class="btn primary_btn" value="Submit"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')

<script type="text/javascript">
    jQuery(document).ready(function() {
        var setNewPassword = {
            newPassword: {
                required: function(el) {
                    checkPassword()
                },
            }
        };
        $("#forgot-password-set-new").validate({
            rules: setNewPassword,
            messages: {
                newPassword: {required: '<?php echo trans('Please Enter New Password') ?>'
                }
            }
        });
    });
    function checkPassword(){
      var password = $('#password').val();
          if (password == '') {
              $("#pass_validation").text('');
              return true;
          } else if (password.length < 6) {
              $("#pass_validation").text('Password is too short.Please enter altest 6 characters');
              return true;
          } else if (password.length > 20) {
              $("#pass_validation").text('Password maximum range is 20');
              return true;
          } else if (password.search(/\d/) == -1) {
              $("#pass_validation").text('Atleast one number required in password');
              return true;
          } else if (password.search(/[a-zA-Z]/) == -1) {
              $("#pass_validation").text('Atleast one character required in password');
              return true;
          } else if (password.search(/[!\@\#\$\%\^\&\*\(\)\_\+]/) == -1) {
              $("#pass_validation").text('Atleast one special character required in password');
              return true;
          } else {
              $("#pass_validation").text('');
              return false;
          }
      }
</script>

@stop
