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
        'created_at',
        'updated_at'
    ];

    public function scopeLastStamp($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->orderBy('stamped_at', 'desc')
            ->first();
    }
}
