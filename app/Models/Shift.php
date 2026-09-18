<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'opened_at', 'closed_at', 'expected_cash', 'expected_mobile', 'expected_card', 'actual_cash', 'actual_mobile', 'actual_card', 'variance', 'status', 'notes'])]
class Shift extends Model
{
    protected function casts(): array
    {
        return ['opened_at' => 'datetime', 'closed_at' => 'datetime', 'expected_cash' => 'decimal:2', 'expected_mobile' => 'decimal:2', 'expected_card' => 'decimal:2', 'actual_cash' => 'decimal:2', 'actual_mobile' => 'decimal:2', 'actual_card' => 'decimal:2', 'variance' => 'decimal:2'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Sale, $this> */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
