<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tasks')->insert([
            [
                'title' => 'Setup Laravel API',
                'descruiption' => 'Install and configure Laravel project',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Create React Frontend',
                'descruiption' => 'Initialize React app',
                'is_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Design Database',
                'descruiption' => 'Create tables and relations',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Build API Routes',
                'descruiption' => 'Define CRUD endpoints',
                'is_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Add Authentication',
                'descruiption' => 'Install Sanctum',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Create Task Controller',
                'descruiption' => 'Resource controller for tasks',
                'is_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Validation Rules',
                'descruiption' => 'Add request validation',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'API Testing',
                'descruiption' => 'Test using Postman',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Deploy Project',
                'descruiption' => 'Upload to server',
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Write Documentation',
                'descruiption' => 'Prepare API docs',
                'is_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
