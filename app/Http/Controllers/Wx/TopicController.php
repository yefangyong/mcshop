<?php

namespace App\Http\Controllers\Wx;


use App\Input\PageInput;
use App\Services\TopicServices;

class TopicController extends WxController
{
    public function getList()
    {
        $page      = PageInput::new();
        $topicList = TopicServices::getInstance()->getList($page);
        return $this->successPaginate($topicList);
    }

    public function getDetail()
    {
        $id = $this->verifyInteger('id');
        list($topic, $goods) = TopicServices::getInstance()->getDetail($id);
        return $this->success(compact('topic', 'goods'));
    }

    public function getRelated()
    {
        $id = $this->verifyInteger('id');
        return $this->success(TopicServices::getInstance()->getRelated($id));
    }
}
