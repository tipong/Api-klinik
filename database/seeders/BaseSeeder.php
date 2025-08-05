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
        // Disable foreign key checks for MySQL
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        $modelClass::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    /**
     * Execute with foreign key checks disabled
     */
    protected function withoutForeignKeyChecks(callable $callback)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            $callback();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
