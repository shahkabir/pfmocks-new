# PerfectMocks — Project Context

## Overview

**PerfectMocks** is a Laravel-based online exam preparation platform for students preparing for standardized tests (IELTS, PTE, TOEFL, GRE). Students can attempt free or paid mock exams across Reading, Writing, Listening, and Speaking modules, and receive scored results with band feedback. It also serves mock exams for Government Entry Exams which are basically MCQ based exams.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 12.0 |
| PHP version | ^8.2 |
| Frontend templating | Blade |
| CSS framework | TailwindCSS 4.0.0 (via Vite) + Bootstrap 5.3.8 (CDN) |
| JavaScript | jQuery 3.7.1 (CDN) + Axios 1.11.0 |
| Build tool | Vite 7.0.7 with laravel-vite-plugin 2.0.0 |
| Database | MySQL (`database/database-15-Apr-2026.sql`) |
| Admin UI theme | AdminLTE (`/public/admin/dist/`) |
| Icons | Bootstrap Icons 1.13.1 (CDN) |
| Scrollbars | OverlayScrollbars 2.11.0 |
| Datatable | Yajra DataTables 12.0 |
| Timezone | Asia/Dhaka |

---

## Key Features

1. **OTP-based Authentication** — Email/mobile login with a 4-digit OTP; no traditional password flow in active use
2. **Exam Engine** — Module-based exams (Reading, Writing, Listening, Speaking, General MCQ) with per-question navigation and a question palette
3. **Question Types** — MCQ single/multiple, fill-in-blanks, text/essay, audio (speaking)
4. **Auto-Grading** — Reading, Listening and General MCQ answers are auto-scored; Writing and Speaking go to manual evaluator review
5. **IELTS Band Scoring** — Score percentage mapped to IELTS band 0–9
6. **Student Dashboard** — Lists purchased / free exams with status chips and "Show Result" review mode
7. **Admin Panel** — Exam / Module / Question / Question-Option CRUD, user management, payment verification, evaluator assignment, referral programs CRUD — all DataTables-driven
8. **Results Tracking** — Per-attempt `Results` row with breakdown JSON, time taken, evaluator feedback (typed + voice), overall band
9. **Payment System** — Manual bKash trxId verification flow: student submits trxId in Buy modal → admin approves/rejects → `UserExam` flips between `payment_pending`/`purchased`/`cancelled`
10. **Evaluator Pipeline** — Admin assigns finished writing/speaking attempts to evaluators (DataTable + assign modal); evaluator submits IELTS-criteria scores + typed feedback + optional voice recording; student gets emailed and sees evaluator panel inline in review mode
11. **Referral Program** — Each user gets a unique `PM-XXXXXXXX` code with shareable link; signup-time pending invitation → discount applied + invitation locked at first paid order. Email-share modal + WhatsApp/Facebook/Telegram/X/copy buttons. Admin CRUD for `referral_programs`. Auto-BCC `gorrjon.official@gmail.com` on every outgoing mail via `App\Services\Mail\Mailer`
12. **Top-bar Discount Banner** — Logged-in users with a still-redeemable referral claim see a soft mint→peach gradient banner on every page; only the discount amount shimmer-pulses
13. **SOP Service** — Statement-of-Purpose Review (1000 BDT) and New SOP Writing (2000 BDT) follow the same pipeline as IELTS Writing: student submits résumé + university + country + (for review) existing SOP via the Buy modal → admin verifies bKash payment → evaluator gets assigned → evaluator uploads the finished SOP → student downloads. All stages emit emails through the common `Mailer`.

---

## Application Roles

| Role | Access | Description
|---|---|
| `user` | Student dashboard, exams, profile | Mock Exam Takers
| `admin` | All student routes + `/admin/*` routes | Administrative Tasks
| `evaluator` | Assigned exams for Evaluation, published exams | Evaluates Exams e.g. IELTS speaking, writing in typed feedback or voice recorded

---

## Entry Points

| URL | Route Name | Purpose |
|---|---|---|
| `/login` | `login.submit` | Login form (OTP flow) |
| `/register` | `signup` / `register.submit` | Self-service registration (accepts `?ref=CODE`) |
| `/show-otp` | `otp.verify.view` | OTP verification |
| `/dashboard-student` | `dashboard.student` | Student exam list |
| `/exam~/{examName}` | `exams.show` | Exams by type (IELTS, PTE…) — Buy modal lives here |
| `/exam/{module}/start` | `exam.start` | Start a specific module |
| `/exam/result/{userExamId}` | `exam.result` | "Show Result" — review-mode view of completed attempt |
| `/services/sop` | `sop.index` | SOP service landing + purchase modal + my-submissions |
| `/services/sop/submit` | `sop.submit` | Submit résumé + university + country + trxId |
| `/services/sop/{id}/download/{type}` | `sop.download` | Auth-checked download (resume / original-sop / final-sop) |
| `/payment/submit` | `payment.submit` | Submit bKash trxId for an exam module |
| `/referral` | `referral.index` | Refer-a-Friend share page |
| `/evaluator/evaluations` | `evaluator.evaluations.index` | Evaluator's My Evaluations |
| `/admin2/payments` | `admin.payments.index` | Admin payment verification |
| `/admin2/evaluations` | `admin.evaluations.index` | Admin assigns evaluators (DataTable) |
| `/admin2/referral-programs` | `admin.referral-programs.index` | Admin CRUD for referral programs |
| `/admin2/modules` | `admin.modules.index` | Admin module list |
| `/user-list` | `admin.user.list` | Admin user management |

---

## Directory Structure (Top-Level)

