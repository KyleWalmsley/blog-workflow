<?php

namespace App\Models;

use App\Enums\CopySectionStatus;
use App\Enums\CopySectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CopySection extends Model
{
    protected $fillable = [
        'job_id',
        'sort_order',
        'section_type',
        'title',
        'headline',
        'sub_headline',
        'content',
        'meta_title',
        'meta_description',
        'status',
        'client_notes',
    ];

    protected function casts(): array
    {
        return [
            'section_type' => CopySectionType::class,
            'status' => CopySectionStatus::class,
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CopySection $section) {
            $type = $section->section_type instanceof CopySectionType
                ? $section->section_type
                : CopySectionType::from($section->section_type);
            $section->sort_order = $type->sortOrder();

            if (empty($section->status)) {
                $section->status = CopySectionStatus::Pending;
            }
        });
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
