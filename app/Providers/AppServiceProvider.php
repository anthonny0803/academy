<?php

namespace App\Providers;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\SubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Models\Student;
use App\Models\Grade;
use App\Models\GradeColumn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Stable polymorphic aliases. Decouple stored morph types
     * (model_has_roles, personal_access_tokens) from class namespaces.
     *
     * @var array<string, class-string>
     */
    private const MORPH_MAP = [
        'user' => User::class,
        'student' => Student::class,
        'representative' => Representative::class,
        'teacher' => Teacher::class,
        'subject' => Subject::class,
        'section' => Section::class,
        'academic_period' => AcademicPeriod::class,
        'subject_teacher' => SubjectTeacher::class,
        'section_subject_teacher' => SectionSubjectTeacher::class,
        'enrollment' => Enrollment::class,
        'grade' => Grade::class,
        'grade_column' => GradeColumn::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap(self::MORPH_MAP);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
