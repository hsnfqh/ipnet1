<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'status',
        'avatar',
        'last_login_at',
        'certification',
        'certification_file',
        'certification_status',
        'certification_uploaded_at',
        'division_id',
        'team_id',
        'level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'certification_uploaded_at' => 'datetime',
    ];

    // Relationships
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function ledTeams()
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'engineer_id');
    }

    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'engineer_id');
    }

    public function schedulesCreated()
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeEngineers($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->whereIn('name', [
                'Team Leader',
                'Lead Engineer',
                'Engineer',
                'Engineer L1',
                'Engineer L2',
            ]);
        });
    }

    public function scopeLeadEngineer($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->whereIn('name', ['Lead Engineer', 'Direktur', 'Lead Divisi', 'Team Leader']);
        });
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists('avatars/' . $this->avatar)) {
            return Storage::url('avatars/' . $this->avatar);
        }
        
        $initials = strtoupper(collect(explode(' ', $this->name))
            ->map(fn($word) => substr($word, 0, 1))
            ->take(2)
            ->join(''));

        return "https://ui-avatars.com/api/?name={$initials}&background=7A0D18&color=ffffff&size=64";
    }

    public function getInitialsAttribute()
    {
        return strtoupper(collect(explode(' ', $this->name))
            ->map(fn($word) => substr($word, 0, 1))
            ->take(2)
            ->join(''));
    }

    public function getRoleLabelAttribute()
    {
        return $this->roles->first()?->name ?? 'User';
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'Active';
    }
}