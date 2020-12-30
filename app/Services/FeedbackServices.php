<?php


namespace App\Services;


use App\CodeResponse;
use App\Input\FeedbackSubmitInput;
use App\Models\Feedback;
use App\Services\User\UserServices;
use Illuminate\Support\Facades\Date;

class FeedbackServices extends BaseServices
{
    public function add(FeedbackSubmitInput $feedbackSubmitInput, $userId)
    {
        $user                = UserServices::getInstance()->getUserById($userId);
        $feedback            = Feedback::new();
        $feedback->status    = $feedbackSubmitInput->status;
        $feedback->content   = $feedbackSubmitInput->content;
        $feedback->mobile    = $feedbackSubmitInput->mobile;
        $feedback->add_time  = Date::now();
        $feedback->user_id   = $userId;
        $feedback->username  = $user->username;
        $feedback->feed_type = $feedbackSubmitInput->feedType;
        if (!$feedback->save()) {
            $this->throwBusinessException(CodeResponse::UPDATED_FAIL);
        }
        return true;
    }
}
