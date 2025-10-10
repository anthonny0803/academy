<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class DeleteAcademicPeriodService
{
    public function handle(AcademicPeriod $academicPeriod): void
    {
        DB::transaction(function () use ($academicPeriod) {
            $academicPeriod->delete();
        });
    }
}
