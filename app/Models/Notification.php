<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = \Illuminate\Support\Str::limit((string)$value, 245, '...');
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = \Illuminate\Support\Str::limit((string)$value, 245, '...');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}