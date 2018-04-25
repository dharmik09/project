@extends('layouts.school-master')

@section('content')
@if($message = Session::get('success'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-success alert-succ-msg alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
            {{ $message }}
        </div>
    </div>
</div>
@endif
@if($message = Session::get('error'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            {{ $message }}
        </div>
    </div>
</div>
@endif
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ask Your Students
    </h1>
</section>

<div class="centerlize">
    <div class="container">
        <div class="container_padd list-question">
            <div class="button_container coins_button_container">
                <div class="coin_summary cst_dsh clearfix">
                    <div class="dashboard_page pull-right col-md-3 col-sm-4 col-xs-12">
                        <a href="{{ url('school/add-questions') }}" class="btn primary_btn space_btm">Add Question</a>
                    </div>
                </div>
            </div>
            <div class="table_title">
                <div class="row">
                    <div class="dashboard_page_title">
                    </div>
                    <div class="dashboard_page_title clearfix">
                        <div class="search_container desktop_search gift_coin_search pull-right">
                            <input type="text" name="search_box" id="searchForUser" class="search_input" placeholder="Search here..." onkeyup="search(this.value)">
                            <button type="submit" class="search_btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table_container show_data">
            @include('school/searchQuestions')
        </div>
        <div class="mySearch_area">
            
        </div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
     function search(searchKeyword) {
        $('#search_loader').parent().addClass('loading-screen-parent');
        $('#search_loader').show();
        searchKeyword = (searchKeyword).trim();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'searchKeyword=' + searchKeyword;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/school/search-questions') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(data) {
                $('.show_data').html(data);
                $('#search_loader').hide();
                $('#search_loader').parent().removeClass('loading-screen-parent');
            }
        });
    }
</script>
@stop
