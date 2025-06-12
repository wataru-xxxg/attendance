<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StampSeeder extends Seeder
{
    public function run()
    {
        $baseDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();

        // 1か月分のデータを作成
        while ($baseDate <= $endDate) {
            // 土日を除外
            if (!$baseDate->isWeekend()) {
                $this->createDailyStamps($baseDate->copy(), 1);
                $this->createDailyStamps($baseDate->copy(), 2);
            }
            $baseDate->addDay();
        }
    }

    private function createDailyStamps(Carbon $date, int $userId)
    {
        // 出勤
        DB::table('stamps')->insert([
            'user_id' => $userId,
            'stamp_type' => '出勤',
            'stamped_at' => $date->copy()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'created_at' => $date->copy()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'updated_at' => $date->copy()->setTime(9, 0)->format('Y-m-d H:i:s'),
        ]);

        // 休憩入
        DB::table('stamps')->insert([
            'user_id' => $userId,
            'stamp_type' => '休憩入',
            'stamped_at' => $date->copy()->setTime(12, 0)->format('Y-m-d H:i:s'),
            'created_at' => $date->copy()->setTime(12, 0)->format('Y-m-d H:i:s'),
            'updated_at' => $date->copy()->setTime(12, 0)->format('Y-m-d H:i:s'),
        ]);

        // 休憩戻
        DB::table('stamps')->insert([
            'user_id' => $userId,
            'stamp_type' => '休憩戻',
            'stamped_at' => $date->copy()->setTime(13, 0)->format('Y-m-d H:i:s'),
            'created_at' => $date->copy()->setTime(13, 0)->format('Y-m-d H:i:s'),
            'updated_at' => $date->copy()->setTime(13, 0)->format('Y-m-d H:i:s'),
        ]);

        // 退勤
        DB::table('stamps')->insert([
            'user_id' => $userId,
            'stamp_type' => '退勤',
            'stamped_at' => $date->copy()->setTime(18, 0)->format('Y-m-d H:i:s'),
            'created_at' => $date->copy()->setTime(18, 0)->format('Y-m-d H:i:s'),
            'updated_at' => $date->copy()->setTime(18, 0)->format('Y-m-d H:i:s'),
        ]);
    }
}
