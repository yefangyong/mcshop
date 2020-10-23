<?php


namespace App\Services;


use App\Models\SearchHistory;
use Carbon\Carbon;

class SearchHistoryServices extends BaseServices
{
    public function save($userId, $keyword, $from)
    {
        $history              = new SearchHistory();
        $history->user_id      = $userId;
        $history->keyword     = $keyword;
        $history->from        = $from;
        $history->add_time    = Carbon::now()->toDateTimeString();
        $history->update_time = Carbon::now()->toDateTimeString();
        return $history->save();
    }
}
