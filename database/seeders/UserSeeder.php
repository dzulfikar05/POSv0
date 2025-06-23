<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'level_id' => 1,
                'username' => 'admin',
                'nama' => 'Administrator',
                'password' => Hash::make('password'), // class untuk mengenkripsi/hash password
            ],
            [
                'user_id' => 2,
                'level_id' => 1,
                'username' => 'admin2',
                'nama' => 'Manager',
                'password' => Hash::make('password'),
            ],
            [
                'user_id' => 3,
                'level_id' => 2,
                'username' => 'customer',
                'nama' => 'Customer Random',
                'password' => Hash::make('password'),
            ],
        ];

        DB::table('m_user')->insert($data);
    }
}
