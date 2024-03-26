<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run()
    {
        DB::table('accounts')->insert([
            'id' => 1,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
            'role' => Role::ROLE['role_admin'],
            'created_at' => now(),
        ]);
    }
}
