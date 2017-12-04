<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Auth;
use Input;
use Config;
use Helpers;
use Redirect;
use Mail;
use Session;
use Response;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Template\Contracts\TemplatesRepository;

class VerifyTeenManagementController extends Controller
{
    public function __construct(TeenagersRepository $teenagersRepository, TemplatesRepository $templatesRepository)
    {
       $this->teenagersRepository = $teenagersRepository;
       $this->templateRepository = $templatesRepository;
    }

    public function index()
    {
        $token = input::get('token');
        $verifyMessage = "Token is empty!";
        if($token != "")
        {
            $teenagerTokenVarify = $this->teenagersRepository->updateTeenagerTokenStatusByToken($token);
            if($teenagerTokenVarify)
            {
                $teenagers = $this->teenagersRepository->updateTeenagerVerifyStatusById($teenagerTokenVarify[0]->tev_teenager);
                if($teenagers){ $verifyMessage = trans('appmessages.email_verify_msg'); } else { $verifyMessage = trans('appmessages.default_error_msg'); }
            }
            else
            {
                $verifyMessage = trans('appmessages.already_email_verify_msg');
            }
        }
        return view('teenager.verifyUser', compact('verifyMessage'));
    }
    
    public function resendVerification($unique_id)
    {
        $teenagerDetailbyId = $this->teenagersRepository->getTeenagerByUniqueId($unique_id);
        if(isset($teenagerDetailbyId->id))
        {
            $replaceArray = array();
            $replaceArray['TEEN_NAME'] = $teenagerDetailbyId->t_name." ".$teenagerDetailbyId->t_lastname;
            $replaceArray['TEEN_UNIQUEID'] = Helpers::getTeenagerUniqueId();
            $replaceArray['TEEN_URL'] = "<a href=" . url("teenager/verify-teenager?token=" . $replaceArray['TEEN_UNIQUEID']) . ">" . url("teenager/verify-teenager?token=" . $replaceArray['TEEN_UNIQUEID']) . "</a>";
            $emailTemplateContent = $this->templateRepository->getEmailTemplateDataByName(Config::get('constant.TEENAGER_VAIRIFIED_EMAIL_TEMPLATE_NAME'));
            $content = $this->templateRepository->getEmailContent($emailTemplateContent->et_body, $replaceArray);
            $data = array();
            $data['subject'] = $emailTemplateContent->et_subject;
            $data['toEmail'] = $teenagerDetailbyId->t_email;
            $data['toName'] = $teenagerDetailbyId->t_name." ".$teenagerDetailbyId->t_lastname;
            $data['content'] = $content;
            $data['teen_token'] = $replaceArray['TEEN_UNIQUEID'];
            $data['teen_url'] = $replaceArray['TEEN_URL'];
            $data['teen_id'] = $teenagerDetailbyId->id;
            Mail::send(['html' => 'emails.Template'], $data, function($message) use ($data) {
                $message->subject($data['subject']);
                $message->to($data['toEmail'], $data['toName']);
                $teenagerTokenDetail = [];
                $teenagerTokenDetail['tev_token'] = $data['teen_token'];
                $teenagerTokenDetail['tev_teenager'] = $data['teen_id'];
                $this->teenagersRepository->addTeenagerEmailVarifyToken($teenagerTokenDetail);
            });
            $responseMsg = 'Hi <strong>'.$teenagerDetailbyId->t_name.' '.$teenagerDetailbyId->t_lastname.'</strong>, <br/> The access link to activate your account has been sent to your registered eMailID <strong>'.$teenagerDetailbyId->t_email.'</strong>';
            return view('teenager.signupVerification', compact('responseMsg'));
        }
        return Redirect::back()->withErrors("No any user found for verification!");
    }

    public function verifyTeenFromSchool()
    {
        $token=input::get('token');
        if($token)
        {
            $teenagerTokenVarify = $this->teenagersRepository->updateTeenagerTokenStatusByToken($token);
            if($teenagerTokenVarify)
            {
                $teenagers = $this->teenagersRepository->updateTeenagerVerifyStatusById($teenagerTokenVarify[0]->tev_teenager);
                if($teenagers){ $verifyMessage = trans('appmessages.email_verify_msg'); } else { $verifyMessage = trans('appmessages.default_error_msg'); }
           }
            else
            {
                $verifyMessage = trans('appmessages.already_email_verify_msg');
            }
        }
        return view('teenager.VarifyUser', compact('verifyMessage'));
    }
}
