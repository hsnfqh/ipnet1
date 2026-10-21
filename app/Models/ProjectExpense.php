<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'category',
        'amount',
        'description',
        'expense_date',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date:Y-m-d',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->amount ?? 0), 0, ',', '.');
    }
}
