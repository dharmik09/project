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
                <div class="learning-guidance">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default factual">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="factual-cl"><span class="icon"><i class="icon-brain"><!-- --></i></span>Factual</a></h4>
                            </div>
                            <div class="panel-collapse collapse in" id="accordion1">
                                <div class="panel-body">
                                    <ul class="factual-list">
                                        <li class="remember">
                                            <h5>Remembering</h5>
                                            <p>Recalling terminology, dates, or any information previously learned relevant to a subject.</p>
                                            <p><strong>Example:</strong> To be able to list primary and secondary colors, list numbers.</p>
                                        </li>
                                        <li class="understanding">
                                            <h5>Understanding</h5>
                                            <p>Interpretting or summarizing facts into something simpler to understand. </p>
                                            <p><strong>Example:</strong> To be able to summarize features of a new product, para-phrase lines from a poem.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Applying Analyzing</h5>
                                            <p>Applying facts and terminology in any situation.</p>
                                           <p><strong>Example 1:</strong> To be able to respond to FAQ’s, classify species of birds and animals. Analyzing facts and terminology in any situation.</p>
                                            <p><strong>Example 2:</strong> To be able to select the most complete list of activities, outline admission steps.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Evaluating Creating</h5>
                                            <p>Evaluating given facts or creating new facts as relevant to the subject.<p>
                                           <p><strong>Example 1:</strong> TTo be able to check for consistency amongst sources, rank students by age. Creating new facts as relevant to a subject.</p>
                                            <p><strong>Example 2:</strong> To be able to generate a log of daily activities, categorize by age groups.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default conceptual-cl">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion2" class="conceptual collapsed"><span class="icon"><i class="icon-bulb"></i></span>Conceptual</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion2">
                                <div class="panel-body">
                                    <ul class="factual-list">
                                        <li class="remember">
                                            <h5>Remembering</h5>
                                            <p>Recalling terminology, dates, or any information previously learned relevant to a subject.</p>
                                            <p><strong>Example:</strong> To be able to list primary and secondary colors, list numbers.</p>
                                        </li>
                                        <li class="understanding">
                                            <h5>Understanding</h5>
                                            <p>Interpretting or summarizing facts into something simpler to understand. </p>
                                            <p><strong>Example:</strong> To be able to summarize features of a new product, para-phrase lines from a poem.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Applying Analyzing</h5>
                                            <p>Applying facts and terminology in any situation.</p>
                                           <p><strong>Example 1:</strong> To be able to respond to FAQ’s, classify species of birds and animals. Analyzing facts and terminology in any situation.</p>
                                            <p><strong>Example 2:</strong> To be able to select the most complete list of activities, outline admission steps.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Evaluating Creating</h5>
                                            <p>Evaluating given facts or creating new facts as relevant to the subject.p>
                                           <p><strong>Example 1:</strong> TTo be able to check for consistency amongst sources, rank students by age. Creating new facts as relevant to a subject.</p>
                                            <p><strong>Example 2:</strong> To be able to generate a log of daily activities, categorize by age groups.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default procedural-cl">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion3" class="procedural collapsed"><span class="icon"><i class="icon-puzzle-piese"></i></span>Procedural</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion3">
                                <div class="panel-body">
                                   <ul class="factual-list">
                                        <li class="remember">
                                            <h5>Remembering</h5>
                                            <p>Recalling terminology, dates, or any information previously learned relevant to a subject.</p>
                                            <p><strong>Example:</strong> To be able to list primary and secondary colors, list numbers.</p>
                                        </li>
                                        <li class="understanding">
                                            <h5>Understanding</h5>
                                            <p>Interpretting or summarizing facts into something simpler to understand. </p>
                                            <p><strong>Example:</strong> To be able to summarize features of a new product, para-phrase lines from a poem.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Applying Analyzing</h5>
                                            <p>Applying facts and terminology in any situation.</p>
                                           <p><strong>Example 1:</strong> To be able to respond to FAQ’s, classify species of birds and animals. Analyzing facts and terminology in any situation.</p>
                                            <p><strong>Example 2:</strong> To be able to select the most complete list of activities, outline admission steps.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Evaluating Creating</h5>
                                            <p>Evaluating given facts or creating new facts as relevant to the subject.<p>
                                           <p><strong>Example 1:</strong> TTo be able to check for consistency amongst sources, rank students by age. Creating new facts as relevant to a subject.</p>
                                            <p><strong>Example 2:</strong> To be able to generate a log of daily activities, categorize by age groups.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default meta-cl">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion4" class="meta collapsed"><span class="icon"><i class="icon-atom"></i></span>Meta-Cognitive</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion4">
                                <div class="panel-body">
                                   <ul class="factual-list">
                                        <li class="remember">
                                            <h5>Remembering</h5>
                                            <p>Recalling terminology, dates, or any information previously learned relevant to a subject.</p>
                                            <p><strong>Example:</strong> To be able to list primary and secondary colors, list numbers.</p>
                                        </li>
                                        <li class="understanding">
                                            <h5>Understanding</h5>
                                            <p>Interpretting or summarizing facts into something simpler to understand. </p>
                                            <p><strong>Example:</strong> To be able to summarize features of a new product, para-phrase lines from a poem.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Applying Analyzing</h5>
                                            <p>Applying facts and terminology in any situation.</p>
                                           <p><strong>Example 1:</strong> To be able to respond to FAQ’s, classify species of birds and animals. Analyzing facts and terminology in any situation.</p>
                                            <p><strong>Example 2:</strong> To be able to select the most complete list of activities, outline admission steps.</p>
                                        </li>
                                        <li class="analyzing">
                                            <h5>Evaluating Creating</h5>
                                            <p>Evaluating given facts or creating new facts as relevant to the subject.<p>
                                           <p><strong>Example 1:</strong> TTo be able to check for consistency amongst sources, rank students by age. Creating new facts as relevant to a subject.</p>
                                            <p><strong>Example 2:</strong> To be able to generate a log of daily activities, categorize by age groups.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- accordian section end-->
        <!-- mid section end-->
    </div>
@stop