<?php

namespace App\Http\Controllers\Wx;

use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\GoodsListInput;
use App\Models\Collect;
use App\Services\CollectServices;
use App\Services\CommentServices;
use App\Services\Goods\BrandServices;
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
     * @throws BusinessException
     * 获取商品的详细信息
     */
    public function detail(Request $request)
    {
        $id = $this->verifyId('id');
        //商品信息
        $info = GoodsServices::getInstance()->getGoods($id);
        if (empty($info)) {
            return $this->fail(CodeResponse::SYSTEM_ERROR);
        }
        //商品属性
        $goodAttributeList = GoodsServices::getInstance()->getGoodsAttributesList($id);

        //商品规格
        $goodSpecification = GoodsServices::getInstance()->getGoodsSpecification($id);

        //商品规格对应的数量和价格
        $goodProductList = GoodsServices::getInstance()->getGoodsProducts($id);

        //商品问题
        $goodIssue = GoodsServices::getInstance()->getGoodsIssue();

        //商品品牌商
        $goodBrand = $info->brand_id ? BrandServices::getInstance()->getBrand($info->brand_id) : (object) [];

        //商品评论
        $goodComment = CommentServices::getInstance()->getGoodsCommentWithUserInfo($id);

        //用户收藏数
        $useHasCollect = CollectServices::getInstance()->getGoodsCollect($id);

        //记录用户足迹
        if ($this->isLogin()) {
            GoodsServices::getInstance()->saveFootPrint($this->userId(), $id);
        }

        //todo 团购信息

        return $this->success([
            'info'              => $info,
            'userHasCollect'    => $useHasCollect,
            'issue'             => $goodIssue,
            'comment'           => $goodComment,
            'specificationList' => $goodSpecification,
            'productList'       => $goodProductList,
            'attribute'         => $goodAttributeList,
            'brand'             => $goodBrand,
            'groupon'           => [],
            'share'             => false,
            'shareImage'        => $info->share_url
        ]);

    }


    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws BusinessException
     * 获取商品分类的数据
     */
    public function category(Request $request)
    {
        $id = $this->verifyId('id');
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
     * @throws BusinessException
     *
     */
    public function list(Request $request)
    {
        $input = GoodsListInput::new();

        if ($this->isLogin() && !empty($input->keyword)) {
            SearchHistoryServices::getInstance()->save($this->userId(), $input->keyword, Constant::SEARCH_HISTORY_FROM_WX);
        }

        //查询列表数据
        $goodLists = GoodsServices::getInstance()->GoodsLists($input);

        //查询商品所属类目列表
        $categoryIds = GoodsServices::getInstance()->getCatIds($input);

        $categoryList = CategoryServices::getInstance()->getCategoryByIds($categoryIds);

        $lists                       = $this->paginate($goodLists);
        $lists['filterCategoryList'] = $categoryList;
        return $this->success($lists);
    }

}
