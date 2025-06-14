<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'po_number',
        'po_date',
        'po_image',
        'institution_name',
        'institution_email',
        'institution_phone',
        'institution_address',
        'status'
    ];

    protected $appends = ['total_amount'];

    protected $casts = [
        'po_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(POItem::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_amount');
    }

    public function getSuppliedAmountAttribute()
    {
        return $this->supplies->sum(function ($supply) {
            return $supply->items->sum(function ($item) {
                return $item->quantity * $item->poItem->price;
            });
        });
    }
}
