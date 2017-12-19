<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Testimonial extends Model
{
    protected $table = 'pro_t_testinomials';

    protected $guarded = [];
    
    public function getAllTestimonials() {
        $testinomials = Testimonial::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->where('t_type', "testinomials")->get();
        return $testinomials;
    }


    public function saveTestimonialDetail($testinomialDetail) {
       if ($testinomialDetail['id'] != '0'){
          $this->where('id', $testinomialDetail['id'])->update($testinomialDetail);
       } else {
          $this->create($testinomialDetail);
       }
         return '1';
    }

    public function deleteTestimonial($id) {
        $flag = true;
        $components = $this->find($id);
        $components->deleted = config::get('constant.DELETED_FLAG');
        $response = $components->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllTestimonialRecords() {
        $testinomials = Testimonial::selectRaw('*')->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $testinomials;
    }
}
