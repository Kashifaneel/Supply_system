<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'po_item_id',
        'quantity'
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function poItem()
    {
        return $this->belongsTo(POItem::class, 'po_item_id');
    }

    public function getTotalAmountAttribute()
    {
        return $this->quantity * $this->poItem->price;
    }
}
