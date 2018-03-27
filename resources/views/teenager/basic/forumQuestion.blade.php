@if(count($forumQuestionData)>0)
    @foreach($forumQuestionData as $key => $value)
    <div class="single-article" style="background:#eeeeef;">
        <div class="forum-que-block t-table">
            <div class="author-img t-cell">
                <a href="javascript:void(0);"><i class="icon-hand-simple"></i></a>
            </div>
            <div class="forum-que t-cell">
                <h4><a href="{{url('teenager/forum-question/'.Crypt::encrypt($value->id))}}" title="{{$value->fq_que}}">{{$value->fq_que}}</a></h4>
                <ul class="que-detail">
                    <li class="author-name"><a href="#" title="ProTeen Posted">ProTeen Posted</a></li>
                    <li class="posted-date">{{date('d M Y',strtotime($value->created_at))}}</li>
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
                            $answerTime = date('d M Y',strtotime($value->latestAnswer->created_at));
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
                <div class="forum-answer text-overflow">
                    <div class="text-full">
                        <p>{{$answerText}}</p>
                    </div>
                </div>
                <span><a href="#" title="Read More" class="read-more">Read More</a></span>
            @else
                <div class="sec-forum bg-offwhite"><span>No answer yet, Be the first to answer this question</span></div>
            @endif
        </div>
    </div>
    @endforeach
@endif