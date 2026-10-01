<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRecommendationFeedback extends Model
{
    protected $fillable = [
        'ai_recommendation_id',
        'guru_id',
        'catatan',
        'status',
    ];

    public function aiRecommendation()
    {
        return $this->belongsTo(AiRecommendation::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
