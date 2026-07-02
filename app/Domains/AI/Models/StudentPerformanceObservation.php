<?php

namespace App\Domains\AI\Models;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPerformanceObservation extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'student_id',
        'requested_by_id',
        'status',
        'content',
        'failure_reason',
        'generated_at',
    ];

    protected $casts = [
        'status' => ObservationStatus::class,
        'generated_at' => 'datetime',
    ];

    // Relationships

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }
}
