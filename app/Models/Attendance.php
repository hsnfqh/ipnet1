<?php
// app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'latitude',
        'longitude',
        'distance_meters',
        'is_within_range',
        'photo_path',
        'address',
        'note',
        'attendance_date',
    ];

    protected $casts = [
        'is_within_range'  => 'boolean',
        'attendance_date'  => 'date:Y-m-d',
        'latitude'         => 'float',
        'longitude'        => 'float',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = [
        'status_label',
        'photo_url',
    ];

    // Accessor: label status tampilan
    public function getStatusLabelAttribute(): string
    {
        if ($this->type === 'clock_in') {
            return $this->is_within_range ? 'Hadir' : 'Luar Jangkauan';
        }
        return 'Clock Out';
    }

    // Accessor: URL foto
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return '/storage/' . ltrim($this->photo_path, '/');
    }

    // Hitung jarak Haversine (meter) dari dua koordinat
    public static function haversineDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): int {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }
}
