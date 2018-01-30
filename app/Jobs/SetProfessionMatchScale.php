<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Helpers;
use App\ProfessionMatchScale;
use App\TeenagerPromiseScore;

class SetProfessionMatchScale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $objProfessionScale = new ProfessionMatchScale;
        $objTeenagerPromiseScore = new TeenagerPromiseScore;

        $compareLogic = array('HL', 'HM', 'HH', 'ML', 'MM', 'MH', 'LL', 'LM', 'LH');
        //FOR COMPARE LOGIC RESULT, L ='nomatch', M = 'moderate', H ='match'
        $compareLogicResult = array('L', 'M', 'H', 'L', 'H', 'H', 'H', 'H', 'H');
        
        $getCareerMappingFromSystem = Helpers::getCareerMappingFromSystem();
        $getLevel2AssessmentResult = Helpers::getTeenAPIScore($this->userId);
        //save promise score into the table
        if(isset($getLevel2AssessmentResult['APIdataSlug']) && count($getLevel2AssessmentResult['APIdataSlug']) > 0) {
            try {
                $saveProfessionScale = $objTeenagerPromiseScore->saveTeenagerPromiseScore($getLevel2AssessmentResult['APIdataSlug'], $this->userId);
            } catch(\Exception $e) {
                //
            }
        }

        if (isset($getCareerMappingFromSystem[0]) && !empty($getCareerMappingFromSystem[0])) { 
            $professionScale = [];
            foreach ($getCareerMappingFromSystem as $keyId => $valueProfession) { 
                $valueProfession->tcm_scientific_reasoning = (isset($valueProfession->tcm_scientific_reasoning) && $valueProfession->tcm_scientific_reasoning != '') ? $valueProfession->tcm_scientific_reasoning : 'L';
                $valueProfession->tcm_verbal_reasoning = (isset($valueProfession->tcm_verbal_reasoning) && $valueProfession->tcm_verbal_reasoning != '') ? $valueProfession->tcm_verbal_reasoning : 'L';
                $valueProfession->tcm_numerical_ability = (isset($valueProfession->tcm_numerical_ability) && $valueProfession->tcm_numerical_ability != '') ? $valueProfession->tcm_numerical_ability : 'L';
                $valueProfession->tcm_logical_reasoning = (isset($valueProfession->tcm_logical_reasoning) && $valueProfession->tcm_logical_reasoning != '') ? $valueProfession->tcm_logical_reasoning : 'L';
                $valueProfession->tcm_social_ability = (isset($valueProfession->tcm_social_ability) && $valueProfession->tcm_social_ability != '') ? $valueProfession->tcm_social_ability : 'L';
                $valueProfession->tcm_artistic_ability = (isset($valueProfession->tcm_artistic_ability) && $valueProfession->tcm_artistic_ability != '') ? $valueProfession->tcm_artistic_ability : 'L';
                $valueProfession->tcm_spatial_ability = (isset($valueProfession->tcm_spatial_ability) && $valueProfession->tcm_spatial_ability != '') ? $valueProfession->tcm_spatial_ability : 'L';
                $valueProfession->tcm_creativity = (isset($valueProfession->tcm_creativity) && $valueProfession->tcm_creativity != '') ? $valueProfession->tcm_creativity : 'L';
                $valueProfession->tcm_clerical_ability = (isset($valueProfession->tcm_clerical_ability) && $valueProfession->tcm_clerical_ability != '') ? $valueProfession->tcm_clerical_ability : 'L';
                $valueProfession->tcm_doers_realistic = (isset($valueProfession->tcm_doers_realistic) && $valueProfession->tcm_doers_realistic != '') ? $valueProfession->tcm_doers_realistic : 'L';
                $valueProfession->tcm_thinkers_investigative = (isset($valueProfession->tcm_thinkers_investigative) && $valueProfession->tcm_thinkers_investigative != '') ? $valueProfession->tcm_thinkers_investigative : 'L';
                $valueProfession->tcm_creators_artistic = (isset($valueProfession->tcm_creators_artistic) && $valueProfession->tcm_creators_artistic != '') ? $valueProfession->tcm_creators_artistic : 'L';
                $valueProfession->tcm_helpers_social = (isset($valueProfession->tcm_helpers_social) && $valueProfession->tcm_helpers_social != '') ? $valueProfession->tcm_helpers_social : 'L';
                $valueProfession->tcm_persuaders_enterprising = (isset($valueProfession->tcm_persuaders_enterprising) && $valueProfession->tcm_persuaders_enterprising != '') ? $valueProfession->tcm_persuaders_enterprising : 'L';
                $valueProfession->tcm_organizers_conventional = (isset($valueProfession->tcm_organizers_conventional) && $valueProfession->tcm_organizers_conventional != '') ? $valueProfession->tcm_organizers_conventional : 'L';
                $valueProfession->tcm_linguistic = (isset($valueProfession->tcm_linguistic) && $valueProfession->tcm_linguistic != '') ? $valueProfession->tcm_linguistic : 'L';
                $valueProfession->tcm_logical = (isset($valueProfession->tcm_logical) && $valueProfession->tcm_logical != '') ? $valueProfession->tcm_logical : 'L';
                $valueProfession->tcm_musical = (isset($valueProfession->tcm_musical) && $valueProfession->tcm_musical != '') ? $valueProfession->tcm_musical : 'L';
                $valueProfession->tcm_spatial = (isset($valueProfession->tcm_spatial) && $valueProfession->tcm_spatial != '') ? $valueProfession->tcm_spatial : 'L';
                $valueProfession->tcm_bodily_kinesthetic = (isset($valueProfession->tcm_bodily_kinesthetic) && $valueProfession->tcm_bodily_kinesthetic != '') ? $valueProfession->tcm_bodily_kinesthetic : 'L';
                $valueProfession->tcm_naturalist = (isset($valueProfession->tcm_naturalist) && $valueProfession->tcm_naturalist != '') ? $valueProfession->tcm_naturalist : 'L';
                $valueProfession->tcm_interpersonal = (isset($valueProfession->tcm_interpersonal) && $valueProfession->tcm_interpersonal != '') ? $valueProfession->tcm_interpersonal : 'L';
                $valueProfession->tcm_intrapersonal = (isset($valueProfession->tcm_intrapersonal) && $valueProfession->tcm_intrapersonal != '') ? $valueProfession->tcm_intrapersonal : 'L';
                $valueProfession->tcm_existential = (isset($valueProfession->tcm_existential) && $valueProfession->tcm_existential != '') ? $valueProfession->tcm_existential : 'L';

                $variable0 = array_keys($compareLogic, $valueProfession->tcm_scientific_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Scientific Reasoning']);
                $variable1 = array_keys($compareLogic, $valueProfession->tcm_verbal_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Verbal Reasoning']);
                $variable2 = array_keys($compareLogic, $valueProfession->tcm_numerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Numerical Ability']);
                $variable3 = array_keys($compareLogic, $valueProfession->tcm_logical_reasoning . $getLevel2AssessmentResult['APIscale']['aptitude']['Logical Reasoning']);
                $variable4 = array_keys($compareLogic, $valueProfession->tcm_social_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Social Ability']);
                $variable5 = array_keys($compareLogic, $valueProfession->tcm_artistic_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Artistic Ability']);
                $variable6 = array_keys($compareLogic, $valueProfession->tcm_spatial_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Spatial Ability']);
                $variable7 = array_keys($compareLogic, $valueProfession->tcm_creativity . $getLevel2AssessmentResult['APIscale']['aptitude']['Creativity']);
                $variable8 = array_keys($compareLogic, $valueProfession->tcm_clerical_ability . $getLevel2AssessmentResult['APIscale']['aptitude']['Clerical Ability']);

                $variable9 = array_keys($compareLogic, $valueProfession->tcm_doers_realistic . $getLevel2AssessmentResult['APIscale']['personality']['Mechanical']);
                $variable10 = array_keys($compareLogic, $valueProfession->tcm_thinkers_investigative . $getLevel2AssessmentResult['APIscale']['personality']['Investigative']);
                $variable11 = array_keys($compareLogic, $valueProfession->tcm_creators_artistic . $getLevel2AssessmentResult['APIscale']['personality']['Artistic']);
                $variable12 = array_keys($compareLogic, $valueProfession->tcm_helpers_social . $getLevel2AssessmentResult['APIscale']['personality']['Social']);
                $variable13 = array_keys($compareLogic, $valueProfession->tcm_persuaders_enterprising . $getLevel2AssessmentResult['APIscale']['personality']['Enterprising']);
                $variable14 = array_keys($compareLogic, $valueProfession->tcm_organizers_conventional . $getLevel2AssessmentResult['APIscale']['personality']['Conventional']);

                $variable15 = array_keys($compareLogic, $valueProfession->tcm_linguistic . $getLevel2AssessmentResult['APIscale']['MI']['Linguistic']);
                $variable16 = array_keys($compareLogic, $valueProfession->tcm_logical . $getLevel2AssessmentResult['APIscale']['MI']['Logical']);
                $variable17 = array_keys($compareLogic, $valueProfession->tcm_musical . $getLevel2AssessmentResult['APIscale']['MI']['Musical']);
                $variable18 = array_keys($compareLogic, $valueProfession->tcm_spatial . $getLevel2AssessmentResult['APIscale']['MI']['Spatial']);
                $variable19 = array_keys($compareLogic, $valueProfession->tcm_bodily_kinesthetic . $getLevel2AssessmentResult['APIscale']['MI']['Bodily-Kinesthetic']);
                $variable20 = array_keys($compareLogic, $valueProfession->tcm_naturalist . $getLevel2AssessmentResult['APIscale']['MI']['Naturalist']);
                $variable21 = array_keys($compareLogic, $valueProfession->tcm_interpersonal . $getLevel2AssessmentResult['APIscale']['MI']['Interpersonal']);
                $variable22 = array_keys($compareLogic, $valueProfession->tcm_intrapersonal . $getLevel2AssessmentResult['APIscale']['MI']['Intrapersonal']);
                $variable23 = array_keys($compareLogic, $valueProfession->tcm_existential . $getLevel2AssessmentResult['APIscale']['MI']['Existential']);

                $arrayCombinePoint[] = $compareLogicResult[$variable0[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable1[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable2[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable3[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable4[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable5[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable6[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable7[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable8[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable9[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable10[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable11[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable12[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable13[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable14[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable15[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable16[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable17[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable18[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable19[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable20[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable21[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable22[0]];
                $arrayCombinePoint[] = $compareLogicResult[$variable23[0]];

                $L = $M = $H = [];
                $hmlCountArray = array_count_values($arrayCombinePoint);
                if(count($hmlCountArray) > 0) {
                    $L = (isset($hmlCountArray['L'])) ? $hmlCountArray['L'] : 0;
                    $H = (isset($hmlCountArray['H'])) ? $hmlCountArray['H'] : 0;
                    $M = (isset($hmlCountArray['M'])) ? $hmlCountArray['M'] : 0;
                    if ($L > 0) {
                        $answer['matchScale'] = "nomatch";
                    } else if ($M > 0 && $L < 1) {
                        $answer['matchScale'] = "moderate";
                    } else if ($L == 0 && $M == 0) {
                        $answer['matchScale'] = "match";
                    } else {
                        $answer['matchScale'] = "";
                    }
                    $professionScale[$valueProfession->tcm_profession] = $answer['matchScale'];
                }
            }
            //echo "<pre/>"; print_r($professionScale); die();
            if(count($professionScale) > 0) {
                $jsonData = json_encode($professionScale);
                $array = [];
                $arraySave['teenager_id'] = $this->userId;
                $arraySave['match_scale'] = $jsonData;
                $saveProfessionScale = $objProfessionScale->saveTeenagerProfessionScale($arraySave);
            }
        }

        return $this->userId;
    }
}
