@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        
    </h1>
    <h1>
        <div class="col-md-9">
            Question Concepts       
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addLevel4Template') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/copyConcept') }}" class="btn btn-block btn-primary">Copy Concept</a>
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
                    <table class="table table-striped" id="g_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                           <th>{{trans('labels.serialnumber')}}</th>
                           <th>Profession</th>
                           <th>Concept Title</th>
                           <th>{{trans('labels.formlblcoins')}}</th>
                           <th>Template</th>
                           <th>Status</th>
                           <th>{{trans('labels.basketblheadaction')}}</th>
                        </tr>
                      </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="templateCoinsData" class="modal fade" role="dialog">

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
    function add_coins_details($id)
    {
       $.ajax({
         type: 'post',
         url: '{{ url("admin/addCoinsDataForTemplate") }}',
         headers: { 
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
         },
         data: {
           templateid:$id,
           searchBy: $('#searchBy').val(),
           searchText: $('#searchText').val(),
           orderBy: $('#orderBy').val(),
           sortOrder: $('#sortOrder').val(),
           page: <?php echo (isset($_GET['page']) && $_GET['page'] > 0 )? $_GET['page']: 1 ;?>
         },
         success: function (response)
         {
            $('#templateCoinsData').html(response);
         }
       });
    }

    var getGList = function(ajaxParams){
        $('#g_table').DataTable({
          "processing": true,
          "serverSide": true,
          "destroy": true,
          "ajax":{
              "url": "{{ url('admin/getGamificationTemplateList') }}",
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
              { "data": "pf_name"},
              { "data": "gt_template_title"},
              { "data": "gt_coins"},
              { "data": "tat_type"},
              { "data": "deleted"},
              { "data": "action", "orderable": false }
          ]
        });
    };

    $(document).ready(function() {
        var ajaxParams = {};
        getGList(ajaxParams);
    });
</script>

@stop