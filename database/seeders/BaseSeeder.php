<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class BaseSeeder extends Seeder
{
    /**
     * Safely truncate table with foreign key handling
     */
    protected function safeTruncate($modelClass)
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            // Disable foreign key checks for MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Truncate the table
            $modelClass::truncate();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            // For SQLite, disable foreign key constraints
            DB::statement('PRAGMA foreign_keys=OFF;');
            
            // Truncate the table
            $modelClass::truncate();
            
            // Re-enable foreign key constraints
            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            // For other databases, just truncate
            $modelClass::truncate();
        }
    }
    
    /**
     * Execute with foreign key checks disabled
     */
    protected function withoutForeignKeyChecks(callable $callback)
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $callback();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
            
            $callback();
            
            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            $callback();
        }
    }
}
