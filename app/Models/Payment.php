<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'cheque_no',
        'amount',
        'cheque_image',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
