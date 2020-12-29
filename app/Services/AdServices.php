<?php


namespace App\Services;


use App\Models\Ad;

class AdServices extends BaseServices
{
    public function queryIndex()
    {
        return Ad::query()->wherePosition(1)->whereEnabled(1)->get();
    }

}
