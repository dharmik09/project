@if(isset($getQuestionTemplateForProfession[0]) && count($getQuestionTemplateForProfession[0]) > 0)
    @foreach($getQuestionTemplateForProfession as $templateProfession)
        <div class="col-sm-6 flex-items">
            <div class="quiz-box">
                <div class="img">
                    <img src="{{ $templateProfession->gt_template_image }}" alt="{{ $templateProfession->gt_template_title }}">
                </div>
                <h6>{!! $templateProfession->gt_template_title !!}</h6>
                <p title="{{strip_tags($templateProfession->gt_template_descritpion)}}"> {!! strip_tags(str_limit($templateProfession->gt_template_descritpion, '100', '...more')) !!}</p>
                @if ($templateProfession->remaningDays > 0)
                    @if($templateProfession->attempted == 'yes')
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}" >
                            <a href="javascript:void(0);" title="Play now!" class="btn-primary" onclick="getConceptData({{$templateProfession->gt_template_id}})">
                                <span class="unbox-me">Played!</span>
                            </a>
                        </div>   
                    @else
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}">
                            <a href="javascript:void(0);" title="Play now!" class="btn-primary" onclick="getConceptData({{$templateProfession->gt_template_id}})" >
                                <span class="unbox-me">Play now!</span>
                                <span class="coins-outer">
                                    <span class="coins"></span>
                                    @if($templateProfession->gt_coins > 0) {{$templateProfession->remaningDays}} Days Left @else this is free enjoy @endif
                                </span>
                            </a>
                        </div>    
                    @endif
                @elseif($templateProfession->gt_coins == 0)
                    @if($templateProfession->attempted == 'yes')
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}" >
                            <a href="javascript:void(0);" title="Play now!" class="btn-primary" onclick="getConceptData({{$templateProfession->gt_template_id}})">
                                <span class="unbox-me">Played!</span>
                            </a>
                        </div>   
                    @else
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}">
                            <a href="javascript:void(0);" title="Play now!" class="btn-primary" onclick="getConceptData({{$templateProfession->gt_template_id}})">
                                <span class="unbox-me">Play now!</span>
                                <span class="coins-outer">
                                    <span class="coins"></span> 
                                    This is free enjoy
                                </span>
                            </a>
                        </div>
                    @endif
                @else
                    @if($templateProfession->attempted == 'yes')
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}" >
                            <a href="javascript:void(0);" title="Play now!" class="btn-primary" onclick="getConceptData({{$templateProfession->gt_template_id}})">
                                <span class="unbox-me">Played!</span>
                            </a>
                        </div>   
                    @else
                        <div class="unbox-btn set-template-{{$templateProfession->gt_template_id}}">
                            <a href="javascript:void(0);" title="Unlock Me" class="btn-primary" onclick="getTemplateConceptData({{$templateProfession->l4ia_profession_id}}, {{$templateProfession->gt_template_id}})">
                                <span class="unbox-me">Unlock Me</span>
                                <span class="coins-outer">
                                    <span class="coins"></span> 
                                    {{ ($templateProfession->gt_coins > 0) ? number_format($templateProfession->gt_coins) : 0 }} 
                                </span>
                            </a>
                        </div>
                        <div class="modal fade" id="myModal{{$templateProfession->gt_template_id}}" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content custom-modal">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                                        <h4 class="modal-title">Congratulations!</h4>
                                    </div>
                                    <div class="no-coins-availibility">
                                        <div class="modal-body">
                                            <p class="my-coins-info">You have {{ (Auth::guard('teenager')->user()->t_coins > 0) ? number_format(Auth::guard('teenager')->user()->t_coins) : 0 }} ProCoins available.</p>
                                            <p>Click OK to consume your {{ ($templateProfession->gt_coins > 0) ? number_format($templateProfession->gt_coins) : 0 }} ProCoins and play on</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary btn-intermediate" data-dismiss="modal" onclick="saveCoinsForTemplateData({{$templateProfession->l4ia_profession_id}}, {{$templateProfession->gt_template_id}}, 'no')" >ok</button>
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
@else

@endif