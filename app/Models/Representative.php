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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
        return $query->whereHas('user', function ($userQuery) use ($term) {
            $userQuery->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
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

    public function getDocumentIdAttribute()
    {
        return $this->user->document_id ?? $this->attributes['document_id'] ?? null;
    }

    public function getBirthDateAttribute()
    {
        return $this->user->birth_date ?? $this->attributes['birth_date'] ?? null;
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone ?? $this->attributes['phone'] ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->user->address ?? $this->attributes['address'] ?? null;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->user->getAge();
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
