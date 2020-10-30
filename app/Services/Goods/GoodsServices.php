<?php


namespace App\Services\Goods;


use App\Input\GoodsListInput;
use App\Models\FootPrint;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsAttribute;
use App\Models\Goods\GoodsProduct;
use App\Models\Goods\GoodsSpecification;
use App\Models\Goods\Issue;
use App\Services\BaseServices;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GoodsServices extends BaseServices
{
    /**
     * @param $ids
     * @return Goods[]|Builder[]|Collection
     * 根据商品的id,获取商品的列表
     */
    public function getGoodsListByIds($ids) {
        return Goods::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param $userId
     * @param $goodId
     * 记录用户的足迹
     */
    public function saveFootPrint($userId, $goodId)
    {
        $footPrint              = new FootPrint();
        $footPrint->goods_id    = $goodId;
        $footPrint->user_id     = $userId;
        $footPrint->update_time = Carbon::now()->toDateTimeString();
        $footPrint->deleted     = 0;
        $footPrint->save();
    }

    /**
     * @return Builder[]|Collection
     * 获取商品的问题
     */
    public function getGoodsIssue()
    {
        return Issue::query()->get();
    }

    /**
     * @param $id
     * @return Builder[]|Collection
     * 获取商品的产品
     */
    public function getGoodsProducts($id)
    {
        return GoodsProduct::query()->where('goods_id', $id)->get();
    }

    /**
     * @param $id
     * @return Builder[]|Collection|\Illuminate\Support\Collection
     * 获取产品的规格
     */
    public function getGoodsSpecification($id)
    {
        $spec = GoodsSpecification::query()->where('goods_id', $id)->get();
        $spec = $spec->groupBy('specification');
        return $spec->map(function ($v, $k) {
            return ['name' => $k, 'valueList' => $v];
        })->values();
    }

    public function getGoodsAttributesList($id)
    {
        return GoodsAttribute::query()->where('goods_id', $id)->get();
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * 获取商品
     */
    public function getGoods($id)
    {
        return Goods::query()->find($id);
    }

    /**
     * @return int
     * 获取在售商品的数量
     */
    public function countGoodsOnSales()
    {
        return Goods::query()->where('is_on_sale', 1)->count('id');
    }

    /**
     * @param  GoodsListInput  $input
     * @return mixed
     * 获取商品的列表
     */
    public function GoodsLists(GoodsListInput $input)
    {

        $query = Goods::query()->select([
            'id', 'name', 'brief', 'pic_url', 'is_new', 'is_hot', 'counter_price', 'retail_price'
        ])->where('is_on_sale', 1);

        $query = $this->getGoodsQuery($query, $input->keyword, $input->brandId, $input->isNew, $input->isHot);

        if (!empty($input->categoryId)) {
            $query = $query->where('category_id', $input->categoryId);
        }

        if (!empty($input->sort) && !empty($input->order)) {
            $query = $query->orderBy($input->sort, $input->order);
        }

        return $query->paginate($input->limit, ['*'], 'page', $input->page);
    }

    /**
     * @param  GoodsListInput  $input
     * @return mixed
     * 获取商品分类ID的数据
     */
    public function getCatIds(GoodsListInput $input)
    {
        $query = Goods::query()->where('is_on_sale', 1);
        $query = $this->getGoodsQuery($query, $input->keyword, $input->brandId, $input->isNew, $input->isHot);
        return $query->select(['category_id'])->pluck('category_id')->toArray();
    }

    private function getGoodsQuery($query, $keywords, $brandId, $isNew, $isHot)
    {
        if (!empty($brandId)) {
            $query = $query->where('brand_id', $brandId);
        }

        if (!is_null($isNew)) {
            $query = $query->where('is_new', $isNew);
        }

        if (!is_null($isHot)) {
            $query = $query->where('is_hot', $isHot);
        }

        if (!empty($keywords)) {
            $query->Where('keywords', 'like', "%{$keywords}%")->orWhere('name', 'like', "%{$keywords}%");
        }
        return $query;
    }
}
