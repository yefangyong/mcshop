<?php


namespace App\Services;


use App\Constant;
use App\Models\Comment;
use App\Services\User\UserServices;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CommentServices extends BaseServices
{
    /**
     * @param $goods_id
     * @param  int  $page
     * @param  int  $limit
     * @param  string  $sort
     * @param  string  $order
     * @return LengthAwarePaginator
     * 获取商品的评论
     */
    public function getGoodsComment($goods_id, $page = 1, $limit = 2, $sort = 'add_time', $order = 'desc')
    {
        return Comment::query()->where('value_id', $goods_id)->where('type',
            Constant::COMMENT_GOOD_TYPE)->orderBy($sort, $order)->paginate($limit,
            ['*'], 'page', $page);
    }

    public function getGoodsCommentWithUserInfo($goodsId, $page = 1, $limit = 2)
    {
        $comment = $this->getGoodsComment($goodsId, $page, $limit);
        $userIds = Arr::pluck($comment->items(), 'user_id');
        $userIds = array_unique($userIds);
        $users   = UserServices::getInstance()->getUsers($userIds)->keyBy('id');
        $data    = collect(($comment->items()))->map(function (Comment $comment) use ($users) {
            $user = $users->get($comment->user_id);
            return [
                'id'           => $comment->id,
                'addTime'      => Carbon::instance($comment->add_time)->toDateTimeString(),
                'content'      => $comment->content,
                'adminContent' => $comment->admin_content,
                'picList'      => $comment->pic_urls,
                'nickname'     => $user->nickname ?? '',
                'avatar'       => $user->avatar ?? ''
            ];
        });
        return ['count' => $comment->total(), 'data' => $data];
    }

}
