<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int $tender_id
 * @property string $company_name
 * @property string $email
 * @property string $phone_number
 * @property string $proposed_amount
 * @property string $technical_proposal_path
 * @property string $financial_proposal_path
 * @property string $status
 * @property-read Tender $tender
 */
class TenderSubmission extends Model
{
    use Notifiable;

    protected $fillable = [
        'tender_id',
        'company_name',
        'email',
        'phone_number',
        'proposed_amount',
        'technical_proposal_path',
        'financial_proposal_path',
        'status',
    ];

    /** @return BelongsTo<Tender, $this> */
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function routeNotificationForMail(?object $notification = null): string
    {
        return $this->email;
    }
}
