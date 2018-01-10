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
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet mattisac sit amet turpismolestie lacus non, elementum velit.</p>
            </div>
        </div>
        <!-- accordian section-->
        <div class="sec-accordian learning-guidance-page">
            <div class="container">
                <ul class="match-list">
                    <li><span class="number match-strong"><!-- --></span>Easy</li>
                    <li><span class="number match-potential"><!-- --></span>Medium</li>
                    <li><span class="number match-unlikely"><!-- --></span>Challenging</li>
                </ul>
                @if (isset($learningGuidance) && !empty($learningGuidance))
                <div class="learning-guidance">
                    <div class="panel-group" id="accordion">
                        @forelse ($learningGuidance['panelData'] as $learningGuidanceData) 
                            <?php 
                            switch($learningGuidanceData['slug']) {
                                case Config::get('constant.FACTUAL_SLUG'):
                                    $panelClass = 'factual';
                                    $tagClass = 'factual-cl';
                                    break;

                                case Config::get('constant.CONSEPTUAL_SLUG'):
                                    $panelClass = 'conceptual-cl';
                                    $tagClass = 'conceptual';
                                    break;

                                case Config::get('constant.PROCEDURAL_SLUG'):
                                    $panelClass = 'procedural-cl';
                                    $tagClass = 'procedural';
                                    break;

                                case Config::get('constant.META_SLUG'):
                                    $panelClass = 'meta-cl';
                                    $tagClass = 'meta';
                                    break;

                                default: 
                                    $panelClass = '';
                                    break;
                            }; ?>
                            <div class="panel panel-default {{$panelClass}}">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-parent="#accordion" data-toggle="collapse" href="#accordion{{$learningGuidanceData['id']}}" class="{{$tagClass}}"><span class="icon"><i class="icon-brain"><!-- --></i></span>{{$learningGuidanceData['name']}}</a></h4>
                                </div>
                                <?php $panelStyle = (isset($learningGuidanceData['id']) && $learningGuidanceData['id'] == 1) ? "in" : ""; ?>
                                <div class="panel-collapse collapse {{$panelStyle}}" id="accordion{{$learningGuidanceData['id']}}">
                                    <div class="panel-body">
                                        @if (isset($learningGuidanceData['subPanelData']) && !empty($learningGuidanceData['subPanelData']))
                                        <ul class="factual-list">
                                            @forelse ($learningGuidanceData['subPanelData'] as $subPanelData)
                                            <?php 
                                                switch($subPanelData['titleType']) {
                                                    case Config::get('constant.EASY_FLAG'):
                                                        $subPanelClass = "understanding";
                                                        break;

                                                    case Config::get('constant.MEDIUM_FLAG'):
                                                        $subPanelClass = "remember";
                                                        break;

                                                    case Config::get('constant.CHALLENGING_FLAG'):
                                                        $subPanelClass = "analyzing";
                                                        break;

                                                    default:
                                                        $subPanelClass = '';
                                                        break;
                                                }; ?>
                                            <li class="{{ $subPanelClass }}">
                                                <h5>{{ $subPanelData['title'] }}</h5>
                                                <p>{!! $subPanelData['description'] !!}</p>
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