<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['inventory_category_id', 'name', 'description', 'quantity', 'location', 'purchase_date', 'status'];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    protected $dates = ['purchase_date'];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function usageLogs()
    {
        return $this->hasMany(InventoryUsageLog::class);
    }

    // Ensure purchase_date is always a Carbon instance
    public function getPurchaseDateAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            return $value;
        }
        return $value ? \Carbon\Carbon::parse($value) : null;
    }
}
