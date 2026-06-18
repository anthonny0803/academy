<?php

namespace App\Providers;

use App\Domains\Academics\Repositories\EloquentSectionRepository;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Repositories\EloquentEnrollmentRepository;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Grades\Repositories\EloquentGradeRepository;
use App\Domains\Grades\Repositories\GradeRepository;
use App\Domains\Identity\Repositories\EloquentUserRepository;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Representatives\Repositories\EloquentRepresentativeRepository;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use App\Domains\Students\Repositories\EloquentStudentRepository;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository contract bindings: interface => implementation.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepository::class => EloquentUserRepository::class,
        RepresentativeRepository::class => EloquentRepresentativeRepository::class,
        StudentRepository::class => EloquentStudentRepository::class,
        EnrollmentRepository::class => EloquentEnrollmentRepository::class,
        GradeRepository::class => EloquentGradeRepository::class,
        SectionRepository::class => EloquentSectionRepository::class,
    ];

    public function register(): void
    {
        //
    }
}
