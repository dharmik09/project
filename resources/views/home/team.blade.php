@extends('layouts.home-master')

@push('script-header')
    <title>{{trans('labels.appname')}} : Team</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="team-heading">
            <div class="container">
                <h1>Team Headline</h1>
                <h2>Lorem ipsum dolor sit amet</h2>
                <p>ProTeen is the brainchild of Sunil K Dalal and Sunil K Tatkar who through their interactions with scores of young people saw a real need to create a tool to help navigate the real world and improve their chances of success. ProTeen has born from their multi-pronged global research on the challenges faced by teenagers, current school practices, available solutions, market directions and employer needs.</p>
            </div>
        </div>
        <div class="team-management">
            <div class="container">
                <h2>Management</h2>
                <ul class="nav nav-tabs clearfix">
                    <li class="active custom-tab col-xs-6"><a data-toggle="tab" href="#menu1"><span class="name">John Doe</span><span class="job-title">Job Title</span></a></li>
                    <li class="custom-tab col-xs-6"><a data-toggle="tab" href="#menu2"><span class="name">John Doe</span><span class="job-title">Job Title</span></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div id="menu1" class="tab-pane fade in active">
                    <div class="container">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                    </div>
                </div>
                <div id="menu2" class="tab-pane fade in ">
                    <div class="container">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Velit, consequatur laudantium veniam, accusantium soluta asperiores ipsam eos debitis unde obcaecati? Distinctio tempora obcaecati laudantium consectetur voluptatem fugiat enim, eligendi consequuntur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt iste laboriosam laborum excepturi maxime sed error? Qui, alias? Nostrum libero dolorum, eos quisquam quod exercitationem id pariatur qui sunt magni!</p>
                    </div>
                </div>
            </div>
        </div>
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
        <!-- accordian section end-->
        <!-- mid section end-->
    </div>
@stop