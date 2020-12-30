<?php

namespace App\Http\Controllers\Wx;

use App\Input\FeedbackSubmitInput;
use App\Services\FeedbackServices;


class FeedbackController extends WxController
{
    protected $only = [];

    public function submit()
    {
        $params = FeedbackSubmitInput::new();
        $userId = $this->userId();
        FeedbackServices::getInstance()->add($params, $userId);
        return $this->success();
    }
}
