<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'approved',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function corrections()
    {
        return $this->hasMany(Correction::class);
    }
}
