<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '山田 太郎',
            'email' => 'yamada@test.com',
            'password' => Hash::make('password'),
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '西 伶奈',
            'email' => 'nishi@test.com',
            'password' => Hash::make('password'),
        ];
        DB::table('users')->insert($param);
    }
}
