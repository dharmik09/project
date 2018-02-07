@if(count($forumAnswerData)>0)
    @foreach($forumAnswerData as $key => $value)
        <div class="article-answer full-text">
            <?php
            
                $teenagerName = '';
                $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';

                if(isset($value->teenager)){
                    $teenagerName = ucfirst($value->teenager->t_name).' '.ucfirst($value->teenager->t_lastname);
                    if(isset($value->teenager->t_photo) && $value->teenager->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->teenager->t_photo) > 0) {
                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->teenager->t_photo;
                    }
                }

            ?>
            <div class="ans-detail t-table">
                <div class="answer-img t-cell"><a href="#" title="{{$teenagerName}}"><img src="{{ Storage::url($teenPhoto) }}" alt="author img"></a></div>
                <div class="ans-author-detail t-cell">
                    <h4><a href="#" title="{{$teenagerName}}">{{$teenagerName}}</a></h4>
                    <span class="ans-posted-date">{{date('jS M Y',strtotime($value->created_at))}}</span>
                </div>
            </div>
            <div class="forum-answer text-overflow">
                <div class="text-full">
                    <p>{{$value->fq_ans}}</p>
                </div>
            </div>
            <span><a href="#" title="Read More" class="read-more">Read More</a></span>
        </div>
    @endforeach
@endif