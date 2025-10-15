<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegistrationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'response_value'
    ];

    /**
     * Get the user that owns this response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the question that this response belongs to.
     */
    public function question()
    {
        return $this->belongsTo(RegistrationQuestion::class, 'question_id');
    }
}
