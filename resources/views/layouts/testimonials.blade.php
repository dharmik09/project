<div class="container-small clearfix">
    <ul class="testimonial-slider owl-carousel clearfix">
        @forelse ($testimonials as $testimonial)
            <li class="clearfix">
                <div class="testimonial-img">
                    <img src="{{Storage::url('uploads/testimonial/'.$testimonial->t_image)}}" alt="user">
                </div>
                <div class="testimonial-content">
                    <span><img src="{{Storage::url($quoteImage)}}" alt="quote"></span>
                    <p>{!! $testimonial->t_description !!} - <strong>{{$testimonial->t_name}}</strong></p>
                    <h5><a href="#" title="Lorem ipsum"></a>{{$testimonial->t_title}}  </h5>
                </div>
            </li>
        @empty
            No Testinomials found.
        @endforelse
    </ul>
</div>