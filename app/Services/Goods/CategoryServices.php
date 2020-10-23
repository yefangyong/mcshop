<?php


namespace App\Services\Goods;


use App\Models\Goods\Category;
use App\Services\BaseServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryServices extends BaseServices
{
    /**
     * @return Builder[]|Collection
     * 获取一级分类的数据
     */
    public function getL1List()
    {
        return Category::query()->where('deleted', 0)->where('pid', 0)->where('level', 'L1')->get();
    }

    /**
     * @param $pid
     * @return Builder[]|Collection
     * 根据一级分类的ID获取二级分类的数据
     */
    public function getL2ListDataByPid($pid)
    {
        return Category::query()->where('deleted', 0)->where('pid', $pid)->where('level', 'L2')->get();
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     * 根据主键获取分类的数据
     */
    public function getCategoryById($id)
    {
        return Category::query()->where('deleted', 0)->find($id);
    }

    /**
     * @param $pid
     * @return Builder[]|Collection
     * 根据分类的父类ID获取商品的数据
     */
    public function getCategoryByPId($pid)
    {
        return Category::query()->where('deleted', 0)->where('pid', $pid)->get();
    }

    /**
     * @param $ids
     * @return Builder[]|Collection
     * 根据主键ID（数组）获取分类数据
     */
    public function getCategoryByIds($ids)
    {
        return Category::query()->where('deleted', 0)->whereIn('id', $ids)->get();
    }
}
