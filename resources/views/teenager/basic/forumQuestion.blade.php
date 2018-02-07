@if(count($forumQuestionData)>0)
    @foreach($forumQuestionData as $key => $value)
    <div class="single-article">
        <div class="forum-que-block t-table">
            <div class="author-img t-cell"><a href="#" title="Kelly Cheng"><img src="{{ Storage::url('img/proteen-logo.png') }}" alt="author img"></a></div>
            <div class="forum-que t-cell">
                <h4><a href="{{url('teenager/fetch-question/'.Crypt::encrypt($value->id))}}" title="{{$value->fq_que}}">{{$value->fq_que}}</a></h4>
                <ul class="que-detail">
                    <li class="author-name"><a href="#" title="ProTeen Admin">ProTeen Admin</a></li>
                    <li class="posted-date">{{date('jS M Y',strtotime($value->created_at))}}</li>
                </ul>
            </div>
        </div>
        <div class="forum-ans full-text">
            <div class="ans-detail t-table">
                <div class="ans-author-detail t-cell no-padding">
                    <?php
                        $teenagerName = '';
                        $answerTime = '';
                        $answerText = '';
                        $answerTextPart1 = '';
                        $answerTextPart2 = '';
                        
                        if(isset($value->latestAnswer)){
                            $answerText = $value->latestAnswer->fq_ans;
                            $answerTextPart1 = substr($answerText, 0, 400);
                            $answerTextPart2 = substr($answerText, 400);

                            $answerTime = date('jS M Y',strtotime($value->latestAnswer->created_at));

                        }

                        if(isset($value->latestAnswer->teenager)){
                            $teenagerName = ucfirst($value->latestAnswer->teenager->t_name).' '.ucfirst($value->latestAnswer->teenager->t_lastname);
                        }
                    ?>
                    <h4><a href="#" title="{{$teenagerName}}">{{$teenagerName}}</a></h4>
                    <span class="ans-posted-date">{{$answerTime}}</span>
                </div>
            </div>
            @if(strlen($answerText)>0)
                <div class="forum-answer">
                    <div class="text-full accordion">
                        <div class="accordion-group">
                            <p>
                                {{$answerTextPart1}}
                                <span class="accordion-body collapse" id="viewdetails{{$value->id}}">{{$answerTextPart2}}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <span><a data-toggle="collapse" data-target="#viewdetails{{$value->id}}" readMoreClass">Read More</a></span>
            @else
                <div class="sec-forum"><span>No Answer Found</span></div>
            @endif
        </div>
    </div>
    @endforeach
@endif