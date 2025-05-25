<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-24',
            'approved' => 0,
            'notes' => 'test',
            'created_at' => '2025-05-24 09:00:00',
            'updated_at' => '2025-05-24 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-25',
            'approved' => 0,
            'notes' => 'test2',
            'created_at' => '2025-05-25 09:00:00',
            'updated_at' => '2025-05-25 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-26',
            'approved' => 0,
            'notes' => 'test3',
            'created_at' => '2025-05-26 09:00:00',
            'updated_at' => '2025-05-26 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-27',
            'approved' => 0,
            'notes' => 'test4',
            'created_at' => '2025-05-27 09:00:00',
            'updated_at' => '2025-05-27 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-28',
            'approved' => 1,
            'notes' => 'test5',
            'created_at' => '2025-05-28 09:00:00',
            'updated_at' => '2025-05-28 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-29',
            'approved' => 1,
            'notes' => 'test6',
            'created_at' => '2025-05-29 09:00:00',
            'updated_at' => '2025-05-29 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-30',
            'approved' => 1,
            'notes' => 'test7',
            'created_at' => '2025-05-30 09:00:00',
            'updated_at' => '2025-05-30 09:00:00',
        ]);

        DB::table('correction_requests')->insert([
            'user_id' => 1,
            'date' => '2025-05-31',
            'approved' => 1,
            'notes' => 'test8',
            'created_at' => '2025-05-31 09:00:00',
            'updated_at' => '2025-05-31 09:00:00',
        ]);
    }
}
