<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stamped_at',
        'stamp_type',
    ];

    protected $dates = [
        'stamped_at',
    ];

    public function scopeTodayLastStamp($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereDate('stamped_at', now())
            ->orderBy('stamped_at', 'desc')
            ->first();
    }
}
