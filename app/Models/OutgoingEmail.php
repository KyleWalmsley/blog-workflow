<?php

namespace App\Models;

use App\Enums\OutgoingEmailStatus;
use App\Enums\OutgoingEmailType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingEmail extends Model
{
    protected $fillable = [
        'job_id',
        'client_id',
        'recipient_email',
        'subject',
        'type',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => OutgoingEmailType::class,
            'status' => OutgoingEmailStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
