<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POItem extends Model
{
    use HasFactory;

    protected $table = 'p_o_items';

    protected $fillable = [
        'purchase_order_id',
        'name',
        'price',
        'quantity',
        'batch_no',
        'mfg_date',
        'exp_date',
        'total_amount',
        'supplied'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'mfg_date' => 'date',
        'exp_date' => 'date',
    ];

    protected $appends = ['total_amount', 'remaining_quantity'];

    public function getTotalAmountAttribute()
    {
        return $this->price * $this->quantity;
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->setAttribute('total_amount', $item->price * $item->quantity);
        });

        static::updating(function ($item) {
            $item->setAttribute('total_amount', $item->price * $item->quantity);
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplyItems()
    {
        return $this->hasMany(SupplyItem::class, 'po_item_id');
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->supplied;
    }

    public function getIsFullySuppliedAttribute()
    {
        return $this->supplied >= $this->quantity;
    }
}
