<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroEngagement extends Model
{
    use HasFactory;

    protected $table = 'cro_engagements';

    protected $fillable = [
        'client_id',
        'client_name',
        'pic_name',
        'pic_contact',
        'engagement_type',
        'title',
        'discussion_summary',
        'action_items',
        'sentiment',
        'engagement_date',
        'location',
        'mom_file_path',
        'created_by',
    ];

    protected $casts = [
        'engagement_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSentimentBadgeAttribute()
    {
        return match($this->sentiment) {
            'Positive'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Positif'],
            'Neutral'   => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Netral'],
            'Concerned' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Ada Perhatian'],
            'Negative'  => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Kritis/Negatif'],
            default     => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $this->sentiment ?? '-'],
        };
    }
}
