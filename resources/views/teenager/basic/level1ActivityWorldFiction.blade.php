<div class="real-world-survey clearfix fictional-world">
    <div class="selection-container">
        <div class="row sec-filter">
            <div class="col-sm-4">
                <div class="icon-slider owl-carousel">
                    @if(isset($mainArray['topTrendingImages'][0]))
                        @foreach($mainArray['topTrendingImages'] as $trendingImages)
                            <div class="icon-item">
                                <div class="icon-content">
                                    <img src="{{$trendingImages['image']}}" alt="{{$trendingImages['name']}}">
                                    <span class="rank-i">{{$trendingImages['rank']}}</span>
                                    <div class="character-name"><span>{{$trendingImages['name']}}</span></div>
                                </div>
                                <span class="rank">Votes : {{$trendingImages['votes']}}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="icon-item">
                            <div class="icon-content">
                                <img src="img/Mario_games_c4.png" alt="Icon image">
                                <span class="rank-i">1</span>
                                <div class="character-name"><span>ProteenLife Default</span></div>
                            </div>
                            <span class="rank">Votes : 147</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group custom-select">
                            <select tabindex="8" class="form-control" onChange="getIconName(this.value, '1', 1)">
                                @if(isset($maincartoonIconCategoryArray) && $maincartoonIconCategoryArray)
                                    <option value="">Select Category</option>
                                    @foreach($maincartoonIconCategoryArray as $mainIconArray)
                                        <option value="{{ $mainIconArray['id'] }}">{{$mainIconArray['name']}}</option>
                                    @endforeach
                                @else
                                    <option value="">Select Category</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <a href="javascript:void(0)" class="form-control add-icon">Add ICON <span class="micro_icon"><i class="icon-plus"></i></span></a>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group search-bar clearfix">
                            <input type="text" placeholder="search" tabindex="1" class="form-control search-feild">
                            <button type="submit" class="btn-search"><i class="icon-search"><!-- --></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="icon-container">
            <div class="no-data hide">
                <p>Please select one category</p>
            </div>
            <div class="icon-container-inner">
                <div class="row">
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Shaktimaan_Superhero_c2.jpeg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Shaktimaan</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Mario_games_c4.png" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Mario</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/cartoon_1477831258.jpg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Iron Man</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Roshesh%20Sarabhai_Wannabe%20Actor_c2.jpeg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Rosesh Sarabhai</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Mario_games_c4.png" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Mario</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/cartoon_1477831258.jpg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Iron Man</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Shaktimaan_Superhero_c2.jpeg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Shaktimaan</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/cartoon_1477831258.jpg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Ironman</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Roshesh%20Sarabhai_Wannabe%20Actor_c2.jpeg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Rosesh Sarabhai</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Mario_games_c4.png" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Mario</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Shaktimaan_Superhero_c2.jpeg" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Shaktimaan</span>
                        </label>
                    </div>
                    <div class="col-sm-3 col-xs-4">
                        <input class="icon-radio" type="radio" name="category_id">
                        <label class="radio_img">
                            <span class="icn">
                                <img src="img/Mario_games_c4.png" alt="">
                                <span class="check"><i class="icon-check-mark" aria-hidden="true"></i></span>
                            </span>
                            <span class="title">Mario</span>
                        </label>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>














<div class="inner_container">
    <div class="landing_container">
        <h1><span class="title_border">Vote</span></h1>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 borderright">
                <a href="<?php if($isQuestionCompleted == 0) { ?> {{ url('/teenager/playLevel1Activity') }} <?php } else { echo "javascript:void(0)"; }?>" class="landing_box landing_l1" onclick="checkLevel1Questions({{$isQuestionCompleted}});">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="vote"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Opinions</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 borderbottom">
                <a href="javascript:void(0);" onClick="playFirstLevelWorldType(1)" class="landing_box landing_l2">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="fiction"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Fictional World</span>
                    </span>
                </a>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 bordertop">
                    <a onClick="playFirstLevelWorldType(2)" href="javascript:void(0);" class="landing_box landing_l3 right">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="real"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Real World</span>
                        </span>
                    </a>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 borderleft">
                    <a onClick="playFirstLevelWorldType(3)" href="javascript:void(0);" class="landing_box landing_l4">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="family"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Your World</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>