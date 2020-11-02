<?php


namespace App\Services\Promotion;


use App\CodeResponse;
use App\Constant;
use App\Exceptions\BusinessException;
use App\Input\PageInput;
use App\Models\Promotion\Groupon;
use App\Models\Promotion\GrouponRules;
use App\Services\BaseServices;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\AbstractFont;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GrouponServices extends BaseServices
{
    public function payGrouponOrder($orderId)
    {
        $groupon = $this->getGrouponByOrderId($orderId);

        if (is_null($groupon)) {
            return;
        }

        $rule = $this->getGrouponRuleById($groupon->rules_id);

        if ($groupon->groupon_id == 0) {
            $groupon->share_url = $this->createGroupShareImage($rule);
        }

        $groupon->status = Constant::Groupon_STATUS_ON;
        $isSuccess       = $groupon->save();
        if (!$isSuccess) {

        }
    }

    /**
     * 创建团购分享图片
     * 1、获取链接，创建二维码
     * 2、合成图片
     * 3、保存图片，返回图片地址
     * @param  GrouponRules  $rules
     * @return string
     */
    public function createGroupShareImage(GrouponRules $rules)
    {
        $shareUrl   = 'http://127.0.0.1/test/'.$rules->goods_id;
        $qrcode     = QrCode::format('png')->margin(1)->size(290)->generate($shareUrl);
        $goodsImage = Image::make($rules->pic_url)->resize(660, 660);
        $image      = Image::make(resource_path('/images/back_groupon.png'))->insert($qrcode, 'top-left', 460, 770)
            ->insert($goodsImage, 'top-left', 71, 69)->text($rules->goods_name, 65, 867, function (AbstractFont $font) {
                $font->color(array(167, 136, 69));
                $font->file(resource_path('ttf/msyh.ttf'));
                $font->size(28);
            });
        return $image->encode();
    }

    /**
     * @param $orderId
     * @param  string[]  $column
     * @return Groupon|Builder|Model|object|null
     * 根据订单Id获取团购数据
     */
    public function getGrouponByOrderId($orderId, $column = ['*'])
    {
        return Groupon::query()->whereOrderId($orderId)->first($column);
    }


    /**
     * @param  PageInput  $page
     * @param  string[]  $column
     * @return LengthAwarePaginator
     * 获取团购规则列表数据
     */
    public function getGroupRuleLists(PageInput $page, $column = ['*'])
    {
        return GrouponRules::query()->where('status', Constant::Groupon_RULE_STATUS_ON)->orderBy($page->sort,
            $page->order)->paginate($page->limit, $column, 'page', $page->page);
    }

    /**
     * @param $ruleId
     * @param  string[]  $column
     * @return GrouponRules|GrouponRules[]|Builder|Builder[]|Collection|Model|null
     * 获取团购规则的数据
     */
    public function getGrouponRuleById($ruleId, $column = ['*'])
    {
        return GrouponRules::query()->find($ruleId, $column);
    }

    /**
     * @param $grouponId
     * @return int
     * 获取参团的人数
     */
    public function countGrouponJoin($grouponId)
    {
        return Groupon::query()->whereGrouponId($grouponId)->where('status', '!=',
            Constant::Groupon_STATUS_NONE)->count(['id']);
    }

    /**
     * @param $userId
     * @param $grouponId
     * @return bool
     * 判断这个用户是否已经参团或者开团
     */
    public function isOpenOrJoin($userId, $grouponId)
    {
        return Groupon::query()->whereUserId($userId)->where(function (Builder $builder) use ($grouponId) {
            return $builder->where('groupon_id', $grouponId)->orWhere('id', $grouponId);
        })->where('status', '!=', Constant::Groupon_STATUS_NONE)->exists();
    }

    /**
     * @param $userId
     * @param $ruleId
     * @param  null  $linkId
     * @throws BusinessException
     * 检查用户是否有开团的资格
     */
    public function checkGrouponRulesValid($userId, $ruleId, $linkId = null)
    {
        //卫语句
        if ($ruleId == null || $ruleId < 0) {
            return;
        }
        $grouponRule = $this->getGrouponRuleById($ruleId);
        if (is_null($grouponRule)) {
            $this->throwBusinessException(CodeResponse::PARAM_NOT_EMPTY);
        }
        if ($grouponRule->status == Constant::Groupon_RULE_STATUS_DOWN_EXPIRE) {
            $this->throwBusinessException(CodeResponse::GROUPON_EXPIRED);
        }
        if ($grouponRule->status == Constant::Groupon_RULE_STATUS_DOWN_ADMIN) {
            $this->throwBusinessException(CodeResponse::GROUPON_OFFLINE);
        }

        if ($linkId == null || $linkId < 0) {
            return;
        }

        if ($this->countGrouponJoin($linkId) >= $grouponRule->discount_member) {
            $this->throwBusinessException(CodeResponse::GROUPON_FULL);
        }

        if ($this->isOpenOrJoin($userId, $linkId)) {
            $this->throwBusinessException(CodeResponse::GROUPON_JOIN);
        }
    }

    /**
     * @param  int  $groupId  团购ID
     * @param  string[]  $column  字段
     * @return Groupon|Groupon[]|Builder|Builder[]|Collection|Model|null
     * 获取团购的数据
     */
    public function getGrouponById(int $groupId, $column = ['*'])
    {
        return Groupon::query()->find($groupId, $column);
    }

    /**
     * @param $ruleId
     * @param $userId
     * @param $orderId
     * @param $linkId
     * @return bool
     * 保存团购相关的数据
     */
    public function saveGrouponData($ruleId, $userId, $orderId, $linkId = null)
    {
        if ($ruleId == null || $ruleId < 0) {
            return $ruleId;
        }
        $groupon           = new Groupon();
        $groupon->order_id = $orderId;
        $groupon->status   = Constant::Groupon_STATUS_NONE;
        $groupon->user_id  = $userId;
        $groupon->rules_id = $ruleId;

        //参与者
        if ($linkId != null && $linkId > 0) {
            $groupon->groupon_id      = $linkId;
            $baseGroupon              = $this->getGrouponById($linkId);
            $groupon->creator_user_id = $baseGroupon->creator_user_id;
            $groupon->share_url       = $baseGroupon->share_url;
            $groupon->save();
        }
        $groupon->creator_user_id   = $userId;
        $groupon->groupon_id        = 0;
        $groupon->creator_user_time = Carbon::now()->toDateTimeString();
        return $groupon->save();
    }
}
