@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<section class="content-header">
    <h1>
        <div class="col-md-9">
        {{trans('labels.notification')}}
        </div>
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box-header pull-right ">
                <i class="s_active fa fa-square"></i> {{trans('labels.activelbl')}} <i class="s_inactive fa fa-square"></i>{{trans('labels.inactivelbl')}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <div class="box-body">
                    <form onsubmit="return fetch_checkbox(this);" id="Notification" class="form-horizontal" name="Notification" method="post" action="{{ url('/admin/send-notification') }}" enctype="multipart/form-data">
                        <table class="table table-striped display" id="example" width="100%">
                            <thead>
                                <tr class="filters">
                                    <th><input type="checkbox" name="checkall" id="checkall"></th>
                                    <th>{{trans('labels.serialnumber')}}</th>
                                    <th>{{trans('labels.teentblheadname')}}</th>
                                    <th>{{trans('labels.teentblheademail')}}</th>
                                    <th>{{trans('labels.teentblheadgender')}}</th>
                                    <th>{{trans('labels.teentblheadsponsorchoice')}}</th>
                                    <th>{{trans('labels.teentblheadcountry')}}</th>
                                    <th>{{trans('labels.teentblheadregistrationtype')}}</th>
                                    <th>{{trans('labels.teenblheadstatus')}}</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{trans('labels.teentblheadname')}}</th>
                                    <th>{{trans('labels.teentblheademail')}}</th>
                                    <th>{{trans('labels.teentblheadgender')}}</th>
                                    <th>{{trans('labels.teentblheadsponsorchoice')}}</th>
                                    <th>{{trans('labels.teentblheadcountry')}}</th>
                                    <th>{{trans('labels.teentblheadregistrationtype')}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($teenagers as $teenager)
                            <?php $serialno++; ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{$teenager->id}}">
                                    </td>
                                    <td>
                                        {{$serialno}}
                                    </td>
                                    <td>
                                        {{$teenager->t_name}}
                                    </td>
                                    <td>
                                        {{$teenager->t_email}}
                                    </td>
                                    <td>
                                        {{ ($teenager->t_gender == 1)? 'Male' : 'Female' }}
                                    </td>
                                    <td>
                                        @if ($teenager->t_sponsor_choice == 1)
                                            {{trans('labels.formblself')}}
                                        @elseif ($teenager->t_sponsor_choice == 2)
                                            {{trans('labels.formblsponsor')}}
                                        @else
                                            {{trans('labels.formblnone')}}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $teenager->c_name }}
                                    </td>
                                    <td>
                                        {{ $teenager->t_social_provider }}
                                    </td>
                                    <td>
                                    @if ($teenager->deleted == 1)
                                        <i class="s_active fa fa-square"></i>
                                    @else
                                        <i class="s_inactive fa fa-square"></i>
                                    @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="box-footer">
                            <div class="form-group">
                                <label for="notification_message" class="col-sm-2 control-label">{{trans('labels.teensendnotificationmessage')}}</label>
                                <div class="col-sm-6 err">
                                    <textarea name='notification_message' id='notification_message' rows="3" cols="50" ></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-6">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-primary btn-flat pull-left" id="sendNotification" name="sendNotification">{{trans('labels.sendbtn')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#example tfoot th').each( function () {
            var title = $(this).text();
            if (title != ''){
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            }
        } );

        var table = $('#example').DataTable({
            lengthMenu: [[10, 25 ,50 ,100 ,150,200,250,300, -1], [10, 25 ,50 ,100 ,150,200,250,300,"All"]],
        });

        table.columns().every( function () {
            var that = this;
            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );

        $('#checkall').click(function(event) {
          if(this.checked) {
              $(':checkbox').each(function() {
                  this.checked = true;
              } );
          }
          else {
            $(':checkbox').each(function() {
                  this.checked = false;
              } );
          }
        } );

        var Rules = {
            notification_message: {
                required: true,
                maxlength: 150
            }
        };
        $("#Notification").validate({
            rules: Rules,
            messages: {
                notification_message: {required: '<?php echo trans('validation.requiredfield') ?>',
                    maxlength: 'Messange length Upto 150 characters',
                }
            }
        });
    });

    function fetch_checkbox(val) {
        var cboxes = document.getElementsByName('id[]');
        var len = cboxes.length;
        var checkedValue = false;
        for (var i=0; i<len; i++) {
          if (cboxes[i].checked) {
            checkedValue = true;
            break;
          }
        }
        if (checkedValue == true) {
            return confirm('<?php echo trans("labels.sendnotification"); ?>');
        } else {
            /*window.scrollTo(0, 0);
            if ($("#useForClass").hasClass('r_after_click')) {
                $("#errorGoneMsg").html('');
            }
            $("#errorGoneMsg").fadeIn();
            $("#errorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select at-least one User</span></div></div></div>');
            setTimeout(function(){$("#errorGoneMsg").fadeOut();},5000);
            return false;*/
            alert('<?php echo trans("labels.pleaseselectrecord"); ?>');
            return false;
        }
    }
    $("#notification_message").keyup(function() {
        el = $(this);
        if(el.val().length >= 150){
            el.val( el.val().substr(0, 150) );
            alert("Maximum characters limit reached");
        }
    } );
</script>
@stop