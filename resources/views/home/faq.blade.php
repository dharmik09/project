@extends('layouts.home-master')

@push('script-header')
    <title>FAQ</title>
@endpush

@section('content')
    <div class="bg-offwhite">
        <!-- mid section starts-->
        <div class="learning-heading faq">
            <div class="container">
            <h1 class="font-blue">How to</h1>
            <p>Frequently asked questions</p>
            </div>
        </div>
        <!-- accordian section-->
        <div class="sec-accordian sec-faq">
            <div class="container">
                <div class="learning-guidance faq-accordian">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="collapsed"><span>Question:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit?</a>
                                </h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion1">
                                <div class="panel-body">
                               <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                <p>Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion2" class="collapsed"><span>Question:</span> Proin volutpat eros libero, et sagittis metus posuere id. Mauris mattis velit risus,
                                        nec tristique erat mattis sit amet?</a>
                                </h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion2">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion3" class="collapsed"><span>Question:</span> Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet
                                    mattisac sit amet turpismolestie lacus non, elementum velit?</a>
                                </h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion3">
                                <div class="panel-body">
                                   <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion4" class="collapsed"><span>Question:</span> Proin a neque hendrerit, molestie lacus non, elementum velit. Nunc mattis justo magna,tempor faucibus diam commodo sit amet?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion4">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion5" class="collapsed"><span>Question:</span> Integer lobortis vestibulum ipsum id commodo?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion5">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion6" class="collapsed"><span>Question:</span> Mauris id ante eget lectus iaculis pellentesque eu efficitur nisl. Proin sagittis nec orci?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion6">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion7" class="collapsed"><span>Question:</span> Proin a neque hendrerit, molestie lacus non, elementum velit. Nunc mattis justo magna,tempor faucibus diam commodo sit amet?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion7">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion8" class="collapsed"><span>Question:</span> Integer lobortis vestibulum ipsum id commodo. Curabitur non turpis eget turpis laoreet mattisac sit amet turpismolestie lacus non, elementum velit?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion8">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion9" class="collapsed"><span>Question:</span> Proin a neque hendrerit, molestie lacus non, elementum velit. Nunc mattis justo magna, tempor faucibus diam commodo sit amet?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion9">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion10" class="collapsed"><span>Question:</span> Integer lobortis vestibulum ipsum id commodo?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion10">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-parent="#accordion" data-toggle="collapse" href="#accordion11" class="collapsed"><span>Question:</span> Mauris id ante eget lectus iaculis pellentesque eu efficitur nisl. Proin sagittis nec orci?</a></h4>
                            </div>
                            <div class="panel-collapse collapse" id="accordion11">
                                <div class="panel-body">
                                    <p><span>Answer:</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem. Fusce ut est id sem pellentesque viverra. Sed aliquam mi pellentesque suscipit dignissim. Morbi bibendum turpis vel suscipit accumsan.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc.</p>
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
