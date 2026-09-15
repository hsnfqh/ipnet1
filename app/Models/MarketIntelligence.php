<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIntelligence extends Model
{
    use HasFactory;

    protected $table = 'market_intelligences';

    protected $fillable = [
        'title',
        'category',
        'industry_sector',
        'summary',
        'potential_value',
        'source_url',
        'impact_level',
        'status',
        'author_id',
    ];

    protected $casts = [
        'potential_value' => 'decimal:2',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
