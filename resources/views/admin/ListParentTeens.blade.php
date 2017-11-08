@extends('layouts.admin-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
    <div class="col-md-10">
        <strong>{{$parentDetail->p_first_name}}&nbsp;{{$parentDetail->p_last_name}}</strong>
    </div>    
    </h1>
    <br/>
</section>

<section class="content">
    <div class="row">
         <div class="col-md-12">
            
        </div>
        <div class="col-md-12">
            @if($parentDetail->p_user_type == 1)
            <a href="{{ url('admin/parents/1') }}">Back</a>
            @else
            <a href="{{ url('admin/counselors/2') }}">Back</a>
            @endif
            <div class="box box-primary">
                
                <div class="box-header">
                </div>
                <div class="box-body">
                     <table class="table table-striped" id="teenager_list" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.serialnumber')}}</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Email</th>                            
                                <th>Phone</th>                            
                                <th>Birthdate</th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php $serialno = 0; ?>
                            @forelse($final as $teen)
                                <?php $serialno++; ?>
                                <tr>
                                    <td>
                                        <?php echo $serialno; ?>
                                    </td>
                                    <td>
                                        {{$teen->t_name}}
                                    </td>
                                    <td>
                                        <?php
                                            $t_photo = ($teen->t_photo != "" && Storage::disk('s3')->exists(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$teen->t_photo)) ? Config::get('constant.DEFAULT_AWS').Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$teen->t_photo : asset(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH') . 'proteen-logo.png');
                                        ?>
                                        <img src="{{$t_photo}}" title="Proteen-Coupon-User" width="50px" height="50px"/>
                                    </td>
                                    <td>
                                        {{$teen->t_email}}
                                    </td>
                                    <td>
                                        {{$teen->t_phone}}
                                    </td>
                                    <td>
                                        {{date('d/m/Y',strtotime($teen->t_birthdate))}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
                                </tr>
                            @endforelse
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')
<script type="text/javascript">
    $('#teenager_list').DataTable();
</script>
@stop