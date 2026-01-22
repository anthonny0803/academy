<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Carbon\Carbon;
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
        $upperTerm = strtoupper($term);
        $lowerTerm = strtolower($term);

        return $query->whereHas('user', function ($userQuery) use ($upperTerm, $lowerTerm) {
            $userQuery->where('name', 'like', "%{$upperTerm}%")
                ->orWhere('last_name', 'like', "%{$upperTerm}%")
                ->orWhere('email', 'like', "%{$lowerTerm}%")
                ->orWhere('document_id', 'like', "%{$upperTerm}%")
                ->orWhere('phone', 'like', "%{$lowerTerm}%");
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

    public function getFullNameAttribute(): string
    {
        return $this->user?->full_name ?? '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->user?->age ?? null;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email ?? null;
    }

    public function getDocumentIdAttribute(): ?string
    {
        return $this->user?->document_id ?? null;
    }
    
    public function getSexAttribute(): ?string
    {
        return $this->user?->sex ?? null;
    }

    public function getBirthDateAttribute(): ?Carbon
    {
        return $this->user?->birth_date ?? null;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->user?->phone ?? null;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->user?->address ?? null;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->user?->occupation ?? null;
    }

    // Helper Methods

    public function hasStudents(): bool
    {
        return $this->students()->exists();
    }

    public function getStudentCount(): int
    {
        return $this->students()->count();
    }
}
