<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class CMS extends Model
{

    protected $table = 'pro_cms';
    protected $fillable = ['id', 'cms_slug', 'cms_subject', 'cms_body', 'deleted'];

    public function getCmsForAboutUs()
    {
        $CmsDetails = CMS::Select('pro_cms.cms_body')
                ->where("pro_cms.cms_slug", "aboutus")              
                ->first();
            return $CmsDetails;
    }
    
    public function getCmsForTeam()
    {      
        $cmsDetailsForTeam = CMS::Select('pro_cms.cms_body')
                ->where("pro_cms.cms_slug", "teampage")
                ->first();
        return $cmsDetailsForTeam;
    }
    public function getCmsForContactUs()
    {
       $cmsDetailsForContact = CMS::Select('pro_cms.cms_body')
                ->where("pro_cms.cms_slug", "contactus")
                ->first();       
        return $cmsDetailsForContact;
    }
    
    public function getCmsBySlug($slug)
    {
       $cmsDetails = CMS::Select('pro_cms.cms_body')
                ->where("pro_cms.cms_slug", $slug)
                ->first();       
        return $cmsDetails;
    }
}
