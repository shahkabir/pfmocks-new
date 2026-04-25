<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces
use App\Repositories\Interfaces\ExamRepositoryInterface;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuestionOptionsRepositoryInterface;
use App\Repositories\Interfaces\QuestionGroupRepositoryInterface;
use App\Repositories\Interfaces\QuestionGroupBlockRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\ReferralProgramRepositoryInterface;

// Implementations
use App\Repositories\ExamRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\QuestionOptionsRepository;
use App\Repositories\QuestionGroupRepository;
use App\Repositories\QuestionGroupBlockRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ReferralProgramRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ExamRepositoryInterface::class,               ExamRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class,             ModuleRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class,           QuestionRepository::class);
        $this->app->bind(QuestionOptionsRepositoryInterface::class,    QuestionOptionsRepository::class);
        $this->app->bind(QuestionGroupRepositoryInterface::class,      QuestionGroupRepository::class);
        $this->app->bind(QuestionGroupBlockRepositoryInterface::class, QuestionGroupBlockRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,            PaymentRepository::class);
        $this->app->bind(ReferralProgramRepositoryInterface::class,    ReferralProgramRepository::class);
    }
}
