<?php


namespace App\Services\Goods;


use App\Models\Goods\Category;
use App\Services\BaseServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryServices extends BaseServices
{
    /**
     * @return Builder[]|Collection
     * 获取一级分类的数据
     */
    public function getL1List()
    {
        return Category::query()->where('deleted',0)->where('pid', 0)->where('level', 'L1')->get();
    }

    /**
     * @param $pid
     * @return Builder[]|Collection
     * 根据一级分类的ID获取二级分类的数据
     */
    public function getL2ListDataByPid($pid)
    {
        return Category::query()->where('deleted',0)->where('pid', $pid)->where('level', 'L2')->get();
    }
}
