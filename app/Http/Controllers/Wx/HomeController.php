<?php

namespace App\Http\Controllers\Wx;


use App\Services\AdServices;
use App\Services\Goods\BrandServices;
use App\Services\Goods\CategoryServices;
use App\Services\Goods\GoodsServices;
use App\Services\Promotion\CouponServices;
use App\Services\Promotion\GrouponServices;
use App\Services\SystemServices;
use App\Services\TopicServices;

class HomeController extends WxController
{
    protected $except = ['index'];

    public function index()
    {
        $user        = $this->user();
        $bannerList  = AdServices::getInstance()->queryIndex();
        $channelList = CategoryServices::getInstance()->getL1List(['id', 'name', 'icon_url']);
        if (empty($user)) {
            $couponList = CouponServices::getInstance()->getCouponListByLimit();
        } else {
            //过滤登录用户已经领取的优惠券
            $couponList = CouponServices::getInstance()->getAvailableList($user->id);
        }

        $newGoodsList = GoodsServices::getInstance()->getNewGoods(SystemServices::getInstance()->getNewGoodsLimit());

        $hotGoodsList = GoodsServices::getInstance()->getHotGoods(SystemServices::getInstance()->getHotGoodsLimit());

        $brandList = BrandServices::getInstance()->getBrandByLimit(SystemServices::getInstance()->getBrandLimit(), ['id','name', 'desc', 'pic_url', 'floor_price']);

        $topicList = TopicServices::getInstance()->getTopicByLimit(SystemServices::getInstance()->getTopicLimit());

        $grouponList    = GrouponServices::getInstance()->getGrouponListByLimit();
        $floorGoodsList = [];
        $data           = [
            'banner'         => $bannerList,
            'channel'        => $channelList,
            'couponList'     => $couponList,
            'newGoodsList'   => $newGoodsList,
            'hotGoodsList'   => $hotGoodsList,
            'brandList'      => $brandList,
            'topicList'      => $topicList,
            'grounonList'    => $grouponList,
            'floorGoodsList' => $floorGoodsList
        ];
        return $this->success($data);
    }

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
