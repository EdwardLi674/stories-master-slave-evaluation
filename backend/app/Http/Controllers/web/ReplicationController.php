<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ReplicateTablesJob;

class ReplicationController extends Controller
{
    public function index()
    {
        // Get default database name (mysql_master)
        $database = DB::getDatabaseName();

        // Tables to exclude (Laravel default)
        $excluded = [
            'migrations',
            'password_reset_tokens',
            'failed_jobs',
            'personal_access_tokens',
            'users', 
            'sessions',
            'jobs',
            'cache',
            'password_resets',
            'cache_locks',
            'job_batches',
        ];

        // Get list of tables
        $tables = DB::table('information_schema.tables')
                    ->where('table_schema', $database)
                    ->whereNotIn('TABLE_NAME', $excluded)
                    ->pluck('TABLE_NAME');
                    
        $active = 'replication';
        $data = compact('active', 'tables');  
        return view('replication.index', $data);
    }

    public function startReplication(Request $request)
    {
        $tables = $request->tables;
        $total = count($tables);

        // Initialize progress
        Cache::put('replication_progress', 0);

        $completed = [];
        foreach ($tables as $index => $table) {

            // Update current table in cache
            Cache::put('current_table', $table);   

            // Check if table exists in master
            if (!Schema::connection('mysql_master')->hasTable($table)) continue;

            // Get CREATE TABLE statement
            $createTableResult = DB::connection('mysql_master')
                ->select("SHOW CREATE TABLE `$table`");

            if (empty($createTableResult)) continue;

            // Extract CREATE TABLE SQL
            $create = $createTableResult[0]->{'Create Table'};

            // Drop table if exists in slave and recreate
            if (Schema::connection('mysql_slave')->hasTable($table)) {
                DB::connection('mysql_slave')->statement("DROP TABLE `$table`");
            }

            DB::connection('mysql_slave')->statement($create);

            // Copy data from master to slave
            $rows = DB::connection('mysql_master')->table($table)->get();
            // Insert data into slave
            foreach ($rows as $row) {
                DB::connection('mysql_slave')->table($table)->insert((array)$row);
            }

            // Add to completed list
            $completed[] = $table;
            Cache::put('completed_tables', $completed);
            
            // Calculate progress
            $progress = intval((($index + 1)/$total) * 100);
            Cache::put('replication_progress', $progress);

        }
        // Finalize progress
        Cache::put('replication_progress', 100);
    }

    // Get current progress
    public function getProgress()
    {
        // Retrieve progress from cache
        $progress = Cache::get('replication_progress', 0);

        // Return as JSON response
        return response()->json([
            'progress' => $progress,
            'current_table' => Cache::get('current_table', ''),
            'completed_tables' => Cache::get('completed_tables', [])
        ]);
    }
}