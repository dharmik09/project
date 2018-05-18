@extends('layouts.admin-master')
@section('content')
<section class="content-header">
	<h1>
		<div class="col-md-9">
			{{trans('labels.schools')}}
		</div>
		<div class="col-md-1">
			<a href="{{ url('admin/add-school') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
		</div>
		<div class="col-md-2">
			<a href="{{ url('admin/export-school') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>            
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
			 <table class="table table-striped" id="school_table" cellspacing="0" width="100%">
				<thead>
				  	<tr>
					  	<th>{{trans('labels.serialnumber')}}</th>
					  	<th width="30%">{{trans('labels.schoolblheadname')}}</th>
					  	<th>{{trans('labels.schoolblheademail')}}</th>
					  	<th>{{trans('labels.schoolblheadphone')}}</th>
					  	<th>{{trans('labels.formlblcoins')}}</th>
					  	<th>{{trans('labels.schoolblheadapproved')}}</th>
					  	<th>Student Count</th>
					  	<th>{{trans('labels.schoolblheadstatus')}}</th>
					  	<th>{{trans('labels.schoolblheadlogo')}}</th>
					  	<th>Sign Up Date</th>
					  	<th>{{trans('labels.schoolblheadactions')}}</th>
				  	</tr>
				</thead>
			 </table>
		</div>
	  </div>
	</div>
  </div>
  <div id="schoolCoinsData" class="modal fade" role="dialog">
  </div>
</section>
@stop
@section('script')
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
        var ajaxParams = {};
        getSchoolList(ajaxParams);
    });
    var getSchoolList = function(ajaxParams){
        $('#school_table').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "lengthMenu": [ 10, 25, 50, 100, 200, 500, 1000 ],
            "ajax":{
                "url": "{{ url('admin/get-school') }}",
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
                { "data": "id"},
                { "data": "sc_name"},
                { "data": "sc_email"},
                { "data": "sc_phone"},
                { "data": "sc_coins", "orderable": false},
                { "data": "sc_isapproved"},
                { "data": "studentcount", "orderable": false},
                { "data": "deleted", "orderable": false},
                { "data": "sc_logo", "orderable": false},
                { "data": "created_at"},
                { "data": "action", "orderable": false}
            ], 
            "initComplete": function(settings, json) {
                if(typeof(json.customMessage) != "undefined" && json.customMessage !== '') {
                    $('.customMessage').removeClass('hidden');
                    $('#customMessage').html(json.customMessage);
                }
            }
        });
    };
	function add_coins_details($id)
	{
	   	$.ajax({
		 	type: 'post',
		 	url: '{{ url("admin/add-coins-data-for-school") }}',
		 	data: {
		   		schoolid:$id
		 	},
		 	headers: { 
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
		 	success: function (response)
		 	{
				$('#schoolCoinsData').html(response);
		 	}
	   	});
	}
	$("#fromText").datepicker({
		dateFormat: 'yy-mm-dd',
	});
	$("#toText").datepicker({
		dateFormat: 'yy-mm-dd',
	});
	$( "#searchBy" ).change(function() {
		var val = $(this).val();
		if (val == 'school.created_at') {
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
</script>
@stop