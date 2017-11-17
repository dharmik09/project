@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.teenagers')}}
        </div>
         <div class="col-md-2">
            <a href="{{ url('admin/clear-cache-teenager') }}" class="btn btn-block btn-primary">{{trans('labels.ldlcacheclear')}}</a>
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/add-teenager') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/export-teenager') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
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
                <div class="box-body">
                    <table class="table table-striped" id="teenager_table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.teentblheadname')}}</th>
                                <th>{{trans('labels.teentblheademail')}}</th>
                                <th>{{trans('labels.formlblcoins')}}</th>
                                <th>{{trans('labels.teentblheadphone')}}</th>
                                <th>{{trans('labels.teentblheadbirthdate')}}</th>
                                <th>{{trans('labels.teenblheadstatus')}}</th>
                                <th>Export L4 Data</th>
                                <th>Sign Up Date</th>
                                <th>{{trans('labels.tblheadactions')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>            
        </div>
    </div>
    <div id="userCoinsData" class="modal fade" role="dialog">

    </div>
</section>
@stop
@section('script')
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>

<script type="text/javascript">
    var getTeenagerList = function(ajaxParams){
        $('#teenager_table').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
                "url": "{{ url('admin/get-teenager') }}",
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
                { "data": "t_coins" },
                { "data": "t_phone" , "orderable": false},
                { "data": "t_birthdate" , "orderable": false},
                { "data": "deleted"},
                { "data": "importData" , "orderable": false},
                { "data": "created_at" },
                { "data": "action", "orderable": false }
            ]
        });
    };

    $(document).ready(function() {
        var ajaxParams = {};
        getTeenagerList(ajaxParams);
    });

    $("#fromText").datepicker({
        dateFormat: 'yy-mm-dd',
    })
    $("#toText").datepicker({
        dateFormat: 'yy-mm-dd',
    })
    $( "#searchBy" ).change(function() {
        var val = $(this).val();
        if (val == 'teenager.created_at') {
            $('.serach_box').hide();
            $('.cst_serach_box').show();
            $("#fromText").datepicker({
                dateFormat: 'yy-mm-dd',
            })
            $("#toText").datepicker({
                dateFormat: 'yy-mm-dd',
            })
        } else {
          $("#searchText").datepicker("destroy");
          $('.serach_box').show();
          $('.cst_serach_box').hide();
        }
    });
    
    function add_details($id)
    {
        $.ajax({
         type: 'post',
         url: '{{ url("admin/add-coins-data-for-teenager") }}',
         headers: { 
            'X-CSRF-TOKEN': "{{ csrf_token() }} "
         },
         data: {
           teenid:$id,
           searchBy: $('#searchBy').val(),
           searchText: $('#searchText').val(),
           orderBy: $('#orderBy').val(),
           sortOrder: $('#sortOrder').val(),
           page: <?php echo (isset($_GET['page']) && $_GET['page'] > 0 )? $_GET['page']: 1 ;?>
         },
         success: function (response)
         {
            $('#userCoinsData').html(response);
         }
       });
    }

    $(".numeric").on("keyup", function() {
      this.value = this.value.replace(/[^0-9]/gi, "");
    });

    var Rules = {
        t_coins: {
            required: true
        }
    };
    
    $("#addCoinsTeenager").validate({
        rules: Rules,
        messages: {
            t_coins: {
              required: "<?php echo trans('validation.requiredfield'); ?>"
            }
        }
    });
</script>

@stop