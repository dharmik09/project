@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        <div class="col-md-7">
            {{trans('labels.teenagers')}}
        </div>
         <div class="col-md-2">
            <a href="{{ url('admin/clearcacheteenager') }}" class="btn btn-block btn-primary">{{trans('labels.ldlcacheclear')}}</a>
        </div>
        <div class="col-md-1">
            <a href="{{ url('admin/addteenager') }}" class="btn btn-block btn-primary add-btn-primary pull-right">{{trans('labels.add')}}</a>
        </div>
        <div class="col-md-2">
            <a href="{{ url('admin/exportteenager') }}" class="btn btn-block btn-primary">{{trans('labels.exportdata')}}</a>
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
                    <form id="formSearchTeenager" class="form-horizontal" method="post" action="{{ url('/admin/teenagers') }}">
                        <div class="col-md-2">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select id="searchBy" name="searchBy" class="form-control">
                                <option value="t_name" <?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 't_name'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblname')}}</option>
                                <option value="t_nickname" <?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 't_nickname'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblnickname')}}</option>
                                <option value="t_email" <?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 't_email'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblemail')}}</option>
                                <option value="teenager.created_at" <?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'teenager.created_at'){ echo 'selected = "selected"';}?> >Sign Up Date</option>
                            </select>
                        </div>
                        <div class="col-md-3 serach_box" style="<?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] == 'teenager.created_at'){ echo 'display:none;';}?>">
                            <input type="text" id="searchText" name="searchText" placeholder="{{trans('labels.lblsearch')}}" value="<?php if(isset($searchParamArray['searchText']) && $searchParamArray['searchText'] != ''){ echo $searchParamArray['searchText'];}?>" class="form-control" />
                        </div>
                        <div class="col-md-2 cst_serach_box" style="<?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] != 'teenager.created_at' || empty($searchParamArray)){ echo 'display:none;';}?>">
                            <input type="text" id="fromText" name="fromText" placeholder="{{trans('labels.lblsearch')}}" value="<?php if(isset($searchParamArray['fromText']) && $searchParamArray['fromText'] != ''){ echo $searchParamArray['fromText'];}?>" class="form-control" />
                        </div>
                        <div class="col-md-2 cst_serach_box" style="<?php if(isset($searchParamArray['searchBy']) && $searchParamArray['searchBy'] != 'teenager.created_at' || empty($searchParamArray )){ echo 'display:none;';}?>">
                            <input type="text" id="toText" name="toText" placeholder="{{trans('labels.lblsearch')}}" value="<?php if(isset($searchParamArray['toText']) && $searchParamArray['toText'] != ''){ echo $searchParamArray['toText'];}?>" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <select id="orderBy" name="orderBy" class="form-control">
                                <option value="">{{trans('labels.formlblorderby')}}</option>
                                <option value="t_name" <?php if(isset($searchParamArray['orderBy']) && $searchParamArray['orderBy'] == 't_name'){ echo 'selected = "selected"';}?> >{{trans('labels.formlblname')}}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="sortOrder" name="sortOrder" class="form-control">
                                <option value="">{{trans('labels.lblorder')}}</option>
                                <option value="ASC" <?php if(isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'ASC'){ echo 'selected = "selected"';}?> >Ascending</option>
                                <option value="DESC" <?php if(isset($searchParamArray['sortOrder']) && $searchParamArray['sortOrder'] == 'DESC'){ echo 'selected = "selected"';}?> >Descending</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="searchTeenager" id="searchTeenager" value="{{trans('labels.lblsearch')}}"/>
                        </div>
                        <div class="col-md-1">
                            <input type="submit" class="btn btn-primary btn-flat" name="clearSearch" id="clearSearch" value="{{trans('labels.lblclear')}}"/>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
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
                        <?php $serialno = 0; ?>
                        @forelse($teenagers as $teenager)
                        <?php $serialno++; ?>
                        <tr>
                            <td>
                                <?php echo $serialno; ?>
                            </td>
                            <td>
                                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                <a target="_blank" href=" {{url('/admin/viewteenagers')}}/{{$teenager->id}}{{$page}} ">{{$teenager->t_name}}</a>
                            </td>
                            <td>
                                {{$teenager->t_email}}
                            </td>
                            <td>
                                {{$teenager->t_coins or ''}}
                            </td>                            
                            <td>
                                {{$teenager->t_phone}}
                            </td>
                            <td>
                                {{date('d/m/Y',strtotime($teenager->t_birthdate))}}
                            </td>
                            <td>
                                @if ($teenager->deleted == 1)
                                    <i class="s_active fa fa-square"></i>
                                @else
                                    <i class="s_inactive fa fa-square"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/admin/exportl4data') }}/{{$teenager->id}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                            </td>
                            <td>
                                {{date('d/m/Y',strtotime($teenager->created_at))}}
                            </td>
                            <td>
                                <?php $sid = 0; ?>
                                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                                <a href="{{ url('/admin/editteenager') }}/{{$teenager->id}}/{{$sid}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
                                <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteteenager') }}/{{$teenager->id}}"><i class="i_delete fa fa-trash"></i>&nbsp;&nbsp;</a>
                                <a href="" onClick="add_details({{$teenager->id}});" data-toggle="modal" id="#userCoinsData" data-target="#userCoinsData"><i class="fa fa-database" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                        </tr>
                        @endforelse
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
<script type="text/javascript">
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
  </script>
<script type="text/javascript">
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
         url: '{{ url("admin/addCoinsDataForTeenager") }}',
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