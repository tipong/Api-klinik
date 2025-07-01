<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to handle this differently since SQLite doesn't support ALTER COLUMN
        $connection = Schema::getConnection();
        
        if ($connection->getDriverName() === 'sqlite') {
            // For SQLite, we'll add the HRD role using a more careful approach
            // First, disable foreign key checks temporarily
            \DB::statement('PRAGMA foreign_keys=OFF');
            
            // Create temporary table with new schema
            \DB::statement("
                CREATE TABLE tb_user_temp (
                    id_user INTEGER PRIMARY KEY AUTOINCREMENT,
                    nama_user VARCHAR(255) NOT NULL,
                    no_telp VARCHAR(255) NOT NULL UNIQUE,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    tanggal_lahir DATE,
                    password VARCHAR(255) NOT NULL,
                    foto_profil VARCHAR(255),
                    role VARCHAR(50) NOT NULL DEFAULT 'pelanggan' CHECK (role IN ('pelanggan', 'dokter', 'beautician', 'front office', 'kasir', 'admin', 'hrd')),
                    remember_token VARCHAR(100),
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ");
            
            // Copy all data from original table
            \DB::statement("
                INSERT INTO tb_user_temp (id_user, nama_user, no_telp, email, tanggal_lahir, password, foto_profil, role, remember_token, created_at, updated_at)
                SELECT id_user, nama_user, no_telp, email, tanggal_lahir, password, foto_profil, role, remember_token, created_at, updated_at
                FROM tb_user
            ");
            
            // Drop original table and rename temp table
            \DB::statement('DROP TABLE tb_user');
            \DB::statement('ALTER TABLE tb_user_temp RENAME TO tb_user');
            
            // Re-enable foreign key checks
            \DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // For MySQL/PostgreSQL, use ALTER TABLE
            \DB::statement("ALTER TABLE tb_user MODIFY COLUMN role ENUM('pelanggan', 'dokter', 'beautician', 'front office', 'kasir', 'admin', 'hrd') NOT NULL DEFAULT 'pelanggan'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection();
        
        if ($connection->getDriverName() === 'sqlite') {
            // For SQLite rollback
            \DB::statement('PRAGMA foreign_keys=OFF');
            
            \DB::statement("
                CREATE TABLE tb_user_temp (
                    id_user INTEGER PRIMARY KEY AUTOINCREMENT,
                    nama_user VARCHAR(255) NOT NULL,
                    no_telp VARCHAR(255) NOT NULL UNIQUE,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    tanggal_lahir DATE,
                    password VARCHAR(255) NOT NULL,
                    foto_profil VARCHAR(255),
                    role VARCHAR(50) NOT NULL DEFAULT 'pelanggan' CHECK (role IN ('pelanggan', 'dokter', 'beautician', 'front office', 'kasir', 'admin')),
                    remember_token VARCHAR(100),
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ");
            
            // Copy data excluding HRD users
            \DB::statement("
                INSERT INTO tb_user_temp (id_user, nama_user, no_telp, email, tanggal_lahir, password, foto_profil, role, remember_token, created_at, updated_at)
                SELECT id_user, nama_user, no_telp, email, tanggal_lahir, password, foto_profil, role, remember_token, created_at, updated_at
                FROM tb_user
                WHERE role != 'hrd'
            ");
            
            \DB::statement('DROP TABLE tb_user');
            \DB::statement('ALTER TABLE tb_user_temp RENAME TO tb_user');
            \DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // For MySQL rollback
            \DB::statement("ALTER TABLE tb_user MODIFY COLUMN role ENUM('pelanggan', 'dokter', 'beautician', 'front office', 'kasir', 'admin') NOT NULL DEFAULT 'pelanggan'");
        }
    }
};
