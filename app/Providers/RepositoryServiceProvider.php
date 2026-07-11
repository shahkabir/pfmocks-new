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
use App\Repositories\Interfaces\ScholarshipRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteSectionRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteQuestionSubTypeRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteModuleRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteQuestionGranularRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteModuleWiseQuestionRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteAttemptRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteAnswerRepositoryInterface;

// Implementations
use App\Repositories\ExamRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\QuestionOptionsRepository;
use App\Repositories\QuestionGroupRepository;
use App\Repositories\QuestionGroupBlockRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ReferralProgramRepository;
use App\Repositories\ScholarshipRepository;
use App\Repositories\Pte\PteSectionRepository;
use App\Repositories\Pte\PteQuestionSubTypeRepository;
use App\Repositories\Pte\PteModuleRepository;
use App\Repositories\Pte\PteQuestionGranularRepository;
use App\Repositories\Pte\PteModuleWiseQuestionRepository;
use App\Repositories\Pte\PteAttemptRepository;
use App\Repositories\Pte\PteAnswerRepository;

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
        $this->app->bind(ScholarshipRepositoryInterface::class,        ScholarshipRepository::class);

        // PTE
        $this->app->bind(PteSectionRepositoryInterface::class,              PteSectionRepository::class);
        $this->app->bind(PteQuestionSubTypeRepositoryInterface::class,      PteQuestionSubTypeRepository::class);
        $this->app->bind(PteModuleRepositoryInterface::class,               PteModuleRepository::class);
        $this->app->bind(PteQuestionGranularRepositoryInterface::class,     PteQuestionGranularRepository::class);
        $this->app->bind(PteModuleWiseQuestionRepositoryInterface::class,   PteModuleWiseQuestionRepository::class);
        $this->app->bind(PteAttemptRepositoryInterface::class,              PteAttemptRepository::class);
        $this->app->bind(PteAnswerRepositoryInterface::class,               PteAnswerRepository::class);
    }
}
