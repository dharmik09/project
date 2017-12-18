@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Team</title>
@endpush

@section('content')
    @if(!$data)
        {!! $data->cms_body !!}
    @else
        <div class="bg-offwhite">
            <div class="team-heading">
                <div class="container">
                    <h1>Team Headline</h1>
                    <h2>Lorem ipsum dolor sit amet</h2>
                    <p>ProTeen is the brainchild of Sunil K Dalal and Sunil K Tatkar who through their interactions with scores of young people saw a real need to create a tool to help navigate the real world and improve their chances of success. ProTeen has born from their multi-pronged global research on the challenges faced by teenagers, current school practices, available solutions, market directions and employer needs.</p>
                </div>
            </div>
            @if($data->count() > 0)
            <div class="team-management">
                <div class="container">
                    <h2>Management</h2>
                    <ul class="nav nav-tabs clearfix owl-carousel" id="team-slider">
                        @forelse($data as $user)
                            <li class="custom-tab col-xs-6">
                                <a data-toggle="tab" href="#menu{{ $user->id }}">
                                    <span class="name">{{ $user->t_name }}</span>
                                    <span class="job-title">{{ $user->t_title }}</span>
                                </a>
                            </li>
                            <li class="custom-tab col-xs-6">
                                <a data-toggle="tab" href="#menu{{ $user->id }}">
                                    <span class="name">{{ $user->t_name }}</span>
                                    <span class="job-title">{{ $user->t_title }}</span>
                                </a>
                            </li>
                        @empty

                        @endforelse
                    </ul>
                </div>
                <div class="tab-content">
                    @forelse($data as $user)
                        <div id="menu{{$user->id}}" class="tab-pane fade in active">
                            <div class="container">
                                {!! $user->t_description !!}
                            </div>
                        </div>
                        <div id="menu{{$user->id}}" class="tab-pane fade in active">
                            <div class="container">
                                {!! $user->t_description !!}
                            </div>
                        </div>
                    @empty

                    @endforelse
                </div>
            </div>
            @endif
            <div class="advisory-board">
                <div class="container">
                    <h2>Advisory Board</h2>
                    <div class="board-list">
                        <div class="row">
                            <div class="col-xs-6 text-center">
                                <h3>John Doe</h3>
                                <h4>Job Title</h4>
                            </div>
                            <div class="col-xs-6 text-center">
                                <h3>John Doe</h3>
                                <h4>Job Title</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@push('script-footer')
<script type="text/javascript">
    $('#team-slider').owlCarousel({
        loop: false,
        margin: 20,
        items: 2,
        autoplay: false,
        autoplayTimeout: 3000,
        smartSpeed: 1000,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
        }
    });
</script>
@endpush