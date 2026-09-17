<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderDocument extends Model
{
    protected $fillable = ['tender_id', 'document_type', 'file_path', 'uploaded_by'];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
