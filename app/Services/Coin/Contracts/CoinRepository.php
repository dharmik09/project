<?php

namespace App\Services\Coin\Contracts;
use App\Services\Repositories\BaseRepository;
use App\Coins;

interface CoinRepository extends BaseRepository
{
    public function getAllCoins();

    /**
     * Save Coins detail passed in $coinDetail array
     */
    public function saveCoinDetail($coinDetail);

    public function deleteCoins($id);


}
