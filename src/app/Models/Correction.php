<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_id',
        'stamp_type',
        'corrected_time',
    ];

    public function correctionRequest()
    {
        return $this->belongsTo(CorrectionRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
