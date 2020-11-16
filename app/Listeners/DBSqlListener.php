<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DBSqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (env('APP_DEBUG') === true || app()->environment(['testing', 'local'])) {
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

            if (app()->environment() == 'testing') {
                echo $sql . PHP_EOL;
            } else {
                Log::info('sql log', ['sql' => $sql, 'time' => $time]);
            }

        }
    }
}
