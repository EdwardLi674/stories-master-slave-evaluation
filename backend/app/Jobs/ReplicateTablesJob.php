<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReplicateTablesJob implements ShouldQueue
{
    use Queueable;

    protected $tables;

    public function __construct(array $tables)
    {
        $this->tables = $tables;
    }

    public function handle()
    {
        $total = count($this->tables);
        Cache::put('replication_progress', 0);

        foreach ($this->tables as $index => $table) {
            Cache::put('current_table', $table);

            if (!Schema::connection('mysql_master')->hasTable($table)) continue;

            $createTableResult = DB::connection('mysql_master')
                ->select("SHOW CREATE TABLE `$table`");

            if (empty($createTableResult)) continue;

            $create = $createTableResult[0]->{'Create Table'};

            if (Schema::connection('mysql_slave')->hasTable($table)) {
                DB::connection('mysql_slave')->statement("DROP TABLE `$table`");
            }

            DB::connection('mysql_slave')->statement($create);

            $rows = DB::connection('mysql_master')->table($table)->get();
            foreach ($rows as $row) {
                DB::connection('mysql_slave')->table($table)->insert((array)$row);
            }

            $progress = intval((($index + 1)/$total) * 100);
            Cache::put('replication_progress', $progress);
        }

        Cache::put('replication_progress', 100);
    }
}