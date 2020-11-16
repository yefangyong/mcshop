<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Exception;
use Throwable;

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
     * @throws Throwable
     * 乐观锁的实现，修改数据之前先比较一下 compare and save
     */
    public function cas()
    {
        //当数据不存在时，禁止更新操作
        throw_if(!$this->exists, Exception::class, 'the data is not exist');

        //当内存中更新数据为空时，禁止更新操作
        $dirty = $this->getDirty(); //内存中修改的值
        if (empty($dirty)) {
            return 0;
        }

        //当模型开启自动更新时间字段时，附上更新的时间字段
        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
            $dirty = $this->getDirty();
        }

        $diff = array_diff(array_keys($dirty), array_keys($this->getOriginal()));

        if ($this->fireModelEvent('casing') === false) {
            return 0;
        }

        throw_if(!empty($diff), Exception::class, 'key [ '.implode(',', $diff).' ] is not exist');

        //使用newModelQuery 更新的时候不用带上 deleted = 0 的条件
        $query = self::newModelQuery()->where($this->getKeyName(), $this->getKey());

        foreach ($dirty as $k => $v) {
            $query = $query->where($k, $this->getOriginal($k));  //判断一下更新的字段值是否有改动
        }

        $row = $query->update($dirty);
        if ($row > 0) {
            $this->syncChanges();
            $this->fireModelEvent('cased', false);
            $this->syncOriginal();
        }
        return $row;
    }

    /**
     * @param $callback
     */
    public static function casing($callback)
    {
        static::registerModelEvent('casing', $callback);
    }

    /**
     * @param $callback
     */
    public static function cased($callback)
    {
        static::registerModelEvent('cased', $callback);
    }
}
