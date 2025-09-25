<?php

namespace App\Traits;

trait Activatable
{
    public function activation(bool $active): void
    {
        $this->is_active = $active;
        $this->save();
    }
}
