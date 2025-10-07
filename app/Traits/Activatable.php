<?php

namespace App\Traits;

trait Activatable
{
    // Global State Management

    public function activation(bool $active): void
    {
        $this->update(['is_active' => $active]);
    }

    public function toggleActivation(): void
    {
        $this->activation(!$this->is_active);
    }

    // Global Status Check

    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? false);
    }

    // Global Query Scopes

    public function scopeActive($query)
    {
        $table = $this->getTable();
        return $query->where("{$table}.is_active", true);
    }

    public function scopeInactive($query)
    {
        $table = $this->getTable();
        return $query->where("{$table}.is_active", false);
    }
}
