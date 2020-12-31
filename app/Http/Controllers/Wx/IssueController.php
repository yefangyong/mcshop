<?php

namespace App\Http\Controllers\Wx;

use App\Input\PageInput;
use App\Services\IssueServices;


class IssueController extends WxController
{
    protected $only = [];

    public function getList()
    {
        $page   = PageInput::new();
        $issues = IssueServices::getInstance()->getList($page);
        return $this->successPaginate($issues, $issues->items());
    }
}
