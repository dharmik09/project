<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Country;
use App\State;
use App\City;

class StateCityController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function __construct(
    ) {
        $this->objCountry = new Country();
        $this->objState = new State();
        $this->objCity = new City();
    }
    
    public function getState($id)
    {
        $stateDetail = $this->objState->getStateById($id);
        return json_encode($stateDetail);
    }
    
    public function getCity($id)
    {
        $cityDetail = $this->objCity->getCityByStateId($id);
        return json_encode($cityDetail);
    }
}
