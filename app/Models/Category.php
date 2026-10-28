<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    protected $fillable   = ['name'];

    // A category has many materials (including soft-deleted ones)
    public function materials()
    {
        return $this->hasMany(Material::class, 'category_id', 'id');
    }

    // Only active (non-deleted) materials
    public function activeMaterials()
    {
        return $this->hasMany(Material::class, 'category_id', 'id')->whereNull('deleted_at');
    }
}
