<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '管理者テスト',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ];
        DB::table('admin_users')->insert($param);
    }
}
