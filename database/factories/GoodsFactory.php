<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Goods\Goods::class, function (Faker $faker) {
    \App\Tools\Logs::sql();
    return [
        'goods_sn'      => $faker->word,
        'name'          => "测试商品".$faker->word,
        'category_id'   => 1008009,
        'brand_id'      => 0,
        'gallery'       => [],
        'keywords'      => "",
        'brief'         => '测试',
        'is_on_sale'    => 1,
        'sort_order'    => $faker->numberBetween(1, 999),
        'pic_url'       => 'http://yanxuan.nosdn.127.net/3bd73b7279a83d1cbb50c0e45778e6d6.png',
        'share_url'     => $faker->url,
        'is_new'        => $faker->boolean,
        'is_hot'        => $faker->boolean,
        'unit'          => "件",
        'counter_price' => 919,
        'retail_price'  => 899,
        'detail'        => $faker->text
    ];
});

$factory->define(\App\Models\Goods\GoodsProduct::class, function (Faker $faker) {
    $goods = factory(\App\Models\Goods\Goods::class)->create();
    $spec  = factory(\App\Models\Goods\GoodsSpecification::class)->create(['goods_id' => $goods->id]);
    return [
        'goods_id'      => $goods->id,
        'specifications' => [$spec->value],
        'price'         => 999,
        'number'        => 100,
        'url'           => 'http://yanxuan.nosdn.127.net/3bd73b7279a83d1cbb50c0e45778e6d6.png'
    ];
});

$factory->define(\App\Models\Goods\GoodsSpecification::class, function (Faker $faker) {
    return [
        'goods_id'      => 0,
        'specification' => '规格',
        'value'         => '标准'
    ];
});

$factory->define(\App\Models\Promotion\GrouponRules::class, function () {
   return  [
       'goods_id' => 0,
       'goods_name' => '',
       'pic_url' => '',
       'discount' => 0,
       'discount_member' => 2,
       'expire_time' => now()->addDays(10)->toDateTimeString()
   ];
});

$factory->state(\App\Models\Goods\GoodsProduct::class, 'groupon',function () {
    return [];
})->afterCreatingState(\App\Models\Goods\GoodsProduct::class, 'groupon', function (\App\Models\Goods\GoodsProduct $product) {
    $good = \App\Services\Goods\GoodsServices::getInstance()->getGoods($product->goods_id);
    factory(\App\Models\Promotion\GrouponRules::class)->create([
        'goods_id' => $product->goods_id,
        'goods_name' => $good->name,
        'pic_url' => $good->pic_url,
        'discount' => 2
    ]);
});


