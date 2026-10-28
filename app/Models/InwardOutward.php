<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InwardOutward extends Model
{
    protected $table      = 'inward_outward';
    protected $primaryKey = 'id';
    protected $fillable   = ['material_id', 'date', 'quantity'];
    protected $casts      = [
        'quantity'    => 'decimal:2',
        'date'        => 'date',
        'material_id' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Belongs to a material
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }
}
