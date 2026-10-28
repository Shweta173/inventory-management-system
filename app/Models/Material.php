<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $table      = 'materials';
    protected $primaryKey = 'id';
    protected $fillable   = ['category_id', 'name', 'opening_balance'];
    protected $casts      = [
        'opening_balance' => 'decimal:2',
        'category_id'     => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    // Belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // All inward/outward transactions for this material
    public function transactions()
    {
        return $this->hasMany(InwardOutward::class, 'material_id', 'id');
    }

    // Current balance = opening balance + sum of all transaction quantities
    public function getCurrentBalanceAttribute(): float
    {
        $totalTransactions = $this->transactions()->sum('quantity');
        return (float) $this->opening_balance + (float) $totalTransactions;
    }
}
