<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = User::create([
            'first_name' => 'root',
            'last_name' => 'root',
            'email_verified_at' => null,
            'email' => 'root@example.com',
            'password' => Hash::make('password'),
        ]);

        $root->assignRole('root');
    }
}
