@extends('layouts.developer-master')

@section('content')
<!-- content push wrapper -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level4promiseplus')}}
        <?php if(isset($promiseplusDetail) && empty($promiseplusDetail)){ ?>
            <a href="{{ url('developer/addLevel4PromisePlus') }}" class="btn btn-block btn-primary add-btn-primary pull-right">Add</a>
        <?php } else {?>
            <a href="{{ url('/developer/editLevel4PromisePlus') }}" class="btn btn-block btn-primary add-btn-primary pull-right"> Edit </a>
        <?php } ?>
    </h1>
</section>

<section class="content">
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header"></div>
                <div class="box-body">
                    <table id="listLevel4PromisePlus" class="table table-striped display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{trans('labels.formlbltext')}} </th>
                                <th>{{trans('labels.formlbldescription')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promiseplusDetail as $plus)
                            <tr>
                                <td>
                                    {{$plus['ps_text']}}
                                </td>
                                
                                <td>
                                    {{$plus['ps_description']}}
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
    $(document).ready(function() {
        $('#listLevel4PromisePlus').DataTable();
    });
</script>
@stop