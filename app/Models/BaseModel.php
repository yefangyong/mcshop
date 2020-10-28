<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    public $timestamps = false;

    public $defaultCasts = ['deleted' => 'boolean'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        parent::mergeCasts($this->defaultCasts);
    }

    public static function new() {
        return new static();
    }

    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

    public function toArray()
    {
        $items  = parent::toArray();
        $items  = array_filter($items, function ($item) {
            return !is_null($item);
        });
        $keys   = array_keys($items);
        $keys   = array_map(function ($item) {
            return lcfirst(Str::studly($item));
        }, $keys);
        $values = array_values($items);
        return array_combine($keys, $values);
    }

    public function serializeDate(DateTimeInterface $date)
    {
        $date = Carbon::instance($date)->toDateTimeString();
        return strtotime($date);
    }
}
