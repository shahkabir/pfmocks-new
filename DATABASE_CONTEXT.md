# Database Context — Schema & Relationships

## Engine

**MySQL** — file at `database/database-15-Apr-2026.sql`

Migrations live in `database/migrations/`. Run with `php artisan migrate`.

---

## Tables

### `users`
Migration: `2025_12_26_070848_create_users_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| name | string | |
| email | string | unique, nullable |
| mobile | string | unique, nullable |
| password | string | nullable (OTP-first flow) |
| role | string | default: `user`; values: `user`, `admin` |
| is_verified | boolean | default: false |
| created_at / updated_at | timestamps | |

---

### `otps`
Migration: `2025_12_26_070947_create_otps_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| user_id | foreignId | FK → users (cascadeOnDelete) |
| otp | string | 4-digit code |
| expires_at | timestamp | |
| created_at / updated_at | timestamps | |

---

### `exams`
Migration: `2025_12_26_071032_create_exams_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| name | string | e.g. IELTS, PTE, TOEFL, GRE |
| tag | string | internal slug |
| is_active | boolean | default: true |
| created_at / updated_at | timestamps | |

---

### `modules`
Migration: `2025_12_26_071110_create_modules_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| exam_id | foreignId | FK → exams (cascadeOnDelete) |
| name | string | e.g. "Reading Test 1" |
| module_type | string | `reading`, `writing`, `listening`, `speaking` |
| type | enum | `free`, `paid` |
| price_in_bdt | decimal | |
| price_in_usd | decimal | |
| duration_minutes | integer | |
| created_at / updated_at | timestamps | |

---

### `user_exams`
Migration: `2025_12_26_071143_create_user_exams_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| user_id | foreignId | FK → users (cascadeOnDelete) |
| module_id | foreignId | FK → modules (cascadeOnDelete) |
| type | enum | `free`, `paid` |
| price | decimal | default: 0 |
| status | string | `free`, `purchased`, `payment_pending`, `cancelled`, `completed` |
| purchased_at | timestamp | nullable |
| created_at / updated_at | timestamps | |

Unique constraint: `(user_id, module_id)`

---

### `exam_attempts`
Migration: `2025_12_26_071324_create_exam_attempts_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| user_id | foreignId | FK → users |
| module_id | foreignId | FK → modules |
| started_at | timestamp | |
| ended_at | timestamp | nullable |
| status | enum | `in_progress`, `completed` |
| created_at / updated_at | timestamps | |

Multiple attempts per user per module are allowed (no unique constraint on user+module).

---

### `answers`
Migration: `2025_12_26_071351_create_answers_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| user_id | foreignId | FK → users (cascadeOnDelete) |
| exam_attempt_id | foreignId | FK → exam_attempts (cascadeOnDelete) |
| question_id | foreignId | FK → questions (cascadeOnDelete) |
| question_option_id | foreignId | FK → question_options, nullable (nullOnDelete) |
| answer | text | nullable — used for essay / fill-in-blank / audio path |
| is_correct | boolean | nullable; auto-set for MCQ, null for manual review |
| created_at / updated_at | timestamps | |

Unique: `(exam_attempt_id, question_id, question_option_id)`

---

### `results`
Migration: `2025_12_26_071423_create_results_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| user_id | foreignId | FK → users (cascadeOnDelete) |
| exam_attempt_id | foreignId | FK → exam_attempts (cascadeOnDelete), unique |
| evaluated_by | foreignId | FK → users, nullable (nullOnDelete) — admin evaluator |
| status | string | `pending`, `completed`, `evaluated`, `manual_review` |
| exam_name | string | e.g. IELTS |
| module_name | string | e.g. Reading |
| achieved_score | integer | |
| total_score | integer | |
| score_percentage | decimal | |
| band_score | decimal | IELTS band 0.0–9.0 |
| time_taken_seconds | unsignedInteger | |
| evaluator_feedback | text | nullable |
| admin_feedback | text | nullable |
| breakdown | JSON | nullable — per-part scores e.g. `{"part1": 8, "part2": 7}` |
| created_at / updated_at | timestamps | |

