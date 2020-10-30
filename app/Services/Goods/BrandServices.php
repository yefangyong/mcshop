<?php


namespace App\Services\Goods;


use App\Models\Goods\Brand;
use App\Services\BaseServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BrandServices extends BaseServices
{
    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * 获取品牌的详细数据
     */
    public function getBrand($id)
    {
        return Brand::query()->find($id);
    }

    /**
     * @param $page
     * @param $limit
     * @param $sort
     * @param $order
     * @param  string[]  $columns
     * @return LengthAwarePaginator
     * 获取品牌分页的数据
     */
    public function getBrandList($page, $limit, $sort, $order, $columns = ['*'])
    {
        $query = Brand::query();
        return $query->when((!empty($sort) && !empty($order)), function ($query) use ($sort, $order) {
            return $query->orderBy($sort, $order);
        })->paginate($limit, $columns, 'page', $page);
//        $query = Brand::query();
//        if (!empty($sort) && !empty($order)) {
//            $query = $query->orderBy($sort, $order);
//        }
//        return $query->paginate($limit, $columns, 'page', $page);
    }
}
