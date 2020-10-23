<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Services\Goods\CategoryServices;
use App\Services\Goods\GoodsServices;
use App\Services\SearchHistoryServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GoodsController extends WxController
{
    protected $only = [];

    /**
     * @param  Request  $request
     * @return JsonResponse
     * 获取商品分类的数据
     */
    public function category(Request $request)
    {
        $id = $request->input('id', 0);
        if (empty($id)) {
            return $this->fail(CodeResponse::PARAM_NOT_EMPTY);
        }
        $currentCategory = CategoryServices::getInstance()->getCategoryById($id);
        if (is_null($currentCategory)) {
            return $this->fail(CodeResponse::SYSTEM_ERROR);
        }
        if ($currentCategory->pid == 0) {
            $parentCategory  = $currentCategory;
            $brotherCategory = CategoryServices::getInstance()->getCategoryByPId($currentCategory->id);
            $currentCategory = !is_null($brotherCategory) ? $brotherCategory->first() : $currentCategory;
        } else {
            $parentCategory  = CategoryServices::getInstance()->getCategoryById($currentCategory->pid);
            $brotherCategory = CategoryServices::getInstance()->getCategoryByPId($currentCategory->pid);
        }
        return $this->success(compact('currentCategory', 'parentCategory', 'brotherCategory'));
    }

    /**
     * @return JsonResponse
     * 获取在售商品的数量
     */
    public function count()
    {
        $count = GoodsServices::getInstance()->countGoodsOnSales();
        return $this->success($count);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * 获取商品的列表
     */
    public function list(Request $request)
    {
        $categoryId = $request->input('categoryId', '');
        $brandId    = $request->input('brandId', '');
        $keyword    = $request->input('keyword', '');
        $isNew      = $request->input('isNew', '');
        $isHot      = $request->input('isHot', '');
        $page       = $request->input('page', 1);
        $limit      = $request->input('limit', 10);
        $order      = $request->input('order', 'desc');
        $sort       = $request->input('sort', 'add_time');

        //todo 参数验证

        if ($this->isLogin() && !empty($keyword)) {
            SearchHistoryServices::getInstance()->save($this->userId(), $keyword, Constant::SEARCH_HISTORY_FROM_WX);
        }

        //查询列表数据
        $goodLists = GoodsServices::getInstance()->GoodsLists($categoryId, $brandId, $isNew, $isHot, $keyword, $page,
            $sort, $order, $limit);

        //查询商品所属类目列表
        $categoryIds = GoodsServices::getInstance()->getCatIds($keyword, $brandId, $isNew, $isHot);

        $categoryList = CategoryServices::getInstance()->getCategoryByIds($categoryIds);

        $lists                       = $this->paginate($goodLists);
        $lists['filterCategoryList'] = $categoryList;
        return $this->success($lists);
    }

}
