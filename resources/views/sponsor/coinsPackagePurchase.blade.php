@extends('layouts.common-master')

@section('content')
<div>
    @if ($message = Session::get('error'))
      <div class="col-md-8 col-md-offset-2 invalid_pass_error">
          <div class="box-body">
              <div class="alert alert-error alert-dismissable danger">
                  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                  <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                  {{ $message }}
              </div>
          </div>
      </div>
    @endif
    @if ($message = Session::get('success'))
      <div class="col-md-8 col-md-offset-2 invalid_pass_error">
          <div class="box-body">
              <div class="alert alert-success alert-dismissable success_msg">
                  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                  <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                  {{ $message }}
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
<div class="container_padd">
    <div class="container lil_profile">
        <div class="row">
                <div class="pricing_title">
                    <h1><span class="title_border">{{trans('labels.pcoinsp')}}</span></h1>
                </div>
                <div class="sec-procoins">
                    <div class="list-procoins">
                        <div class="row flex-container">
                            @if(isset($coinsDetail) && count($coinsDetail) > 0)
                            <?php $column_count = 1; ?>
                            @foreach($coinsDetail as $key=>$val)
                            <div class="col-sm-6 flex-items">
                                <div class="block-procoins">
                                    <div class="coin-info">
                                        <div class="icon">
                                        <?php
                                            if (isset($val->id) && $val->id != '0') {
                                                $uploadCoinsThumbPath = 'uploads/coins/thumb/';
                                                if (isset($val->c_image) && $val->c_image != '' && Storage::size($uploadCoinsThumbPath . $val->c_image) > 0) {
                                                    $coinImage = Storage::url($uploadCoinsThumbPath . $val->c_image);
                                                    $altImage = $val->c_image;
                                                } else { 
                                                    $coinImage = Storage::url('frontend/images/proteen_logo.png');
                                                    $altImage = 'Default Image';
                                                }
                                            }
                                        ?>
                                        <img src="{{ $coinImage }}" alt="{{ $altImage }}">
                                        </div>
                                        <h4>{{$val->c_package_name}}</h4>
                                        <h2 class="price">
                                            @if($val->c_currency == 1)
                                            <span class="rupee-symbol">
                                                <i class="fa fa-inr"></i>
                                            </span>
                                            @else
                                            <span class="dollar-symbol">$</span>
                                            @endif
                                            <?php echo intval($val->c_price); ?>    
                                        </h2>
                                        <div class="procoins-value"><?php echo number_format($val->c_coins);?> <span>ProCoins</span>
                                        </div>
                                        <p>{{$val->c_description}}</p>
                                    </div>
                                    <?php $packageId = base64_encode($val->id);?>
                                    <a href="javascript:void(0);" title="Buy" class="btn btn-primary" onclick="purchasedCoins('{{$packageId}}', {{$val->c_valid_for}});">Buy</a>
                                </div>
                            </div>
                            <?php
                                $column_count++;
                            ?>
                            @endforeach
                            @else
                                <p>No Packages found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="confirm" title="Buy Coins" style="display:none;">
  <p><span class="confirm_coins"></span></p>
</div>
@stop
@section('script')
<script type="text/javascript">
    function purchasedCoins(package_id, days)
    {
        var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
        package_id = Base64.decode(package_id);
        if (days > 0) {
            $(".confirm_coins").text('<?php echo 'This Package valid for '; ?>' + days + '<?php echo ' Days'; ?>');
            $.ui.dialog.prototype._focusTabbable = function(){};
            $( "#confirm" ).dialog({

                resizable: false,
                height: "auto",
                width: 400,
                draggable: false,
                modal: true,
                buttons: [
                {
                    text: "BUY",
                    class : 'btn primary_btn',
                    click: function() {
                      var path = '<?php echo url('sponsor/save-coin-package-pre-purchased-data').'/'; ?>'+package_id;
                      location.href = path;
                      //$(".confirm_coins").text(' ');
                    }
                },
                {
                    text: "Cancel",
                    class : 'btn primary_btn',
                    click: function() {
                      $( this ).dialog( "close" );
                      $(".confirm_coins").text(' ');
                    }
                }],
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").replaceWith( '<i class="icon-close"></i>' );
                }
            });
        }
    }

    $(document).on('click','.icon-close', function(){
        $( "#confirm" ).dialog( "close" );
    });
</script>
@stop