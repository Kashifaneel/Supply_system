<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $appends = ['total_amount'];

    protected $fillable = [
        'purchase_order_id',
        'user_id',
        'supply_date',
        'dc_pdf',
        'invoice_pdf',
        'dc_stamped',
        'invoice_stamped'
    ];

    protected $casts = [
        'supply_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SupplyItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->poItem->price;
        });
    }
}
