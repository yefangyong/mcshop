<?php


namespace App\Services;


use App\Models\Topic;

class TopicServices extends BaseServices
{
    public function getTopicByLimit($limit, $offset = 0, $order = 'desc', $sort = 'add_time')
    {
        return Topic::query()->orderBy($sort, $order)->offset($offset)->limit($limit)->get();
    }
}
