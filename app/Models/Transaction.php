<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deposit associated with this transaction.
     */
    public function deposit()
    {
        return $this->hasOne(Deposit::class, 'trx', 'trx');
    }

    /**
     * Get the reward associated with this transaction.
     */
    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id', 'id');
    }
}
