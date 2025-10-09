<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Representative extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'birth_date',
        'phone',
        'address',
        'occupation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Representante';
    }

    // Relationships

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
        return $query->where(function ($q) use ($term) {
            $q->whereHas('user', function ($userQuery) use ($term) {
                $userQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
                ->orWhere('document_id', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    public function scopeHasStudents($query)
    {
        return $query->has('students');
    }

    public function scopeWithoutStudents($query)
    {
        return $query->doesntHave('students');
    }

    // Accessors

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
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

    // Mutators

    protected function setDocumentIdAttribute($value): void
    {
        $this->attributes['document_id'] = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
    }

    protected function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = preg_replace('/[^0-9]/', '', $value);
    }

    protected function setAddressAttribute($value): void
    {
        $this->attributes['address'] = strtoupper(trim(preg_replace('/\s+/', ' ', $value)));
    }
}
