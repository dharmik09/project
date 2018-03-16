@if(count($institutesData)>0)
    @foreach($institutesData as $key => $value)
    <?php
        $instituteWebsite = "javascript:void(0)";
        $instituteName = "";
        $instituteEstablishmentYear = "-";
        $instituteAddress = "-";
        $institutePhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
        $instituteMapUrl = "";
        $instituteAffiliateUniversity = "-";
        $instituteManagement = "-";
        $instituteFeeRange = "-";
        $instituteHostelCount = "-";
        $instituteGender = "General";
        $instituteAccreditation = 0;
        $instituteAccreditationBody = "-";
        $instituteSpeciality = [];

        if(isset($value->website) && $value->website != ""){
            $instituteWebsite = $value->website;
        }
        
        if(isset($value->college_institution) && $value->college_institution != ""){
            $instituteName = $value->college_institution;
        }
        
        if(isset($value->year_of_establishment) && $value->year_of_establishment != ""){
            $instituteEstablishmentYear = "Establish in ".$value->year_of_establishment;
        }
        
        if(isset($value->address_line1) && $value->address_line1 != ""){
            $instituteAddress = $value->address_line1;
        }
        
        if(isset($value->latitude) && $value->latitude != "" && $value->latitude != "NA" && isset($value->longitude) && $value->longitude != "" && $value->longitude != "NA"){
            $instituteMapUrl = "http://maps.google.com/maps?q=".$value->latitude.", ".$value->longitude."&z=15&output=embed";
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
        if(isset($value->is_accredited) && $value->is_accredited != ""){
            $instituteAccreditation = $value->is_accredited;
        }
        if(isset($value->accreditation_body) && $value->accreditation_body != ""){
            $instituteAccreditationBody = $value->accreditation_body;
        }
        if(isset($value->speciality) && $value->speciality != ""){
            $instituteSpeciality = explode("#", $value->speciality);
        }


    ?>
    <div class="institute-block clearfix">
        <div class="row">
            <div class="col-sm-12">
                <div class="institute-address">
                    <div class="row">
                        <div class="institute-img">
                            <figure>
                                <img src="{{ Storage::url($institutePhoto) }}" alt="logo">
                            </figure>
                        </div>
                        <div class="institute-content">
                            <h4><a href="{{$instituteWebsite}}" target="_blank"><?php echo ucwords($instituteName); ?> </a></h4>
                            <h5><strong>{{$instituteEstablishmentYear}}</strong></h5>
                            <p>{{$instituteAddress}}</p>
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
                                        <li><strong>Affiliat University : </strong>{{$instituteAffiliateUniversity}} </li>
                                        <li><strong>Management : </strong>{{$instituteManagement}}</li>
                                        @if($instituteAccreditation = 1)
                                            <li><strong>Accreditation :  </strong>Yes</li>
                                            <li><strong>Accreditation Body : </strong>{{$instituteAccreditationBody}}</li>
                                        @else
                                            <li><strong>Accreditation :  </strong>No</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="institute-detail">
                                        <li><strong>Fee Range : </strong> {{$instituteFeeRange}}</li>
                                        <li><strong>Hostel Count : </strong>{{$instituteHostelCount}}</li>
                                        <li><strong>Gender : </strong>{{$instituteGender}}</li>
                                    </ul>
                                </div>
                                <div class="col-sm-12">
                                    <h5>Education Stream :</h5>
                                    <div class="sec-tags">
                                        @if(count($instituteSpeciality)>0)
                                            <ul class="tag-list">
                                                @forelse($instituteSpeciality as $key => $value)
                                                        <li>{{$value}}</li>
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
<div class="sec-forum"><span>No result Found</span></div>
@endif