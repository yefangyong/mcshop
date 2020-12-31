<?php

namespace App\Input;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\ValidateRequest;
use Illuminate\Support\Facades\Validator;

class Input
{

    use ValidateRequest;

    /**
     * @param  null  $data
     * @return Input
     * @throws BusinessException
     */
    public function fill($data = null)
    {
        if (is_null($data)) {
            $data = request()->input();
        }
        $validate = Validator::make($data, $this->rule());
        if ($validate->fails()) {
            throw new BusinessException(CodeResponse::PARAM_NOT_EMPTY, $validate->errors());
        }
        $map  = get_object_vars($this);
        $keys = array_keys($map);
        collect($data)->map(function ($v, $k) use ($keys) {
            if (in_array($k, $keys)) {
                $this->$k = $v;
            }
        });
        return $this;
    }

    public function rule()
    {
        return [];
    }

    /**
     * @param  null  $data
     * @return Input|static
     * @throws BusinessException
     */
    public static function new($data = null)
    {
        return (new static())->fill($data);
    }
}
