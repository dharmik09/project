@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Dashboard Home</title>
@endpush

@section('content')
<!--mid content-->
    <div class="bg-offwhite">
        <div class="container">
            <div class="sec-forum">
                <span>Forum module</span>
            </div>
            <!-- sec chat -->
            <div class="sec-chat clearfix">
                <div class="tab">
                    <div class="chat-slider">
                        <ul class="nav nav-tabs progress-tab" id="main-slider">
                            <li class="chat-tab active"><a data-toggle="tab" href="#menu1">
                        <span class="chat-img"><img src="img/alex.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                    </a></li>
                            <li class="chat-tab"><a data-toggle="tab" href="#menu2">
                        <span class="chat-img"><img src="img/diana.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                        </a></li>
                            <li class="chat-tab"><a data-toggle="tab" href="#menu3">
                        <span class="chat-img"><img src="img/alex.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                    </a></li>
                            <li class="chat-tab"><a data-toggle="tab" href="#menu4">
                        <span class="chat-img"><img src="img/diana.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                    </a></li>
                            <li class="chat-tab"><a data-toggle="tab" href="#menu5">
                        <span class="chat-img"><img src="img/alex.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                    </a></li>
                            <li class="chat-tab"><a data-toggle="tab" href="#menu6">
                        <span class="chat-img"><img src="img/mike.jpg" alt="chat-img"></span>
                        <span class="member-detail">
                            <ul class="option">
                                <li><i class="icon-user-chat"></i></li>
                                <li><i class="icon-check-mark"></i></li>
                            </ul>
                            <span class="member-info">
                                <span class="name">Joe Smith</span>
                                <span class="detail">Proin volutpat eros libero, et sagittis metus posuere id...</span>
                            </span>
                        </span>
                    </a></li>
                        </ul>
                        <!--<button class="tablinks" onclick="openCity(event, 'London')" id="defaultOpen">London</button>
                    <button class="tablinks" onclick="openCity(event, 'Paris')">Paris</button>
                    <button class="tablinks" onclick="openCity(event, 'Tokyo')">Tokyo</button>-->
                    </div>
                </div>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="menu2" class="tab-pane fade in">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade in">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="menu4" class="tab-pane fade in">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="menu5" class="tab-pane fade in">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="menu6" class="tab-pane fade in">
                        <div class="chat-screen">
                            <div class=" visible-xs close">
                                <i class="icon-close"></i>
                            </div>
                            <h3 class="member-name">Jennifer Garcia</h3>
                            <div class="chat-convo clearfix">
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                                <div class="member-msg">
                                    <p>Etiam ac rhoncus elit, ac consequat urna.</p>
                                </div>
                                <div class="user-msg">
                                    <p>Integer lobortis vestibulum ipsum id commodo.</p>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form>
                                    <ul class="chat-uploads">
                                        <li>
                                            <a href="#" title="file upload"><i class="icon-folder"></i></a>
                                        </li>
                                        <li>
                                            <a href="#" title="image upload"><i class="icon-image"></i></a>
                                        </li>
                                    </ul>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="send-btn">
                                        <a href="#" title="Send"><i class="icon-play-simple"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- sec chat end-->
        </div>
    </div>
    <!--mid content end-->
        
@stop
