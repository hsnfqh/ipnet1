<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroCsatSurvey extends Model
{
    use HasFactory;

    protected $table = 'cro_csat_surveys';

    protected $fillable = [
        'client_id',
        'client_name',
        'project_id',
        'service_category',
        'respondent_name',
        'respondent_role',
        'csat_score',
        'nps_score',
        'sla_satisfaction_score',
        'support_speed_score',
        'feedback_notes',
        'areas_of_improvement',
        'sentiment',
        'survey_date',
        'created_by',
    ];

    protected $casts = [
        'survey_date' => 'date',
        'csat_score' => 'integer',
        'nps_score' => 'integer',
        'sla_satisfaction_score' => 'integer',
        'support_speed_score' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getScoreColorAttribute()
    {
        if ($this->csat_score >= 4) return 'text-emerald-600';
        if ($this->csat_score == 3) return 'text-amber-500';
        return 'text-rose-600';
    }
}
