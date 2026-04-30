<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'total_amount',
        'invoice_num',
        'grand_total',
        'order_date',
        'tax_amount',
        'tax_type',
        'tax_value',
        'status',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    protected $casts = [
        'order_date'  => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'Pending',
            'pending_approval' => 'Menunggu Approval',
            'approved'         => 'Disetujui',
            'rejected'         => 'Ditolak',
            'paid'             => 'Lunas',
            'cancelled'        => 'Dibatalkan',
            default            => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid'             => 'badge-success',
            'pending'          => 'badge-warning',
            'pending_approval' => 'badge-info',
            'approved'         => 'badge-primary',
            'rejected'         => 'badge-danger',
            'cancelled'        => 'badge-secondary',
            default            => 'badge-light',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}