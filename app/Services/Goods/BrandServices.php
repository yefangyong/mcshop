<?php


namespace App\Services\Goods;


use App\Input\PageInput;
use App\Models\Goods\Brand;
use App\Services\BaseServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BrandServices extends BaseServices
{

    public function getBrandByLimit($limit, $columns = ['*'], $offset = 0)
    {
        return Brand::query()->offset($offset)->limit($limit)->get($columns);
    }

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
     * @param  PageInput  $page
     * @param  string[]  $columns
     * @return LengthAwarePaginator
     */
    public function getBrandList(PageInput $page, $columns = ['*'])
    {
        return  Brand::query()->orderBy($page->sort, $page->order)->paginate($page->limit, $columns, 'page', $page->page);
    }
}
