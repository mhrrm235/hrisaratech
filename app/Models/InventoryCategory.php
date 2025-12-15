<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $fillable = ['name', 'description', 'items_count'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
