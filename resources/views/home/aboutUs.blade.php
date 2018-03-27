@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : About Us</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <div class="terms-heading">
            <div class="container">
                <h1>About Us</h1>
            </div>
        </div>
        <div class="terms-content">
            <div class="container">
                <p><strong>ProTeen</strong> unlocks the true potential of young adults.  ProTeen's gamified web and mobile platform guides high school and college students through the maze of real world career options and helps them achieve their future goals by making intelligent academic choices.</p>
                <p>ProTeen supplements traditional school or counselor driven approaches currently in use globally. It encompasses all aspects of the educational ecosystem – students, parents, schools and career mentors such as teachers, counselors and other education professionals.</p>
                <p>ProTeen is a <strong><a href="https://www.unidel-group.com/" target="_blank" title="Unidel Company">UNIDEL</a></strong> Company, a builder and creator of disruptive technology ventures.</p>
            </div>
        </div>
        <div class="sec-blank-about">
        </div>
    </div>
@stop