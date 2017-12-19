<?php

namespace App\Http\Controllers\Admin;

use File;
use Image;
use Auth;
use Input;
use Config;
use Request;
use Redirect;
use App\Testimonial;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestimonialRequest;
use Helpers;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class TestimonialManagementController extends Controller
{
    public function __construct(FileStorageRepository $fileStorageRepository) {
        $this->fileStorageRepository = $fileStorageRepository;
        $this->objTestimonial = new Testimonial;
        $this->testimonialOriginalImageUploadPath = Config::get("constant.TESTINOMIAL_ORIGINAL_IMAGE_UPLOAD_PATH");
    }

    public function index() {
        $testimonialOriginalImageUploadPath = $this->testimonialOriginalImageUploadPath;
        $testimonials = $this->objTestimonial->getAllTestimonials();
        return view('admin.ListTestimonial', compact('testimonials', 'testimonialOriginalImageUploadPath'));
    }

    public function add() {
        $testimonial = [];
        $testimonialOriginalImageUploadPath = $this->testimonialOriginalImageUploadPath;
        return view('admin.EditTestimonial', compact('testimonial', 'testimonialOriginalImageUploadPath'));
    }

    public function edit($id) {
        $testimonial = $this->objTestimonial->find($id);
        $testimonialOriginalImageUploadPath = $this->testimonialOriginalImageUploadPath;
        return view('admin.EditTestimonial', compact('testimonial', 'testimonialOriginalImageUploadPath'));
    }

    public function save(TestimonialRequest $testimonialRequest) {
        $testimonialDetail = [];
        $hiddenLogo     = e(input::get('hidden_logo'));
        $testimonialDetail['id'] = e(Input::get('id'));
        $testimonialDetail['t_name'] = e(Input::get('t_name'));
        $testimonialDetail['t_title'] = e(Input::get('t_title'));
        $testimonialDetail['t_description'] = e(Input::get('t_description'));
        $testimonialDetail['deleted'] = e(Input::get('deleted'));
        $testimonialDetail['t_type'] = Input::get('t_type');

        if (Input::file())
        {
            $file = Input::file('t_image');
            if(!empty($file))
            {
                //Check image valid extension 
                $validationPass = Helpers::checkValidImageExtension($file);
                if($validationPass)
                {
                    $fileName = 'testimonial_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->testimonialOriginalImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    
                    if ($hiddenLogo != '')
                    {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->testimonialOriginalImageUploadPath, "s3");
                    }

                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->testimonialOriginalImageUploadPath, $pathOriginal, "s3");
                    \File::delete($this->testimonialOriginalImageUploadPath . $fileName);
                    $testimonialDetail['t_image'] = $fileName;
                }                
            }
        }
        $response = $this->objTestimonial->saveTestimonialDetail($testimonialDetail);
        if ($response) {
             return Redirect::to("admin/testimonials")->with('success',trans('labels.tetimonialupdatesuccess'));
        } else {
            return Redirect::to("admin/testimonials")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->objTestimonial->deleteTestimonial($id);
        if ($return){
           return Redirect::to("admin/testimonials")->with('success', trans('labels.testimonialdeletesuccess'));
        } else {
            return Redirect::to("admin/testimonials")->with('error', trans('labels.commonerrormessage'));
        }
    }

}

