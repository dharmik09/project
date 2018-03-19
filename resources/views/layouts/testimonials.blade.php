<div class="container-small clearfix">
    <ul class="testimonial-slider owl-carousel clearfix">
        @forelse ($testimonials as $testimonial)
            <li class="clearfix">
                <div class="testimonial-img">
                    <img src="{{Storage::url('uploads/testimonial/'.$testimonial->t_image)}}" alt="user">
                </div>
                <div class="testimonial-content">
                    <span><img src="{{Storage::url($quoteImage)}}" alt="quote"></span>
                    <p>{!! $testimonial->t_description !!}</p>
                    <h5><a href="#" title="Lorem ipsum"></a>
                        <?php 
                        $arrangedTitle = explode(',', $testimonial->t_title);
                        if (count($arrangedTitle) > 0) { 
                            foreach ($arrangedTitle as $title) {
                                ?>
                                {{$title}}<br/>
                            <?php }
                        } else { ?>
                            {{$testimonial->t_title}}
                        <?php } ?>  
                    </h5>
                </div>
            </li>
        @empty
            No Testinomials found.
        @endforelse
    </ul>
</div>