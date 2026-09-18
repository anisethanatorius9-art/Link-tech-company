<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $status
 * @property string $title
 * @property int|null $quantity
 * @property string|null $unit_cost
 * @property string|null $unit_price
 * @property string|null $vat_percent
 */
#[Fillable(['user_id', 'type', 'title', 'category', 'quantity', 'unit_cost', 'unit_price', 'quoted_amount', 'control_number', 'quote_document_path', 'customer_decision', 'payment_status', 'responded_at', 'margin_percent', 'vat_percent', 'supplier_name', 'received_quantity', 'received_at', 'notes', 'status'])]
class ProcurementRequest extends Model
{
    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'quoted_amount' => 'decimal:2',
            'margin_percent' => 'decimal:2',
            'vat_percent' => 'decimal:2',
            'received_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
