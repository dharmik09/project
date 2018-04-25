@if(count($institutesData)>0)
    @foreach($institutesData as $key => $value)
    <?php
        $instituteWebsite = "javascript:void(0)";
        $instituteName = "";
        $instituteEstablishmentYear = "-";
        $instituteAddress = "-";
        $institutePhoto = 'img/insti-logo.png';
        $instituteMapUrl = "";
        $instituteAffiliateUniversity = "-";
        $instituteManagement = "-";
        $instituteFeeRange = "-";
        $instituteHostelCount = "-";
        $instituteGender = "Co-Ed";
        $instituteAutonomous = "No";
        $instituteAccreditationScore = "-";
        $instituteAccreditationBody = "-";
        $instituteSpeciality = [];

        if(isset($value->website) && $value->website != ""){
            $instituteWebsite = 'http://'.$value->website;
        }
        
        if(isset($value->college_institution) && $value->college_institution != ""){
            $instituteName = $value->college_institution;
        }
        
        if(isset($value->year_of_establishment) && $value->year_of_establishment != ""){
            $instituteEstablishmentYear = $value->year_of_establishment;
        }
        
        if(isset($value->address_line1) && $value->address_line1 != ""){
            $instituteAddress = $value->address_line1;
        }
        if(isset($value->address_line2) && $value->address_line2 != ""){
            $instituteAddress.= ', '.$value->address_line2;
        }
        if(isset($value->city) && $value->city != ""){
            $instituteAddress.= ', '.$value->city;
        }
        if(isset($value->district) && $value->district != ""){
            $instituteAddress.= ', '.$value->district;
        }
        if(isset($value->pin_code) && $value->pin_code != ""){
            $instituteAddress.= ', '.$value->pin_code;
        }
        
        if($instituteName != ""){
            $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.urlencode($instituteName).'&sensor=false&iwloc=near');

            $output= json_decode($geocode);
            if(count($output->results)>0)
            {
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                $instituteMapUrl = "https://maps.google.com/maps?q=".$latitude.", ".$longitude."&z=10&output=embed&iwloc=near";
            }
            elseif(isset($value->latitude) && $value->latitude != "" && isset($value->longitude) && $value->longitude != "")
            {
                $instituteMapUrl = "https://maps.google.com/maps?q=".$value->latitude.", ".$value->longitude."&z=10&output=embed&iwloc=near";
            }
        }
      
        if(isset($value->affiliat_university) && $value->affiliat_university != ""){
            $instituteAffiliateUniversity = $value->affiliat_university;
        }        
        if(isset($value->management) && $value->management != ""){
            $instituteManagement = $value->management;
        }        
     
        if(isset($value->minimum_fee) && $value->minimum_fee != "" && isset($value->maximum_fee) && $value->maximum_fee != ""){
            $instituteFeeRange = number_format((int)$value->minimum_fee, 0, '.', ',') .' - '. number_format((int)$value->maximum_fee, 0, '.', ',');
        }        
        if(isset($value->hostel_count) && $value->hostel_count != ""){
            $instituteHostelCount = $value->hostel_count;
        }        
        if(isset($value->girl_exclusive) && $value->girl_exclusive != ""){
            if($value->girl_exclusive = 1){
                $instituteGender = "Girls Only";
            }
        }
        if(isset($value->autonomous) && $value->autonomous != ""){
            if($value->autonomous = 1){
                $instituteAutonomous = "Yes";
            }
        }
        if(isset($value->accreditation_score) && $value->accreditation_score != ""){
            $instituteAccreditationScore = $value->accreditation_score;
        }
        if(isset($value->accreditation_body) && $value->accreditation_body != ""){
            $instituteAccreditationBody = $value->accreditation_body;
        }
        if(isset($value->speciality) && $value->speciality != ""){
            $instituteSpeciality = explode("#", $value->speciality);
        }
        if(isset($value->image) && $value->image != ""){
            $institutePhoto = Config::get('constant.PROFESSION_INSTITUTE_PHOTO_THUMB_IMAGE_UPLOAD_PATH') .$value->image;
        }


    ?>
    <div class="institute-block clearfix">
        <div class="row">
            <div class="col-sm-12">
                <div class="institute-address">
                    <div class="row">
                        <div class="institute-img">
                            <figure>
                                <img src="{{ Storage::url($institutePhoto) }}" alt="Institute Photo">
                            </figure>
                        </div>
                        <div class="institute-content">
                            @if(isset($value->is_institute_signup) && $value->is_institute_signup != "" && $value->is_institute_signup == 1)
                                <div class="sec-popup">
                                    <a href="javascript:void(0);" data-trigger="hover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><img src="{{ Storage::url('img/logo.png') }}" alt="" title="This college signed-up on ProTeen."></a>
<!--                                     <div class="hide" id="pop1">
                                        <div class="popover-data">
                                            <a class="close popover-closer"><i class="icon-close"></i></a> 
                                        </div>
                                    </div> -->
                                </div>
                            @endif
                            <h4><a href="{{$instituteWebsite}}" target="_blank">{{ $instituteName }} </a></h4>
                            <h5><strong>Year of Establishment </strong>{{$instituteEstablishmentYear}}</h5>
                            <h5><strong>Affiliated University </strong>{{$instituteAffiliateUniversity}} </h5>
                            <h5><strong>Address </strong>{{$instituteAddress}}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 ">
                <div class="institute-detail-content">
                    <div class="row">
                       <div class="col-sm-5 pull-right">
                            <div class="iframe-sec">
                                <p>
                                    <iframe width="100%" height="250" frameborder="0" style="border:0" allowfullscreen src="{{$instituteMapUrl}}"></iframe>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="institute-detail">
                                        <li><strong>Management Type </strong>{{$instituteManagement}}</li>
                                        @if($instituteAccreditationScore != "")
                                            <li><strong>Accreditation CGPA </strong>{{$instituteAccreditationScore}}</li>
                                            <li><strong>Accreditation By </strong>{{$instituteAccreditationBody}}</li>
                                        @endif
                                        <li><strong>Fees in <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?> </strong> {{$instituteFeeRange}}</li>
                                        <li><strong>Hostel </strong>{{$instituteHostelCount}}</li>
                                        <li><strong>Gender </strong>{{$instituteGender}}</li>
                                        <li><strong>Autonomous </strong>{{$instituteAutonomous}}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>Education Streams</h5>
                                    <div class="sec-tags">
                                        @if(count($instituteSpeciality)>0)
                                            <ul class="tag-list">
                                                @forelse($instituteSpeciality as $key => $value)
                                                    <li><a href="{{ url('teenager/institute') }}?speciality={{$value}}" title="{{$value}}">{{$value}}</a></li>
                                                @empty
                                                @endforelse
                                            </ul>
                                        @else
                                            No Courses found
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="sec-forum bg-offwhite"><span>No result found, try different search</span></div>
@endif