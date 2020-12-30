<?php

namespace App\Models;




/**
 * App\Models\Feedback
 *
 * @property int $id
 * @property int $user_id 用户表的用户ID
 * @property string $username 用户名称
 * @property string $mobile 手机号
 * @property string $feed_type 反馈类型
 * @property string $content 反馈内容
 * @property int $status 状态
 * @property int|null $has_picture 是否含有图片
 * @property string|null $pic_urls 图片地址列表，采用JSON数组格式
 * @property \Illuminate\Support\Carbon|null $add_time 创建时间
 * @property \Illuminate\Support\Carbon|null $update_time 更新时间
 * @property bool|null $deleted 逻辑删除
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereFeedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereHasPicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback wherePicUrls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feedback whereUsername($value)
 * @mixin \Eloquent
 */
class Feedback extends BaseModel
{

}
