<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(NewPermissionSeeder::class);
        // $this->call(settingSeeder::class);

        // \App\Models\User::factory(10)->create();
        /*
                \App\Models\User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'admin@admin.com',
                    'password' => bcrypt('password')
                ]);
                */
    }
}
