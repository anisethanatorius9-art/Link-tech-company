<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tender_id
 * @property int $version
 * @property string $description
 * @property string $unit
 * @property string $quantity
 * @property string $unit_price
 * @property string $vat_rate
 */
class TenderQuoteItem extends Model
{
    protected $fillable = ['tender_id', 'version', 'description', 'unit', 'quantity', 'unit_price', 'vat_rate'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'vat_rate' => 'decimal:2'];
    }

    /** @return BelongsTo<Tender, $this> */
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
