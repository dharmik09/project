@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.lblimportteencareermap')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="addCareerBulk" class="form-horizontal" method="post" action="{{ url('/admin/addImportExcel') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        
                    <div class="form-group"> 
                        <label for="teenager_bulk" class="col-sm-2 control-label">Import career mapping</label>
                        <div class="col-sm-6">
                            <input type="file" id="importfile" name="importfile" />
                        </div>
                    </div>
                        
                    </div>
                    <div class="box-footer"> 
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >submit</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/careerMapping') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop


