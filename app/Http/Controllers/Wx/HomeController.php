<?php

namespace App\Http\Controllers\Wx;


class HomeController extends WxController
{
    protected $only = [];

    public function redirectShareUrl()
    {
        $type = $this->verifyString('type', 'groupon');
        $id   = $this->verifyId('id');

        if ($type == 'groupon') {
            return redirect()->to(env('H5_URL') . '/#/items/detail/' . $id);
        }
        return redirect()->to(env('H5_URL') . '/#/items/detail/' . $id);
    }

}
