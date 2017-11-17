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
                    <table class="table table-striped display" id="listNotification" width="100%">
                            <thead>
                                <tr class="filters">
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
                                    <th>{{trans('labels.teentblheadname')}}</th>
                                    <th>{{trans('labels.teentblheademail')}}</th>
                                    <th></th>
                                    <th></th>
                                    <th>{{trans('labels.teentblheadcountry')}}</th>
                                    <th>{{trans('labels.teentblheadregistrationtype')}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <form id="Notification" class="form-horizontal" name="Notification" method="post" action="{{ url('/admin/sendNotification') }}" enctype="multipart/form-data">
                        <div class="box-footer">
                            <div class="form-group">
                                <label for="send_to_all" class="col-sm-2 control-label">Send to All</label>
                                <div class="col-sm-6">
                                    <input id="sendToAll" type="checkbox" name="sendtoall" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="teenager_name" class="col-sm-2 control-label">Teenager</label>
                                <div class="col-sm-6">
                                    <select id="teenName" name="teenName[]" data-placeholder="Choose a teenager..." multiple class="chosen-select form-control">
                                        <?php foreach ($teenagersName as $teenName) { ?>
                                            <option value="{{$teenName->id}}">{{$teenName->t_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
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
    var getNotificationList = function(ajaxParams){
        $('#listNotification').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
                "url": "{{ url('admin/getNotification') }}",
                "dataType": "json",
                "type": "POST",
                headers: { 
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                "data" : function(data) {
                    if (ajaxParams) {
                        $.each(ajaxParams, function(key, value) {
                            data[key] = value;
                        });
                        ajaxParams = {};
                    }
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "t_name" },
                { "data": "t_email" },
                { "data": "t_gender", "searchable": false },
                { "data": "t_sponsor_choice", "searchable": false },
                { "data": "c_name"},
                { "data": "t_social_provider" },
                { "data": "deleted", "orderable": false, "searchable": false },
            ]
        });
    };
    $(document).ready(function() {
        var ajaxParams = {};
        getNotificationList(ajaxParams);
    
        $('#listNotification tfoot th').each( function () {
            var title = $(this).text();
            if (title != ''){
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            }
        });

        $(".chosen-select").chosen();
        $("#sendToAll").change(function() {
            if(this.checked) {
                $("#teenName").prop("disabled", true).trigger("chosen:updated");;
            } else {
                $("#teenName").prop("disabled", false).trigger("chosen:updated");;
            }
        });

        

        // var Rules = {
        //     notification_message: {
        //         required: true,
        //         maxlength: 150
        //     }
        // };
        // $("#Notification").validate({
        //     rules: Rules,
        //     messages: {
        //         notification_message: {required: '<?php //echo trans('validation.requiredfield') ?>',
        //             maxlength: 'Messange length Upto 150 characters',
        //         }
        //     }
        // });
    });

    
    // $("#notification_message").keyup(function() {
    //     el = $(this);
    //     if(el.val().length >= 150){
    //         el.val( el.val().substr(0, 150) );
    //         alert("Maximum characters limit reached");
    //     }
    // } );


</script>
@stop