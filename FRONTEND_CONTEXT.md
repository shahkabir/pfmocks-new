# Frontend Context — jQuery & Blade Structure

## Blade Layout Hierarchy

```
layouts/app.blade.php          ← root layout (AdminLTE shell)
  ├── layouts/header.blade.php     ← <head> meta/CSS includes
  ├── layouts/nav-top.blade.php    ← top navigation bar
  ├── layouts/menu.blade.php       ← sidebar menu links
  ├── @yield('content')            ← page-specific content
  ├── layouts/footer.blade.php     ← page footer
  └── layouts/scripts.blade.php    ← JS includes (jQuery, AdminLTE, etc.)
```

`adminlte.blade.php` — alternate AdminLTE-specific root used by some exam pages.

---

## Views Directory Map

```
resources/views/
├── auth/
│   ├── login.blade.php            ← Login form + OTP modal trigger
│   └── verify-otp.blade.php       ← OTP code entry + resend
├── student/
│   ├── dashboard.blade.php        ← Assigned exam list with status badges
│   └── dashboard-all-exams.blade.php ← All exams grouped by exam type
├── exams/ielts/
│   ├── reading.blade.php          ← Split-screen (55% passage / 45% questions)
│   ├── writing.blade.php          ← Essay textarea + word count
│   ├── listening.blade.php        ← Audio player + question panel
│   └── speaking.blade.php         ← Audio recorder + prompt
├── admin/
│   ├── module/
│   │   ├── module.blade.php       ← Paginated module list
│   │   ├── create.blade.php       ← Create module form
│   │   └── edit.blade.php         ← Edit module form
│   └── user/
│       └── user-list.blade.php    ← DataTables user management
├── layouts/
│   ├── app.blade.php
│   ├── header.blade.php
│   ├── nav-top.blade.php
│   ├── menu.blade.php
│   ├── footer.blade.php
│   └── scripts.blade.php
├── profile.blade.php              ← Student profile page
├── dashboard.blade.php            ← Generic dashboard placeholder
└── welcome.blade.php              ← (commented out / unused)
```

---

## Frontend Libraries

| Library | Version | How Loaded |
|---|---|---|
| AdminLTE | ~3.x | `/public/admin/dist/` (local) |
| Bootstrap | 5.3.8 | CDN |
| jQuery | 3.7.1 | CDN |
| Bootstrap Icons | 1.13.1 | CDN |
| TailwindCSS | 4.0.0 | Vite (`resources/css/app.css`) |
| Axios | 1.11.0 | npm / `resources/js/bootstrap.js` |
| OverlayScrollbars | 2.11.0 | npm |
| Yajra DataTables | 12.0 | CDN + `datatables.net-bs5` |
| SweetAlert2 | 11 | CDN (loaded in `layouts/header.blade.php`) |

---

## JavaScript Structure

### Vite-managed (npm)

```
resources/js/
├── app.js          ← entry point; imports bootstrap.js
└── bootstrap.js    ← configures Axios (sets X-Requested-With header)
```

### Inline / Page-level jQuery

Most interactivity is written as `<script>` blocks directly in Blade files.

**login.blade.php**
- jQuery AJAX `POST /login` on form submit
- On success, redirects to OTP view or dashboard
- On failure, shows inline error message

**verify-otp.blade.php**
- jQuery AJAX `POST /verify-otp`
- Auto-focuses first OTP digit input
- Handles resend OTP

**exam pages (reading / listening / writing / speaking)**
- Question palette — tracks answered/unanswered state per question index
- Timer countdown (derived from `module.duration_minutes`)
- Question navigation (prev/next buttons + palette click)
- Auto-submit on timer expiry
- AJAX submit to respective POST route, then redirects to dashboard

**admin/user/user-list.blade.php**
- Yajra DataTables initialization via `$.ajax` to `/user-list-data`
- Delete user via AJAX DELETE with CSRF token

---

## CSS / Asset Structure

```
resources/css/
└── app.css              ← @import "tailwindcss"; entry for Vite

public/admin/dist/
├── css/adminlte.css     ← AdminLTE core styles
└── js/adminlte.js       ← AdminLTE sidebar/toggle JS
```

TailwindCSS utilities are used primarily for layout helpers and exam UI elements. Bootstrap classes are used for buttons, modals, badges, and forms.

---

## Exam UI Patterns

### Reading Exam (`reading.blade.php`)
- Two-column split: **55% left** (scrollable passage) / **45% right** (question panel)
- Question palette at top: numbered boxes, green = answered, grey = unanswered
- Questions rendered based on `question_type`:
  - `mcq_single` → `<input type="radio">`
  - `mcq_multiple` → `<input type="checkbox">`
  - `fill_in_blanks` → `<input type="text">`
- Submit posts all answers as nested arrays to `/ielts-reading/submit`

### Listening Exam (`listening.blade.php`)
- Audio player (`<audio>`) at top, auto-plays or manual play
- Questions grouped by `part_number` (Parts 1–4)
- Question blocks separated by `instruction_text` from `question_group_blocks`
- Same answer input types as reading

### Writing Exam (`writing.blade.php`)
- `<textarea>` with live word count
- Timer shows remaining time
- Submits to `/ielts-writing/submit`
- On success, shows a blocking **SweetAlert2** modal ("Answers have been submitted…"); redirects to `route('dashboard')` after user clicks OK
- On failure, shows a SweetAlert2 error modal

### Speaking Exam (`speaking.blade.php`)
- Uses browser `MediaRecorder` API
- Uploads audio blob via FormData to `/speaking-upload-audio`
- Stored in `storage/app/public/speaking/`

---

## Vite Configuration

```js
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

In Blade: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
