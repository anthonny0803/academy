<?php

namespace App\Traits;

trait Activatable
{
    /**
     * Activate or deactivate the model.
     *
     * @param bool $active
     * @return void
     */
    public function activation(bool $active): void
    {
        $this->is_active = $active;
        $this->save();
    }
}
