<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\Eloquent\QuestionRepository;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Eloquent\AssessmentRepository;
use App\Repositories\Contracts\AssessmentSessionRepositoryInterface;
use App\Repositories\Eloquent\AssessmentSessionRepository;
use App\Repositories\Contracts\ProctoringRepositoryInterface;
use App\Repositories\Eloquent\ProctoringRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(AssessmentRepositoryInterface::class, AssessmentRepository::class);
        $this->app->bind(AssessmentSessionRepositoryInterface::class, AssessmentSessionRepository::class);
        $this->app->bind(ProctoringRepositoryInterface::class, ProctoringRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