```
pfmocks-new/
├── app/
│   ├── Constants/                # ModuleConstants (module type labels + EVALUATABLE_TYPES + SOP_TYPES)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # ExamController, ModuleController, QuestionController,
│   │   │   │                       PaymentController, EvaluationController, ReferralProgramController, …
│   │   │   ├── Auth/             # RegisterController (login/OTP/register)
│   │   │   ├── Evaluator/        # EvaluationController (my-evaluations, show, submit feedback)
│   │   │   ├── ExamController.php       # start, dashboard, showResult, submitIELTSWriting, submitIELTSReadingAndListening, …
│   │   │   ├── PaymentController.php    # student-facing bKash submit + moduleInfo
│   │   │   ├── ReferralController.php   # share page + send-email
│   │   │   └── SopController.php        # SOP services landing + submit + download
│   │   └── Middleware/           # AdminMiddleware, EvaluatorMiddleware
│   ├── Mail/                     # ReferralInviteMail, EvaluationAssignedMail, EvaluationCompletedMail
│   ├── Models/
│   │   ├── Answer/               # Answer, Results
│   │   ├── Auth/                 # Otp
│   │   ├── Evaluation/           # Evaluation
│   │   ├── Exam/                 # Exam, ExamAttempt, UserExam
│   │   ├── Module/               # Module
│   │   ├── Payment/              # Payment
│   │   ├── Question/             # Question, QuestionOptions, QuestionGroup, QuestionGroupBlock
│   │   ├── Referral/             # ReferralUserProfile, ReferralProgram, ReferralInvitation, ReferralRedemption
│   │   ├── Sop/                  # SopSubmission
│   │   └── User.php
│   ├── Repositories/             # Base + interface-bound repos (Exam, Module, Question, Payment,
│   │                               ReferralProgram, …)
│   └── Services/
│       ├── Mail/Mailer.php       # central mailer — auto-BCCs gorrjon.official@gmail.com
│       ├── EvaluationService.php # assign + submit-feedback orchestration
│       ├── PaymentService.php    # submit/approve/reject + auto-seed SOP attempt on approve
│       ├── ReferralService.php   # code generation, signup invitation, redemption, banner DTO
│       └── SopService.php        # student SOP submission orchestration
├── database/
│   ├── migrations/               # All schema migrations (csv_imports, payments, referral_*, evaluations, sop_submissions, …)
│   └── seeders/                  # DatabaseSeeder, SopServiceSeeder
├── public/
│   ├── admin/dist/               # AdminLTE theme assets
│   └── data/
│       ├── audio/feedback/       # Evaluator voice-feedback recordings
│       └── sop/{userId}/         # Per-user résumés / SOP files (download via auth-checked route)
├── resources/
│   ├── css/app.css               # TailwindCSS entry
│   ├── js/                       # app.js + bootstrap.js (Axios setup)
│   └── views/
│       ├── auth/                 # login, verify-otp, register (Bootstrap-only, no AdminLTE)
│       ├── admin/                # exams, modules, questions, payments, referral-programs, evaluations
│       ├── student/
│       │   ├── dashboard.blade.php           # My Exams (modern chips/cards)
│       │   ├── dashboard-all-exams.blade.php # Buy flow per exam type
│       │   └── services/sop.blade.php        # SOP services landing + purchase + download
│       ├── evaluator/evaluations/   # index, show (writing/speaking), sop (SOP-specific)
│       ├── exams/
│       │   ├── ielts/            # reading, writing, listening, speaking (review-mode, modal, palette)
│       │   ├── general/mcq.blade.php
│       │   └── partials/_evaluator_feedback.blade.php
│       ├── emails/               # referral-invite, evaluation-assigned, evaluation-completed
│       ├── referral/index.blade.php
│       └── layouts/              # app, header, nav-top, menu, footer, scripts, _referral_banner
├── routes/
│   ├── web.php                   # Public + auth routes (student, evaluator, payment, referral, sop)
│   ├── admin.php                 # Admin CRUD routes (mounted under /admin2 with admin middleware)
│   └── api.php                   # (unused / minimal)
└── config/
    ├── app.php                   # App timezone, name, etc.
    ├── payment.php               # bKash payee MSISDN + QR path + currency
    ├── upload_audio.php          # Audio upload mimes/path
    ├── upload_image.php          # Image upload mimes/size/path
    └── csv_import.php            # CSV import limits + disk + path
```

---

## Pipelines / State Machines

### Payment + UserExam
```
Buy click → submit trxId
  Payment.status = pending_verification
  UserExam.status = payment_pending
  ↓
Admin approves            Admin rejects
  Payment.status = approved   Payment.status = rejected
  UserExam.status = purchased UserExam.status = cancelled
  (referral redemption, if pending)
  (SOP module → auto-create completed ExamAttempt + pending Results)
```

### Evaluation (writing / speaking / sop_*)
```
Attempt completes → Results.status = completed (or pending for SOP)
Admin assigns evaluator → Evaluation.status = assigned
                          Results.status   = manual_review
                          (evaluator + student emailed)
Evaluator opens page    → Evaluation.status = in_progress
Evaluator submits       → Evaluation.status = completed
                          Results.status   = evaluated
                          Results.band_score = overallBand()
                          (For SOP: SopSubmission.final_sop_path filled)
                          (Student emailed with result link)
```

### Referral
```
Signup with ?ref=CODE → ReferralInvitation.status = pending
First paid order approved → ReferralInvitation.status = redeemed
                            ReferralRedemption row created
                            (top-bar banner disappears)
Other paid order first → ReferralInvitation.status = expired
```

---

## Composer Dependencies

- `laravel/framework ^12.0`
- `yajra/laravel-datatables ^12.0`
- `laravel/tinker ^2.10.1`

Dev:
- `barryvdh/laravel-debugbar`
- `phpunit/phpunit ^11.5.3`
- `laravel/sail ^1.41`
