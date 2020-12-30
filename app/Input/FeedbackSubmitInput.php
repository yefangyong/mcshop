<?php


namespace App\Input;


class FeedbackSubmitInput extends Input
{
    public $mobile;
    public $feedType;
    public $content;
    public $status = 1;
    public $hasPicture = 0;
    public $pic_urls = '';


    public function rule()
    {
        return [
            'mobile'   => 'required|regex:/^1[0-9]{10}$/',
            'feedType' => 'required|string',
            'content'  => 'required|string',
        ];
    }
}
