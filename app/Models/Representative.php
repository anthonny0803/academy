<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Representative extends Model
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'phone',
        'address',
        'occupation',
        'birth_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];


    // Relationships:

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // Query Scopes

    public function scopeSearch($query, string $term)
    {
        return $query->whereHas('user', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    public function scopeWithStudents($query)
    {
        return $query->with('students');
    }

    // Accessors

    public function getFullNameAttribute(): string
    {
        return $this->user ? trim("{$this->user->name} {$this->user->last_name}") : '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    // Mutators

    protected function setBirthDateAttribute($value): void
    {
        if (is_string($value)) {
            try {
                $this->attributes['birth_date'] = Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->attributes['birth_date'] = null;
            }
        } else {
            $this->attributes['birth_date'] = $value;
        }
    }

    // Business Logic

    public function hasStudents(): bool
    {
        return $this->students()->exists();
    }


    public function getStudentCount(): int
    {
        return $this->students()->count();
    }
}
