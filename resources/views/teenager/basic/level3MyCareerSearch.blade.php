@if(count($basketsData)>0)
    @foreach($basketsData as $key => $value)
        <section class="sec-category">
            <h2>{{$value->b_name}}</h2>
            <div class="row">
                <div class="col-md-6">
                    <?php
                        $professionAttemptedCount = 0;
                        foreach($value->profession as $k => $v){
                            if(count($v->professionAttempted)>0){
                                $professionAttemptedCount++;
                            }
                        }
                    ?>
                    <p>You have completed <strong>{{$professionAttemptedCount}} of {{count($value->profession)}}</strong> careers</p>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <ul class="match-list">
                            <li><span class="number match-strong">4</span> Strong match</li>
                            <li><span class="number match-potential">5</span> Potential match</li>
                            <li><span class="number match-unlikely">4</span> Unlikely match</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="category-list">
                <div class="row">
                    @foreach($value->profession as $k => $v)
                        @if(count($v->starRatedProfession)>0)
                            <div class="col-sm-6">
                                <div class="category-block match-unlikely">
                                    <figure>
                                        <div class="category-img" style="background-image: url('{{ Storage::url($professionImagePath.$v->pf_logo) }} ')"></div>
                                        <figcaption>
                                            <a href="{{url('teenager/career-detail/')}}/{{$v->pf_slug}}" title="{{$v->pf_name}}">{{$v->pf_name}}</a>
                                        </figcaption>
                                        @if(count($v->professionAttempted)>0)
                                            <span class="complete">Complete</span>
                                        @endif
                                    </figure>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach
@else
    <div class="sec-forum"><span>No result Found</span></div>
@endif