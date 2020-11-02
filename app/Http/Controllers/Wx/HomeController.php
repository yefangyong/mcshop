<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\GoodsListInput;
use App\Models\Collect;
use App\Services\CollectServices;
use App\Services\CommentServices;
use App\Services\Goods\BrandServices;
use App\Services\Goods\CategoryServices;
use App\Services\Goods\GoodsServices;
use App\Services\SearchHistoryServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class HomeController extends WxController
{
    protected $only = [];

    public function redirectShareUrl()
    {
        $type = $this->verifyString('type', 'groupon');
        $id   = $this->verifyId('id');

        if ($type == 'groupon') {
            return redirect()->to(env('H5_URL').'/#/items/detail/'.$id);
        }
        return redirect()->to(env('H5_URL').'/#/items/detail/'.$id);
    }

}
