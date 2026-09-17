<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'stock', 'unit_cost', 'unit_price', 'active'])]
class InventoryItem extends Model
{
    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2', 'unit_price' => 'decimal:2', 'active' => 'boolean'];
    }
}