Index on `(user_id, created_at)` for dashboard queries.
One result per attempt (unique on `exam_attempt_id`).

---

### `questions`
Migration: `2025_12_26_082359_create_questions_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| module_id | foreignId | FK → modules (cascadeOnDelete) |
| type | enum | `mcq_single`, `mcq_multiple`, `text`, `essay`, `audio`, `speaking` |
| question_header | text | block-level heading shown above question group |
| question_text | text | per-question body text |
| passage | longText | nullable — reading/listening source text |
| audio_url | string | nullable |
| image_url | string | nullable |
| marks | integer | default: 1 |
| sort_order | integer | default: 0 |
| meta | JSON | nullable — flexible extras (time_limit, word_limit, etc.) |
| created_at / updated_at | timestamps | |

---

### `question_options`
Migration: `2025_12_26_082516_create_question_options_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| question_id | foreignId | FK → questions (cascadeOnDelete) |
| actual_question | text | The rendered question text for this option row |
| question_type | string | `mcq_single`, `mcq_multiple`, `fill_in_blanks`, `writing`, `ielts_speaking`, etc. |
| option_text | string | Answer choice label (MCQ) |
| is_correct | boolean | default: false |
| correct_answer_explanation | text | nullable |
| correct_answer_fib |
| sort_order | integer | default: 0 |
| is_active | boolean | |
| question_image_path | string | nullable |
| question_audio_path | string | nullable |
| created_at / updated_at | timestamps | |

---

### `question_groups`
Migration: `2026_01_03_001750_create_question_groups_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| question_id | foreignId | FK → questions (cascadeOnDelete), unique (1:1) |
| question_options_group_ids | JSON | nullable — ordered list of option IDs in this group |
| part_number | tinyInteger | nullable — IELTS part number 1–4 |
| part_audio_url | string | nullable |
| part_image_url | string | nullable |
| created_at / updated_at | timestamps | |

---

### `question_group_blocks`
Migration: `2026_02_07_014617_create_question_group_blocks_table.php`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| question_group_id | foreignId | FK → question_groups (cascadeOnDelete) |
| instruction_text | text | Shown above the block of questions |
| question_option_ids | JSON | Ordered list of question_option IDs in this block |
| sort_order | integer | default: 0 |
| created_at / updated_at | timestamps | |

Index on `question_group_id`.

---

## Relationship Map

```
User ──────────────────────────────────────────────────────
 ├── hasMany → ExamAttempt
 ├── hasMany → Answer
 ├── hasMany → Results
 ├── hasMany → UserExam
 └── hasMany → Otp

Exam
 └── hasMany → Module

Module ─────────────────────────────────────────────────────
 ├── belongsTo → Exam
 ├── hasMany → Question
 ├── hasMany → UserExam
 └── hasMany → ExamAttempt

Question ───────────────────────────────────────────────────
 ├── belongsTo → Module
 ├── hasMany → QuestionOptions
 ├── hasMany → Answer
 └── hasOne → QuestionGroup

QuestionGroup
 ├── belongsTo → Question
 └── hasMany → QuestionGroupBlock

QuestionGroupBlock
 └── belongsTo → QuestionGroup

ExamAttempt ────────────────────────────────────────────────
 ├── belongsTo → User
 ├── belongsTo → Module
 ├── hasMany → Answer
 └── hasOne → Results

Answer
 ├── belongsTo → User
 ├── belongsTo → ExamAttempt
 ├── belongsTo → Question
 └── belongsTo → QuestionOptions

Results
 ├── belongsTo → User
 └── belongsTo → ExamAttempt
```

---

## Key Constraints Summary

| Constraint | Table | Detail |
|---|---|---|
| Unique | user_exams | `(user_id, module_id)` — one enrollment per user per module |
| Unique | results | `exam_attempt_id` — one result record per attempt |
| Unique | question_groups | `question_id` — one group per question |
| Unique | answers | `(exam_attempt_id, question_id, question_option_id)` — no duplicate answer rows |
| Cascade delete | most FK cols | Child rows removed when parent is deleted |

---

## Seeders

`database/seeders/DatabaseSeeder.php`
- Creates one test user: `test@example.com`
- Used for local dev only
