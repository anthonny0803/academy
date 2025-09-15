<?php

namespace App\Services;

use App\Models\Representative;
use Illuminate\Support\Facades\Auth;

class RepresentativeActivationService
{
    /**
     * Toggle the activation status of a representative.
     *
     * @param Representative $representative
     * @return Representative
     * @throws \Exception
     */
    public function changeStatus(Representative $representative): Representative
    {
        $currentUser = Auth::user();

        // Only a Supervisor can change representative status.
        if (!$currentUser->hasAnyRole(['Supervisor'])) {
            throw new \Exception('No tienes autorización para cambiar el estado de los representantes.');
        }

        // Toggle activation status
        $representative->activation(!$representative->is_active);

        return $representative;
    }
}
