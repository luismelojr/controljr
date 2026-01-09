<?php

namespace App\Models;

use App\Traits\HasMoneyAccessors;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

    protected $fillable = [
        'uuid',
        'user_id',
        'subscription_id',
        'amount_cents',
        'status',
        'payment_method',
        'payment_gateway',
        'external_payment_id',
        'invoice_url',
        'pix_qr_code',
        'pix_copy_paste',
        'boleto_barcode',
        'due_date',
        'confirmed_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'due_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount_cents' => 'integer',
    ];

    /**
     * Interact with the payment's amount.
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($this->amount_cents),
        );
    }

    /**
     * Get formatted amount as BRL string
     */
    protected function amountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCentsAsBRL($this->amount_cents),
        );
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if payment is received
     */
    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    /**
     * Check if payment is PIX
     */
    public function isPix(): bool
    {
        return $this->payment_method === 'pix';
    }

    /**
     * Check if payment is Boleto
     */
    public function isBoleto(): bool
    {
        return $this->payment_method === 'boleto';
    }

    /**
     * Check if payment is Credit Card
     */
    public function isCreditCard(): bool
    {
        return $this->payment_method === 'credit_card';
    }

    /**
     * Mark as confirmed
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark as received
     */
    public function markAsReceived(): void
    {
        $this->update([
            'status' => 'received',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark as overdue
     */
    public function markAsOverdue(): void
    {
        $this->update([
            'status' => 'overdue',
        ]);
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePix($query)
    {
        return $query->where('payment_method', 'pix');
    }

    public function scopeBoleto($query)
    {
        return $query->where('payment_method', 'boleto');
    }

    public function scopeCreditCard($query)
    {
        return $query->where('payment_method', 'credit_card');
    }
}
