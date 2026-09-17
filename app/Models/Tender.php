<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $reference_no
 * @property string|null $reference_number
 * @property string $title
 * @property string|null $client_name
 * @property Carbon|null $published_date
 * @property Carbon|null $submission_deadline
 * @property Carbon|null $submitted_at
 * @property string|null $estimated_value
 * @property string|null $quoted_amount
 * @property string $quote_status
 * @property Carbon|null $quote_sent_at
 * @property Carbon|null $quote_decision_at
 * @property string|null $quote_feedback
 * @property string $currency
 * @property string|null $submission_channel
 * @property string|null $confirmation_reference
 * @property int|null $assigned_officer_id
 * @property int|null $created_by_id
 * @property string|null $contract_value
 * @property string|null $outcome_notes
 * @property string|null $description
 * @property string|null $scope_of_work
 * @property string|null $sla_expectations
 * @property string|null $eligibility_criteria
 * @property string|null $category
 * @property string $status
 * @property Carbon|null $published_at
 * @property Carbon|null $deadline
 * @property string|null $document_path
 * @property-read Collection<int, TenderSubmission> $submissions
 * @property-read User|null $assignedOfficer
 * @property-read Collection<int, TenderDocument> $documents
 * @property-read Collection<int, TenderActivity> $activities
 */
class Tender extends Model
{
    protected $fillable = [
        'reference_no',
        'reference_number',
        'title',
        'client_name',
        'published_date',
        'submission_deadline',
        'submitted_at',
        'estimated_value',
        'quoted_amount',
        'quote_status',
        'quote_sent_at',
        'quote_decision_at',
        'quote_feedback',
        'currency',
        'submission_channel',
        'confirmation_reference',
        'assigned_officer_id',
        'created_by_id',
        'contract_value',
        'outcome_notes',
        'description',
        'scope_of_work',
        'sla_expectations',
        'eligibility_criteria',
        'category',
        'status',
        'published_at',
        'deadline',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'deadline' => 'datetime',
            'published_date' => 'date',
            'submission_deadline' => 'datetime',
            'submitted_at' => 'datetime',
            'estimated_value' => 'decimal:2',
            'quoted_amount' => 'decimal:2',
            'quote_sent_at' => 'datetime',
            'quote_decision_at' => 'datetime',
            'contract_value' => 'decimal:2',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TenderSubmission::class);
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenderDocument::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TenderActivity::class);
    }

    public function quoteItems(): HasMany
    {
        return $this->hasMany(TenderQuoteItem::class);
    }
}
