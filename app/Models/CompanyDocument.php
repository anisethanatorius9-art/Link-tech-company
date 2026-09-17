<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDocument extends Model
{
    protected $fillable = ['category', 'name', 'file_path', 'expires_at', 'uploaded_by'];

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
