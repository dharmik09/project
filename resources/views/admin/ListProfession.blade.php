@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="col-md-12">
        <h1>
            <div class="col-md-3">
                {{trans('labels.professions')}}
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfession') }}" class="btn btn-block btn-primary pull-right">Add Profession</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/exportProfessoin') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionbulkupload')}}</a>
            </div>
        </h1>
    </div>
    <div class="col-md-12">
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseCertificationBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisecertificatebulkupload')}}</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseSubjectBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisesubjectbulkupload')}}</a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('admin/addProfessionWiseTagBulk') }}" class="btn btn-block btn-primary">{{trans('labels.professionwisetagbulkupload')}}</a>
            </div>
    </div>
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
                    <table id="listProfession" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>{{trans('labels.professionblheadname')}}</th>
                                <th>{{trans('labels.professionblheadbasket')}}</th>
                                <th>{{trans('labels.professionblheadlogo')}}</th>
                                <th>{{trans('labels.professionblheadstatus')}}</th>
                                <th>{{trans('labels.professionblheadaction')}}</th>
                                <th>Competitors</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="userCompetotorsData" class="modal fade" role="dialog">

    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
  </script>
<script type="text/javascript">

    function fetch_competitors_details($id)
    {
        //alert("hi");
       $.ajax({
         type: 'post',
         url: '{{ url("admin/getUserCompetitorsData") }}',
         data: {
           Professionid:$id
         },
         success: function (response)
         {
            $('#userCompetotorsData').html(response);
         }
       });
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var ajaxParams = {};
        getProfessionList(ajaxParams);
    });

    var getProfessionList = function(ajaxParams){
        $('#listProfession').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "ajax":{
                "url": "{{ url('admin/getProfessions') }}",
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
                    }
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "pf_name" },
                { "data": "b_name" },
                { "data": "pf_logo", "orderable": false},
                { "data": "deleted" , "orderable": false},
                { "data": "action", "orderable": false },
                { "data": "competitors", "orderable": false }
            ]
        });
    };

</script>
@stop
