@extends('layouts.teenager-master')

@push('script-header')
    <title>Learning Guidance</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="learning-heading">
            <div class="container">
                <div class="head-sec">
                    <div class="head-content">
                        <h1>learning guidance</h1>
                    </div>
                    <div class="head-icon">
                        <span><i class="icon-success"></i></span>
                    </div>
                </div>
                <p>Role play tasks are based on mental, physical and emotional skills of varied professions. Learning Guidance is a personalized insight based on your experential role plays. The more you play the better the insight.</p>
            </div>
        </div>
        <!-- accordian section-->
        <div class="sec-accordian learning-guidance-page">
            <div class="container">
                <ul class="match-list">
                    <li><span class="number match-strong"><!-- --></span>Easy</li>
                    <li><span class="number match-potential"><!-- --></span>Moderate</li>
                    <li><span class="number match-unlikely"><!-- --></span>Challenging</li>
                </ul>
                @if (isset($learningGuidance) && !empty($learningGuidance))
                <div class="learning-guidance">
                    <div class="panel-group" id="accordion">
                        @forelse ($learningGuidance as $learningGuidanceData) 
                            <?php 
                            
                            switch($learningGuidanceData['slug']) {
                                case Config::get('constant.FACTUAL_SLUG'):
                                    $panelClass = 'factual';
                                    $tagClass = 'factual-cl dataCollapse';
                                    break;

                                case Config::get('constant.CONSEPTUAL_SLUG'):
                                    $panelClass = 'conceptual-cl';
                                    $tagClass = 'conceptual dataCollapse';
                                    break;

                                case Config::get('constant.PROCEDURAL_SLUG'):
                                    $panelClass = 'procedural-cl';
                                    $tagClass = 'procedural dataCollapse';
                                    break;

                                case Config::get('constant.META_SLUG'):
                                    $panelClass = 'meta-cl';
                                    $tagClass = 'meta dataCollapse';
                                    break;

                                default: 
                                    $panelClass = '';
                                    break;
                            }; ?>
                            <div class="panel panel-default {{$panelClass}}">
                                <div class="panel-heading accordionClass{{$learningGuidanceData['id']}}">
                                    <h4 class="panel-title">
                                        <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$learningGuidanceData['id']}}" class="{{$tagClass}}" data-class-id="{{$learningGuidanceData['id']}}">
                                            <span class="icon">
                                                <img src="{{ $learningGuidanceData['image'] }}" alt="icon img">
                                            </span>
                                            {{$learningGuidanceData['name']}}
                                        </a>
                                    </h4>
                                </div>
                                <?php $panelStyle = (isset($learningGuidanceData['id']) && $learningGuidanceData['id'] == 1) ? "in" : ""; ?>
                                <div class="panel-collapse collapse {{$panelStyle}}" id="accordion{{$learningGuidanceData['id']}}">
                                    <div class="panel-body">
                                        @if (isset($learningGuidanceData['subPanelData']) && !empty($learningGuidanceData['subPanelData']))
                                        <ul class="factual-list">
                                            @forelse ($learningGuidanceData['subPanelData'] as $subPanelData)
                                            <?php 
                                                switch($subPanelData['titleType']) {
                                                    case 'High':
                                                        $subPanelClass = "understanding";
                                                        break;

                                                    case 'Medium':
                                                        $subPanelClass = "remember";
                                                        break;

                                                    case 'High':
                                                        $subPanelClass = "analyzing";
                                                        break;

                                                    default:
                                                        $subPanelClass = '';
                                                        break;
                                                }; ?>
                                            <li class="{{ $subPanelClass }}">
                                                <h5>{{ str_replace('_', ' ', $subPanelData['title']) }}</h5>
                                                <p>{!! $subPanelData['subPanelDescription'] !!}</p>
                                            </li>
                                            @empty
                                                No Records Found
                                            @endforelse
                                        </ul>
                                        @else
                                            No Records Found
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            No Records Found
                        @endforelse
                    </div>
                </div>
                @else
                    No Records Found
                @endif
            </div>
        </div>
        <!-- accordian section end-->
        <!-- mid section end-->
    </div>
@stop
