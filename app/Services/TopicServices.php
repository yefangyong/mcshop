<?php


namespace App\Services;


use App\Input\PageInput;
use App\Models\Topic;
use App\Services\Goods\GoodsServices;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TopicServices extends BaseServices
{
    public function getTopicByLimit($limit, $offset = 0, $order = 'desc', $sort = 'add_time')
    {
        return Topic::query()->orderBy($sort, $order)->offset($offset)->limit($limit)->get();
    }


    public function getList(PageInput $pageInput, $columns = ['*'])
    {
        return Topic::query()->paginate($pageInput->limit, $columns, 'page', $pageInput->page);
    }

    public function getDetail($id)
    {
        $topic = $goods = [];
        /** @var Topic $topic */
        $topic = Topic::query()->whereId($id)->first();
        if (empty($topic)) {
            return array($topic, $goods);
        }
        $goodIds = json_decode($topic->goods, true);
        if (!empty($goodIds)) {
            $goods = GoodsServices::getInstance()->getGoodsListByIds($goodIds)->toArray();
        }
        return array($topic, $goods);
    }

    /**
     * @param $id
     * @return Topic[]|array|BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    public function getRelated($id)
    {
        $topic = Topic::query()->whereId($id)->first();
        return Topic::query()->when(!empty($topic), function (Builder $builder) use ($topic) {
            return $builder->whereNotIn('id', array($topic->id));
        })->offset(0)->limit(4)->orderBy('add_time', 'desc')->get();
    }
}
