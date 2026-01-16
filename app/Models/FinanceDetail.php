<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'finance_snapshot_id',
        'stock_id',
        'source',
        'amount',
        'currency_code',
        'is_active',
        'comment',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function snapshot()
    {
        return $this->belongsTo(FinanceSnapshot::class, 'finance_snapshot_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
