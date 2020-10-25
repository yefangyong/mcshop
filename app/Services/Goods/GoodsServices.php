<?php


namespace App\Services\Goods;


use App\Models\Comment;
use App\Models\FootPrint;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsAttribute;
use App\Models\Goods\GoodsProduct;
use App\Models\Goods\GoodsSpecification;
use App\Models\Goods\Issue;
use App\Services\BaseServices;
use App\Services\User\UserServices;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class GoodsServices extends BaseServices
{
    /**
     * @param $userId
     * @param $goodId
     * 记录用户的足迹
     */
    public function saveFootPrint($userId, $goodId)
    {
        $footPrint = new FootPrint();
        $footPrint->goods_id = $goodId;
        $footPrint->user_id = $userId;
        $footPrint->add_time = Carbon::now()->toDateTimeString();
        $footPrint->update_time = Carbon::now()->toDateTimeString();
        $footPrint->save();
    }

    /**
     * @return Builder[]|Collection
     * 获取商品的问题
     */
    public function getGoodsIssue()
    {
        return Issue::query()->where('deleted', 0)->get();
    }

    /**
     * @param $id
     * @return Builder[]|Collection
     * 获取商品的产品
     */
    public function getGoodsProducts($id)
    {
        return GoodsProduct::query()->where('deleted', 0)->where('goods_id', $id)->get();
    }

    /**
     * @param $id
     * @return Builder[]|Collection|\Illuminate\Support\Collection
     * 获取产品的规格
     */
    public function getGoodsSpecification($id)
    {
        $spec = GoodsSpecification::query()->where('deleted', 0)->where('goods_id', $id)->get();
        $spec = $spec->groupBy('specification');
        return $spec->map(function ($v, $k) {
            return ['name' => $k, 'valueList' => $v];
        })->values();
    }

    public function getGoodsAttributesList($id)
    {
        return GoodsAttribute::query()->where('deleted', 0)->where('goods_id', $id)->get();
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * 获取商品
     */
    public function getGoods($id)
    {
        return Goods::query()->where('deleted', 0)->find($id);
    }

    /**
     * @return int
     * 获取在售商品的数量
     */
    public function countGoodsOnSales()
    {
        return Goods::query()->where('deleted', 0)->where('is_on_sale', 1)->count('id');
    }

    /**
     * @param $categoryId
     * @param $brandId
     * @param $isNew
     * @param $isHot
     * @param $keywords
     * @param $page
     * @param $sort
     * @param $order
     * @param $limit
     * @return LengthAwarePaginator
     * 获取商品的列表
     */
    public function GoodsLists($categoryId, $brandId, $isNew, $isHot, $keywords, $page, $sort, $order, $limit)
    {

        $query = Goods::query()->select([
            'id', 'name', 'brief', 'pic_url', 'is_new', 'is_hot', 'counter_price', 'retail_price'
        ])->where('deleted', 0)->where('is_on_sale', 1);

        $query = $this->getGoodsQuery($query, $keywords, $brandId, $isNew, $isHot);

        if (!empty($categoryId)) {
            $query = $query->where('category_id', $categoryId);
        }

        if (!empty($sort) && !empty($order)) {
            $query = $query->orderBy($sort, $order);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @param $keywords
     * @param $brandId
     * @param $isNew
     * @param $isHot
     * @return mixed
     * 获取商品分类ID的数据
     */
    public function getCatIds($keywords, $brandId, $isNew, $isHot)
    {
        $query = Goods::query()->where('deleted', 0)->where('is_on_sale', 1);
        $query = $this->getGoodsQuery($query, $keywords, $brandId, $isNew, $isHot);
        return $query->select(['category_id'])->pluck('category_id')->toArray();
    }

    private function getGoodsQuery($query, $keywords, $brandId, $isNew, $isHot)
    {
        if (!empty($brandId)) {
            $query = $query->where('brand_id', $brandId);
        }

        if (!empty($isNew)) {
            $query = $query->where('is_new', $isNew);
        }

        if (!empty($isHot)) {
            $query = $query->where('is_hot', $isHot);
        }

        if (!empty($keywords)) {
            $query->Where('keywords', 'like', "%{$keywords}%")->orWhere('name', 'like', "%{$keywords}%");
        }
        return $query;
    }
}
