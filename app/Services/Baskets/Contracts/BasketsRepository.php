<?php

namespace App\Services\Baskets\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Baskets;

interface BasketsRepository extends BaseRepository
{
    /**
     * @return array of all active baskets in the application
     */
    public function getAllBaskets();

    /**
     * Save CMS detail passed in $basketDetail array
     */
    public function saveBasketDetail($basketDetail);
    /**
     * Delete Basket by $id
    */
    public function deleteBasket($id);
    /**
     * get Basket data from basket name
     */
    public function getBasketData($basketName);
    
}
