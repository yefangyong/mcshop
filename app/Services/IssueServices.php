<?php


namespace App\Services;


use App\Input\PageInput;
use App\Models\Issue;

class IssueServices extends BaseServices
{
    public function getList(PageInput $page, $columns = ['*'])
    {
        return Issue::query()->paginate($page->limit, $columns, 'page', $page->page);
    }
}
