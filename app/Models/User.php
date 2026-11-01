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
        'signature_data',
        'signature_type',
        'signature_qr_token',
        'signature_updated_at',
        'notification_web',
        'notification_email',
        'theme_preference',
        'timezone_preference',
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
        'signature_updated_at' => 'datetime',
        'notification_web' => 'boolean',
        'notification_email' => 'boolean',
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
        if ($this->avatar && \App\Helpers\FileUploadHelper::exists('avatars/' . $this->avatar)) {
            return \App\Helpers\FileUploadHelper::url('avatars/' . $this->avatar);
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
        $roleName = $this->roles->first()?->name;
        if ($roleName === 'PMO') {
            return 'Project Manager';
        }
        return $roleName ?? 'User';
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'Active';
    }

    public function getHasSignatureAttribute()
    {
        return !empty($this->signature_data);
    }

    public function ensureSignatureQrToken()
    {
        if (empty($this->signature_qr_token)) {
            $this->signature_qr_token = strtoupper('SIG-' . substr(hash('sha256', $this->id . '-' . $this->email . '-' . microtime(true)), 0, 16));
            $this->saveQuietly();
        }
        return $this->signature_qr_token;
    }

    public function getSignatureQrSvgAttribute()
    {
        $token = $this->ensureSignatureQrToken();
        $payload = route('public.signature.verify', $token);

        try {
            $svg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(80)
                ->margin(0)
                ->errorCorrection('M')
                ->generate($payload);

            return preg_replace('/<\?xml.*?\?>/i', '', $svg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('User QR SVG error: ' . $e->getMessage());
            return '';
        }
    }

    public function getSignatureQrDataUriAttribute()
    {
        $svg = $this->signature_qr_svg;
        if (empty($svg)) return '';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}