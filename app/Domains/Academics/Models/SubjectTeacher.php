<?php

namespace App\Domains\Academics\Models;

use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class SubjectTeacher extends Model
{
    use AsPivot;
    use BelongsToTenant;
    use HasFactory;
    use HasUuids;

    protected $table = 'subject_teacher';

    protected $fillable = [
        'teacher_id',
        'subject_id',
    ];

    // Relationships

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
