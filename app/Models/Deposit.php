<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Constants\ManageStatus;
use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Payment record for a {@see Campaign} (and optional {@see Reward}), linked to a {@see User} when not guest.
 *
 * **Mass assignment:** see `$fillable` — amounts, gateway codes, `trx`, donor contact fields, `status`, `reward_id`.
 * **Relationships:** `user`, `gateway`, `campaign`, `reward`. Use {@see Deposit::gatewayCurrency()} for display resolution.
 */
class Deposit extends Model
{
    use UniversalStatus, Searchable;

    protected $fillable = [
        'user_id',
        'method_code',
        'method_currency',
        'amount',
        'charge',
        'rate',
        'final_amount',
        'btc_amo',
        'btc_wallet',
        'trx',
        'status',
        'campaign_id',
        'reward_id',
        'deposit_type',
        'email',
        'phone',
        'name',
        'full_name',
        'country',
        'receiver_id',
    ];

    protected $casts = [
        'details' => 'object',
    ];

    protected $hidden = ['details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    /**
     * Get the campaign that owns the deposit.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    /**
     * Get the reward that was selected for this deposit.
     */
    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id', 'id');
    }

    /**
     * Gateway currency row for this deposit. Exact match on method_currency first;
     * if missing (e.g. local display USD but gateway only has PKR), first active row for method_code.
     */
    public function gatewayCurrency(): ?GatewayCurrency
    {
        $methodCode = $this->method_code;
        $currency   = strtoupper(trim((string) $this->method_currency));

        $base = GatewayCurrency::query()
            ->where('method_code', $methodCode)
            ->where('status', ManageStatus::ACTIVE);

        $exact = (clone $base)->whereRaw('UPPER(TRIM(currency)) = ?', [$currency])->first();
        if ($exact) {
            return $exact;
        }

        return $base->orderBy('id')->first();
    }

    public function scopeBaseCurrency()
    {
        return @$this->gateway->crypto == ManageStatus::ACTIVE ? 'USD' : $this->method_currency;
    }

    public function scopePending($query)
    {
        return $query->where('method_code', '>=', 1000)->where('status', ManageStatus::PAYMENT_PENDING);
    }

    public function scopeCancelled($query)
    {
        return $query->where('method_code', '>=', 1000)->where('status', ManageStatus::PAYMENT_CANCEL);
    }

    public function scopeDone($query)
    {
        return $query->where('status', ManageStatus::PAYMENT_SUCCESS);
    }

    public function scopeIndex($query)
    {
        return $query->where('status', '!=', ManageStatus::PAYMENT_INITIATE);
    }

    /**
     * Admin "all donations" list: include PAYMENT_INITIATE rows (user expects status 0 visible).
     */
    public function scopeAdminIndex($query)
    {
        return $query;
    }

    /**
     * Admin pending queue: gateway PENDING (any method) + manual donations still at INITIATE (awaiting proof).
     * Matches summary logic in Admin\DepositController::donationData.
     */
    public function scopeAdminPending($query)
    {
        return $query->where(function ($q) {
            $q->where('status', ManageStatus::PAYMENT_PENDING)
                ->orWhere(function ($q2) {
                    $q2->where('status', ManageStatus::PAYMENT_INITIATE)
                        ->where('method_code', '>=', 1000);
                });
        });
    }

    public function scopeInitiate($query)
    {
        return $query->where('status', ManageStatus::PAYMENT_INITIATE);
    }

    /**
     * Get the donor's full name.
     */
    protected function donorName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->donor_type !== null && (int) $this->donor_type === ManageStatus::ANONYMOUS_DONOR) {
                    return __('Anonymous');
                }

                return $this->user_id ? $this->user->fullname : $this->full_name;
            },
        );
    }

    /**
     * Get the donor's email.
     */
    protected function donorEmail(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->donor_type !== null && (int) $this->donor_type === ManageStatus::ANONYMOUS_DONOR) {
                    return '-';
                }

                return $this->user_id ? $this->user->email : $this->email;
            },
        );
    }

    /**
     * Get the donor's phone.
     */
    protected function donorPhone(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->donor_type !== null && (int) $this->donor_type === ManageStatus::ANONYMOUS_DONOR) {
                    return '-';
                }

                return $this->user_id ? $this->user->mobile : $this->phone;
            },
        );
    }

    /**
     * Get the donor's country.
     */
    protected function donorCountry(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->donor_type !== null && (int) $this->donor_type === ManageStatus::ANONYMOUS_DONOR) {
                    return '-';
                }

                return $this->user_id ? $this->user->country_name : $this->country;
            },
        );
    }

    public function isManualGateway(): bool
    {
        return (int) $this->method_code >= 1000;
    }

    /**
     * API / UI: 1 = proof on file or automated gateway; 0 = manual flow still needs proof upload.
     */
    public function proofSubmittedFlag(): int
    {
        if (! $this->isManualGateway()) {
            return 1;
        }
        if ((int) $this->status === ManageStatus::PAYMENT_INITIATE) {
            return depositHasPaymentProofUpload($this) ? 1 : 0;
        }

        return 1;
    }

    public function needsProofUpload(): bool
    {
        return $this->isManualGateway()
            && (int) $this->status === ManageStatus::PAYMENT_INITIATE
            && ! depositHasPaymentProofUpload($this);
    }
}
