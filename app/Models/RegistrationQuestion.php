<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'field_name',
        'label',
        'type',
        'placeholder',
        'help_text',
        'is_required',
        'order',
        'validation_rules',
        'options',
        'is_active'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the step that owns this question.
     */
    public function step()
    {
        return $this->belongsTo(RegistrationStep::class, 'step_id');
    }

    /**
     * Get the user responses for this question.
     */
    public function responses()
    {
        return $this->hasMany(UserRegistrationResponse::class, 'question_id');
    }

    /**
     * Scope to get active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get validation rules for this question.
     */
    public function getValidationRulesAttribute($value)
    {
        $rules = json_decode($value, true) ?? [];
        
        // Add required rule if question is required
        if ($this->is_required) {
            $rules[] = 'required';
        }
        
        // Add type-specific rules
        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'tel':
                $rules[] = 'regex:/^[\+]?[0-9\s\-\(\)]{10,15}$/';
                break;
            case 'password':
                $rules[] = 'min:8';
                $rules[] = 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/';
                break;
        }
        
        return $rules;
    }
}
