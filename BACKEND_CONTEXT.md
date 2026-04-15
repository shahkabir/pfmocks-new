# Backend Context — Laravel Controllers & Models

## Controllers

### Auth

#### `app/Http/Controllers/Auth/RegisterController.php`
Handles the full OTP login flow.

| Method | Route | Purpose |
|---|---|---|
| `loginView()` | GET `/login` | Show login form |
| `validateLogin(Request)` | POST `/login` | Validate credentials, generate & store OTP, redirect to OTP view |
| `verifyView()` | GET `/show-otp` | Show OTP entry form |
| `verifyOTP(Request)` | POST `/verify-otp` | Verify OTP, log in user, redirect by role |
| `dashboard()` | GET `/dashboard` | Role-based redirect (admin → modules, user → student dashboard) |
| `logout()` | GET `/logout` | Invalidate session, redirect to login |
| `generateOtp()` | — | Private helper; returns `rand(1000, 9999)` |

#### `app/Http/Controllers/AuthController.php`
Legacy controller — `login()` method deprecated. Only `loginView()` and `logout()` remain relevant. Superseded by `RegisterController`.

---

### Exam

#### `app/Http/Controllers/ExamController.php`
Core exam logic.

| Method | Route | Purpose |
|---|---|---|
| `dashboard()` | GET `/dashboard-student` | Load student's assigned exams with status from `user_exams` |
| `showExams($examName)` | GET `/exams/{examName}` | Show all modules for a given exam type (IELTS, PTE…) |
| `start($moduleId)` | GET `/exam/{module}/start` | Create/resume `ExamAttempt`, load module+questions, route to correct exam view |
| `submitIELTSWriting(Request)` | POST `/ielts-writing/submit` | Save essay answers, create `Results` record with `status=manual_review` |
| `submitIELTSReadingAndListening(Request)` | POST `/ielts-reading/submit`, POST `/ielts-listening/submit` | Auto-grade MCQ/fill-in-blank answers, calculate band, save `Results` |
| `submitIeltsSpeakingAudio(Request)` | POST `/speaking-upload-audio` | Store uploaded audio, save `Answer`, mark result as `manual_review` |
| `calculateIELTSBand($pct)` | — | Private; maps score percentage to IELTS band 0.0–9.0 |
| `updateUserExamStatus($userId, $moduleId, $status)` | — | Private; upserts `user_exams.status` |
| `makeArrayLinear($array)` | — | Private; flattens nested answer arrays from different question-type payloads |

---

### Admin

#### `app/Http/Controllers/Admin/ModuleController.php`
CRUD for exam modules. All routes under `/admin/` with `auth` + `admin` middleware.

| Method | Route | Purpose |
|---|---|---|
| `index()` | GET `/admin/modules` | Paginated module list (10/page) |
| `create()` | GET `/admin/modules/create` | Create form |
| `store(Request)` | POST `/admin/modules` | Validate & save new module |
| `edit(Module)` | GET `/admin/modules/{module}/edit` | Edit form |
| `update(Request, Module)` | PUT `/admin/modules/{module}` | Validate & update module |
| `destroy(Module)` | DELETE `/admin/modules/{module}` | Delete module |

#### `app/Http/Controllers/Admin/UserController.php`
User management.

| Method | Route | Purpose |
|---|---|---|
| `showUserList(Request)` | GET `/user-list` | Render user list Blade view |
| `getUserListData()` | GET `/user-list-data` | Return all users as JSON (for DataTables AJAX) |
| `edit($id)` | GET `/user/edit/{id}` | Show edit user form |
| `update(Request)` | PUT `/user/update` | Update user name/email/mobile |
| `destroy($id)` | DELETE `/user/delete/{id}` | Delete user |

---

### General

#### `app/Http/Controllers/DashBoardController.php`
Single `index()` method — renders generic `dashboard` view.

#### `app/Http/Controllers/Controller.php`
Empty base controller extending Laravel's `Controller`.

---

## Middleware

| Middleware | Purpose |
|---|---|
| `auth` | Redirect unauthenticated users to `/login` |
| `admin` | Allow only users with `role = admin` (custom middleware) |

---

## Models

### `app/Models/User.php`
- Table: `users`
- Fillable: `name`, `email`, `password`
- Hidden: `password`, `remember_token`
- Casts: `email_verified_at` → datetime, `password` → hashed
- Relations: `hasMany(ExamAttempt)`, `hasMany(Answer)`, `hasMany(Results)`, `hasMany(UserExam)`, `hasMany(Otp)`

### `app/Models/Auth/Otp.php`
- Table: `otps`
- Fillable: `user_id`, `otp`, `expires_at`
- Relations: `belongsTo(User)`
- Method: `isExpired()` — returns `now() > expires_at`

### `app/Models/Exam/Exam.php`
- Table: `exams`
- Fillable: `name`, `tag`, `is_active`
- Relations: `hasMany(Module)`

### `app/Models/Module/Module.php`
- Table: `modules`
- Fillable: `exam_id`, `name`, `module_type`, `type`, `price_in_bdt`, `price_in_usd`, `duration_minutes`
- Relations: `belongsTo(Exam)`, `hasMany(Question)`, `hasMany(UserExam)`, `hasMany(ExamAttempt)`

