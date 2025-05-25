<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corrections')->insert([
            'correction_request_id' => 1,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-24 10:00:00',
            'created_at' => '2025-05-24 09:00:00',
            'updated_at' => '2025-05-24 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 1,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-24 19:00:00',
            'created_at' => '2025-05-24 18:00:00',
            'updated_at' => '2025-05-24 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 1,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-24 11:50:00',
            'created_at' => '2025-05-24 12:00:00',
            'updated_at' => '2025-05-24 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 1,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-24 12:50:00',
            'created_at' => '2025-05-24 13:00:00',
            'updated_at' => '2025-05-24 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 2,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-25 10:00:00',
            'created_at' => '2025-05-25 09:00:00',
            'updated_at' => '2025-05-25 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 2,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-25 19:00:00',
            'created_at' => '2025-05-25 18:00:00',
            'updated_at' => '2025-05-25 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 2,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-25 11:50:00',
            'created_at' => '2025-05-25 12:00:00',
            'updated_at' => '2025-05-25 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 2,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-25 12:50:00',
            'created_at' => '2025-05-25 13:00:00',
            'updated_at' => '2025-05-25 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 3,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-26 10:00:00',
            'created_at' => '2025-05-26 09:00:00',
            'updated_at' => '2025-05-26 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 3,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-26 19:00:00',
            'created_at' => '2025-05-26 18:00:00',
            'updated_at' => '2025-05-26 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 3,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-26 11:50:00',
            'created_at' => '2025-05-26 12:00:00',
            'updated_at' => '2025-05-26 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 3,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-26 12:50:00',
            'created_at' => '2025-05-26 13:00:00',
            'updated_at' => '2025-05-26 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 4,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-27 10:00:00',
            'created_at' => '2025-05-27 09:00:00',
            'updated_at' => '2025-05-27 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 4,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-27 19:00:00',
            'created_at' => '2025-05-27 18:00:00',
            'updated_at' => '2025-05-27 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 4,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-27 11:50:00',
            'created_at' => '2025-05-27 12:00:00',
            'updated_at' => '2025-05-27 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 4,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-27 12:50:00',
            'created_at' => '2025-05-27 13:00:00',
            'updated_at' => '2025-05-27 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 5,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-28 10:00:00',
            'created_at' => '2025-05-28 09:00:00',
            'updated_at' => '2025-05-28 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 5,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-28 19:00:00',
            'created_at' => '2025-05-28 18:00:00',
            'updated_at' => '2025-05-28 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 5,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-28 11:50:00',
            'created_at' => '2025-05-28 12:00:00',
            'updated_at' => '2025-05-28 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 5,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-28 12:50:00',
            'created_at' => '2025-05-28 13:00:00',
            'updated_at' => '2025-05-28 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 6,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-29 10:00:00',
            'created_at' => '2025-05-29 09:00:00',
            'updated_at' => '2025-05-29 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 6,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-29 19:00:00',
            'created_at' => '2025-05-29 18:00:00',
            'updated_at' => '2025-05-29 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 6,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-29 11:50:00',
            'created_at' => '2025-05-29 12:00:00',
            'updated_at' => '2025-05-29 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 6,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-29 12:50:00',
            'created_at' => '2025-05-29 13:00:00',
            'updated_at' => '2025-05-29 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 7,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-30 10:00:00',
            'created_at' => '2025-05-30 09:00:00',
            'updated_at' => '2025-05-30 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 7,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-30 19:00:00',
            'created_at' => '2025-05-30 18:00:00',
            'updated_at' => '2025-05-30 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 7,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-30 11:50:00',
            'created_at' => '2025-05-30 12:00:00',
            'updated_at' => '2025-05-30 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 7,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-30 12:50:00',
            'created_at' => '2025-05-30 13:00:00',
            'updated_at' => '2025-05-30 13:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 8,
            'stamp_type' => '出勤',
            'corrected_at' => '2025-05-31 10:00:00',
            'created_at' => '2025-05-31 09:00:00',
            'updated_at' => '2025-05-31 09:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 8,
            'stamp_type' => '退勤',
            'corrected_at' => '2025-05-31 19:00:00',
            'created_at' => '2025-05-31 18:00:00',
            'updated_at' => '2025-05-31 18:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 8,
            'stamp_type' => '休憩入',
            'corrected_at' => '2025-05-31 11:50:00',
            'created_at' => '2025-05-31 12:00:00',
            'updated_at' => '2025-05-31 12:00:00',
        ]);

        DB::table('corrections')->insert([
            'correction_request_id' => 8,
            'stamp_type' => '休憩戻',
            'corrected_at' => '2025-05-31 12:50:00',
            'created_at' => '2025-05-31 13:00:00',
            'updated_at' => '2025-05-31 13:00:00',
        ]);
    }
}
