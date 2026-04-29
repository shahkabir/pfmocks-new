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
2. **Exam Engine** — Module-based exams (Reading, Writing, Listening, Speaking) with per-question navigation and a question palette
3. **Question Types** — MCQ single/multiple, fill-in-blanks, text/essay, audio (speaking)
4. **Auto-Grading** — Reading and Listening answers are auto-scored; Writing and Speaking go to manual review
5. **IELTS Band Scoring** — Score percentage mapped to IELTS band 0–9
6. **Student Dashboard** — Lists assigned exams with status (available, in-progress, completed)
7. **Admin Panel** — Module CRUD, user management, DataTables-driven user list
8. **Results Tracking** — Per-attempt results with breakdown JSON, time taken, evaluator feedback

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
| `/show-otp` | `otp.verify.view` | OTP verification |
| `/dashboard-student` | `dashboard.student` | Student exam list |
| `/exams/{examName}` | `exam.show` | Exams by type (IELTS, PTE…) |
| `/exam/{module}/start` | `exam.start` | Start a specific module |
| `/admin/modules` | `admin.modules.index` | Admin module list |
| `/user-list` | `admin.user.list` | Admin user management |

---

## Directory Structure (Top-Level)

```
pfmocks-new/
├── app/
│   ├── Constants/          # ModuleConstants mapping module types to labels
│   ├── Http/
│   │   ├── Controllers/    # Auth, Exam, Admin controllers
│   │   └── Middleware/     # admin middleware
│   └── Models/             # Eloquent models (User, Module, Question, Answer, Results, …)
├── database/
│   ├── migrations/         # All schema migrations
│   └── seeders/            # DatabaseSeeder with test user
├── public/
│   └── admin/dist/         # AdminLTE theme assets
├── resources/
│   ├── css/app.css         # TailwindCSS entry
│   ├── js/                 # app.js + bootstrap.js (Axios setup)
│   └── views/              # Blade templates
│       ├── auth/           # login, verify-otp
│       ├── admin/          # module CRUD, user-list
│       ├── student/        # dashboard views
│       ├── exams/ielts/    # reading, writing, listening, speaking exam pages
│       └── layouts/        # app, header, nav-top, menu, footer, scripts
├── routes/
│   ├── web.php             # All application routes
│   └── api.php             # (unused / minimal)
└── config/app.php          # App timezone, name, etc.
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