### `app/Models/Exam/UserExam.php`
- Table: `user_exams`
- Fillable: `user_id`, `module_id`, `type`, `price`, `status`, `purchased_at`
- Relations: `belongsTo(User)`, `belongsTo(Module)`
- Unique constraint: `(user_id, module_id)`

### `app/Models/Exam/ExamAttempt.php`
- Table: `exam_attempts`
- Fillable: `user_id`, `module_id`, `started_at`, `ended_at`, `status`
- Relations: `belongsTo(User)`, `belongsTo(Module)`, `hasMany(Answer)`, `hasOne(Results)`
- Methods: `isInProgress()`, `complete()`

### `app/Models/Question/Question.php`
- Table: `questions`
- Fillable: `module_id`, `type`, `question_header`, `passage`, `audio_url`, `image_url`, `marks`, `sort_order`, `meta`
- Relations: `belongsTo(Module)`, `hasMany(QuestionOptions)`, `hasMany(Answer)`, `hasOne(QuestionGroup)`
- Scopes: `scopeOrdered()`, `scopeActive()`

### `app/Models/Question/QuestionOptions.php`
- Table: `question_options`
- Fillable: `question_id`, `actual_question`, `question_type`, `option_text`, `is_correct`, `correct_answer_explanation`, `sort_order`, `is_active`, `ielts_listening_question_line`, `question_image_path`, `question_audio_path`
- Relations: `belongsTo(Question)`
- Scopes: `scopeOrdered()`

### `app/Models/Question/QuestionGroup.php`
- Table: `question_groups`
- Fillable: `question_id`, `question_options_group_ids` (JSON), `part_number`, `part_audio_url`, `part_image_url`
- Relations: `belongsTo(Question)`, `hasMany(QuestionGroupBlock)`

### `app/Models/Question/QuestionGroupBlock.php`
- Table: `question_group_blocks`
- Fillable: `question_group_id`, `instruction_text`, `question_option_ids` (JSON), `sort_order`
- Relations: `belongsTo(QuestionGroup)`

### `app/Models/Answer/Answer.php`
- Table: `answers`
- Fillable: `user_id`, `exam_attempt_id`, `question_id`, `question_option_id`, `answer`, `is_correct`
- Relations: `belongsTo(User)`, `belongsTo(ExamAttempt)`, `belongsTo(Question)`, `belongsTo(QuestionOptions)`
- Unique: `(exam_attempt_id, question_id, question_option_id)`

### `app/Models/Answer/Results.php`
- Table: `results`
- Fillable: `user_id`, `exam_attempt_id`, `exam_name`, `evaluated_by`, `status`, `module_name`, `achieved_score`, `total_score`, `score_percentage`, `band_score`, `time_taken_seconds`, `evaluator_feedback`, `admin_feedback`, `breakdown`
- Relations: `belongsTo(User)`, `belongsTo(ExamAttempt)`
- Methods: `calculatePercentage()`, `formattedTime()`, `passed($passMark=50)`, `formatIELTSBand()`

---

## Routes Summary (`routes/web.php`)

```
# Public
GET  /login                      → RegisterController@loginView
POST /login                      → RegisterController@validateLogin
GET  /show-otp                   → RegisterController@verifyView
POST /verify-otp                 → RegisterController@verifyOTP
GET  /logout                     → RegisterController@logout

# Authenticated (middleware: auth)
GET  /dashboard                  → RegisterController@dashboard
GET  /dashboard-student          → ExamController@dashboard
GET  /exams/{examName}           → ExamController@showExams
GET  /exam/{module}/start        → ExamController@start
GET  /profile                    → (profile view)
GET  /ielts-writing              → (writing exam view)
POST /ielts-writing/submit       → ExamController@submitIELTSWriting
GET  /ielts-reading              → (reading exam view)
POST /ielts-reading/submit       → ExamController@submitIELTSReadingAndListening
GET  /ielts-listening            → (listening exam view)
POST /ielts-listening/submit     → ExamController@submitIELTSReadingAndListening
GET  /ielts-speaking             → (speaking exam view)
POST /speaking-upload-audio      → ExamController@submitIeltsSpeakingAudio

# Admin (middleware: auth, admin) — prefix: /admin
GET    /admin/modules            → ModuleController@index
GET    /admin/modules/create     → ModuleController@create
POST   /admin/modules            → ModuleController@store
GET    /admin/modules/{module}/edit → ModuleController@edit
PUT    /admin/modules/{module}   → ModuleController@update
DELETE /admin/modules/{module}   → ModuleController@destroy

# Admin User Management (middleware: auth, admin)
GET    /user-list                → UserController@showUserList
GET    /user-list-data           → UserController@getUserListData
GET    /user/edit/{id}           → UserController@edit
PUT    /user/update              → UserController@update
DELETE /user/delete/{id}         → UserController@destroy

# Utility
GET  /csrf-token                 → Returns JSON {csrfToken}
```

---

## Constants

### `app/Constants/ModuleConstants.php`
Maps `module_type` strings to human-readable labels used in views and results.

```php
const MODULE_TYPE_LABELS = [
    'reading'  => 'Reading',
    'writing'  => 'Writing',
    'listening'=> 'Listening',
    'speaking' => 'Speaking',
];
```
