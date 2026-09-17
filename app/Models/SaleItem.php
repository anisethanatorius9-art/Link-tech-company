<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sale_id', 'inventory_item_id', 'product_name', 'quantity', 'unit_price', 'unit_cost', 'line_total'])]
class SaleItem extends Model
{
    protected function casts(): array { return ['unit_price' => 'decimal:2', 'unit_cost' => 'decimal:2', 'line_total' => 'decimal:2']; }
}
