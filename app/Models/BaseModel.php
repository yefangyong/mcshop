<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    use BooleanSoftDeletes;

    public static $instance = null;

    const CREATED_AT = 'add_time';

    public $defaultCasts = ['deleted' => 'boolean'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        parent::mergeCasts($this->defaultCasts);
    }

    public static function new()
    {
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
        return Carbon::instance($date)->toDateTimeString();
    }

    /**
     * @return bool|int
     * 乐观锁的实现，修改数据之前先比较一下 compare and save
     */
    public function cas()
    {
        $dirty    = $this->getDirty(); //内存中修改的值
        $updateAt = $this->getUpdatedAtColumn(); //更新数据之前，判断一下更新时间有没有改变
        $query    = self::query()->where($this->getKeyName(), $this->getKey())->where($updateAt, $this->{$updateAt});

        foreach ($dirty as $k => $v) {
            $query = $query->where($k, $this->getOriginal($k));  //判断一下更新的字段值是否有改动
        }

        return $query->update($dirty);
    }
}
