<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'note',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(FinanceDetail::class)->orderBy('position')->orderBy('id');
    }
}
