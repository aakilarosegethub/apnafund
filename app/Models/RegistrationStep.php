<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'step_order',
        'is_active',
        'is_required',
        'validation_rules'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'is_required' => 'boolean'
    ];

    /**
     * Get the questions for this step.
     */
    public function questions()
    {
        return $this->hasMany(RegistrationQuestion::class, 'step_id')->orderBy('order');
    }

    /**
     * Get active questions for this step.
     */
    public function activeQuestions()
    {
        return $this->hasMany(RegistrationQuestion::class, 'step_id')
                    ->where('is_active', true)
                    ->orderBy('order');
    }

    /**
     * Scope to get active steps.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by step order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }
}
