<?php

namespace App\Models;


/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $value_id 如果type=0，则是商品评论；如果是type=1，则是专题评论。
 * @property int $type 评论类型，如果type=0，则是商品评论；如果是type=1，则是专题评论；
 * @property string|null $content 评论内容
 * @property string|null $admin_content 管理员回复内容
 * @property int $user_id 用户表的用户ID
 * @property int|null $has_picture 是否含有图片
 * @property array|null $pic_urls 图片地址列表，采用JSON数组格式
 * @property int|null $star 评分， 1-5
 * @property string|null $add_time 创建时间
 * @property string|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAdminContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHasPicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePicUrls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereValueId($value)
 * @mixin \Eloquent
 */
class Comment extends BaseModel
{
    protected $casts = [
        'pic_urls' => 'array'
    ];

}
