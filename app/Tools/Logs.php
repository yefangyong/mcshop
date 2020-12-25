<?php


namespace App\Tools;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Logs
{
    const LOG_KEY = 'mcshop';

    public function __construct()
    {

    }

    public static function get_log_hash()
    {
        return substr(md5(time() . self::LOG_KEY . rand(1, 10000)), 8, 16);
    }

    public static function save($message, $data = [], $filename = 'mcshop', $isDate = false)
    {
        global $log_hash;

        if (strlen($log_hash) == 0) {
            $log_hash = self::get_log_hash();
        }

        $log = new Logger('mcshop log');

        //通过命令行执行时区分一下
        if (PHP_SAPI == 'cli') {
            $filename .= '_cli';
        }

        $filename .= '.log';

        if ($isDate) {
            $path = storage_path('logs/' . date('Ym'));
        } else {
            $path = storage_path('logs/');
        }


        self::mkDirs($path);
        $path = $path . $filename;

        $message = $log_hash . ' ' . $message;

        if (!is_array($data)) {
            $message .= ':' . $data;
            $data    = [];
        }

        $log->pushHandler(new StreamHandler($path, Logger::INFO));
        $log->info($message, $data);
    }

    public static function info($message, $data = [], $filename = 'mcshop', $isDate = false)
    {
        self::save($message, $data, $filename, $isDate);
    }

    /**
     * 临时记录sql日志
     */
    public static function sql()
    {
        DB::listen(function (QueryExecuted $event) {
            $sql      = $event->sql;
            $bindings = $event->bindings;
            $time     = $event->time;
            $bindings = array_map(function ($binding) {
                if (is_string($binding)) {
                    return "'$binding'";
                } elseif ($binding instanceof \DateTime) {
                    return $binding->format("'Y-m-d H:i:s'");
                }
                return $binding;
            }, $bindings);
            $sql      = str_replace('?', '%s', $sql);
            $sql      = sprintf($sql, ...$bindings);
            Logs::info('sql log', $sql . ' time:' . $time, 'sql', false);
        });
    }

    /**
     * @param $dir
     * @param  int  $mode
     * @return bool
     * 给日志文件夹权限
     */
    public static function mkDirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!self::mkdirs(dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }
}
