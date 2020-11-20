<?php


namespace App\Services\Order;


use App\Services\BaseServices;

class ExpressServices extends BaseServices
{
    public function getExpressName($code)
    {
        return [
                   "ZTO"      => "中通快递",
                   "YTO"      => "圆通速递",
                   "YD"       => "韵达速递",
                   "YZPY"     => "邮政快递包裹",
                   "EMS"      => "EMS",
                   "DBL"      => "德邦快递",
                   "FAST"     => "快捷快递",
                   "ZJS"      => "宅急送",
                   "TNT"      => "TNT快递",
                   "UPS"      => "UPS",
                   "DHL"      => "DHL",
                   "FEDEX"    => "FEDEX联邦(国内件)",
                   "FEDEX_GJ" => "FEDEX联邦(国际件)",
               ][$code] ?? '';
    }
}
