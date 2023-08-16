<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::factory(10)
            ->sequence(
                ['name' => 'Role 1'],
                ['name' => 'Role 2'],
                ['name' => 'Role 3'],
                ['name' => 'Role 4'],
                ['name' => 'Role 5'],
                ['name' => 'Role 6'],
                ['name' => 'Role 7'],
                ['name' => 'Role 8'],
                ['name' => 'Role 9'],
                ['name' => 'Role 10'],
            )->create();
    }
}
