# 📋 Rencana Implementasi Backend API CBT (Clean Architecture dengan ULID)

Dokumen ini berisi rencana arsitektur, skema database berbasis **ULID** (Universally Unique Lexicographically Sortable Identifier), struktur folder, dan langkah-langkah implementasi untuk membangun **Backend API Computer Based Test (CBT)** berdasarkan analisis gabungan dari [gemini-code-1780456307803.md](file:///c:/laragon/www/cbt-backend/gemini-code-1780456307803.md) dan [gemini-code-1780456587210.md](file:///c:/laragon/www/cbt-backend/gemini-code-1780456587210.md).

---

## 🏗️ 1. Arsitektur Folder (Clean Architecture)

Untuk memisahkan logika bisnis dari database dan HTTP layer, kita menerapkan struktur **Service-Repository Pattern** di dalam direktori `app/`:

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/                  # API Controller (hanya memanggil Service & mengembalikan Resource)
│   ├── Requests/                 # Form Request untuk Validasi Input
│   └── Resources/                # API Resource untuk Format Output JSON
├── Models/                       # Eloquent Models (menggunakan trait HasUlids)
├── Repositories/                 # Abstraksi Database Access
│   ├── Contracts/                # Interfaces/Contracts
│   │   ├── UserRepositoryInterface.php
│   │   ├── QuestionRepositoryInterface.php
│   │   ├── AssessmentRepositoryInterface.php
│   │   └── AssessmentSessionRepositoryInterface.php
│   └── Eloquent/                 # Implementasi Eloquent
│       ├── UserRepository.php
│       ├── QuestionRepository.php
│       ├── AssessmentRepository.php
│       └── AssessmentSessionRepository.php
├── Services/                     # Pusat Logika Bisnis (Business Logic Layer)
│   ├── AuthService.php           # Logika Auth & JWT Sesi
│   ├── QuestionService.php       # Manajemen Bank Soal & Media
│   ├── AssessmentService.php     # Konfigurasi Ujian & Perakitan Paket
│   ├── TestEngineService.php     # Logika Ujian (Start, Auto-save, Proctoring)
│   └── GradingService.php        # Auto-grading, Essay Rubric, & Kalkulasi IRT
└── Providers/
    └── RepositoryServiceProvider.php # Binding Interface ke Implementasi
```

---

## 🗄️ 2. Skema Database & Relasi (Berbasis ULID)

Semua primary key (`id`) dan foreign key diubah menggunakan tipe data **ULID** (26 karakter string alfanumerik yang terurut secara waktu) untuk keamanan, skalabilitas, dan kemudahan indexing dibanding UUID biasa.

```mermaid
erDiagram
    users ||--o{ group_user : "belongs to"
    groups ||--o{ group_user : "contains"
    categories ||--o{ questions : "categorizes"
    questions ||--o{ question_options : "has"
    assessments ||--o{ assessment_group : "assigned to"
    groups ||--o{ assessment_group : "assigned"
    assessments ||--o{ assessment_question : "contains"
    questions ||--o{ assessment_question : "included in"
    assessments ||--o{ assessment_sessions : "has"
    users ||--o{ assessment_sessions : "attempts"
    assessment_sessions ||--o{ session_answers : "records"
    questions ||--o{ session_answers : "answered"
    question_options ||--o{ session_answers : "selected"
    assessment_sessions ||--o{ assessment_proctoring_logs : "monitors"
    assessment_sessions ||--o1 certificates : "issues"
    
    users {
        ulid id PK
        string name
        string email
        string password
        timestamp email_verified_at
    }
    groups {
        ulid id PK
        string name
        text description
    }
    group_user {
        ulid group_id FK
        ulid user_id FK
    }
    categories {
        ulid id PK
        ulid parent_id FK "nullable"
        string name
    }
    questions {
        ulid id PK
        ulid category_id FK
        enum type "pg, essay, likert"
        enum difficulty "easy, medium, hard"
        longtext content_text
        ulid created_by FK
    }
    question_options {
        ulid id PK
        ulid question_id FK
        text option_text
        boolean is_correct
        decimal weight
    }
    assessments {
        ulid id PK
        string title
        datetime start_date
        datetime end_date
        int duration_minutes
        int max_attempts
        boolean randomize_questions
        boolean randomize_options
        decimal passing_grade
    }
    assessment_group {
        ulid assessment_id FK
        ulid group_id FK
    }
    assessment_question {
        ulid assessment_id FK
        ulid question_id FK
        int order_no "default 0"
    }
    assessment_sessions {
        ulid id PK
        ulid assessment_id FK
        ulid user_id FK
        datetime start_time
        datetime end_time
        enum status "in_progress, completed, force_submitted"
        decimal total_score
    }
    session_answers {
        ulid id PK
        ulid session_id FK
        ulid question_id FK
        ulid selected_option_id FK "nullable"
        text answer_text "nullable"
        boolean is_correct
        decimal score_earned
    }
    assessment_proctoring_logs {
        ulid id PK
        ulid session_id FK
        string event_type "tab_lost_focus, right_click, copy_paste"
        text event_details
        timestamp created_at
    }
    certificates {
        ulid id PK
        ulid assessment_session_id FK
        ulid user_id FK
        string certificate_number
        timestamp issue_date
        string file_path
    }
```

---

## 🚀 3. Rencana Langkah-Langkah Pengerjaan

### 📅 Fase 1: Setup & Instalasi Dependensi
1. **Mengaktifkan Routing API:** Jalankan `php artisan install:api` untuk menghasilkan file `routes/api.php` dan middleware API.
2. **Setup JWT Auth:** Menginstal package `php-open-source-saver/jwt-auth`.
3. **Setup Spatie Permission:** Menginstal `spatie/laravel-permission` (modifikasi migrasi untuk mendukung ULID).
4. **Setup Spatie MediaLibrary:** Menginstal `spatie/laravel-medialibrary` untuk attachment media soal.
5. **Setup Laravel Excel:** Menginstal `maatwebsite/excel`.

### 📅 Fase 2: Migrasi dengan ULID & Model Eloquent
1. Membuat migrasi database. Contoh pembuatan skema tabel dengan ULID di Laravel:
   ```php
   Schema::create('questions', function (Blueprint $table) {
       $table->ulid('id')->primary();
       $table->foreignUlid('category_id')->constrained()->cascadeOnDelete();
       $table->enum('type', ['pg', 'essay', 'likert']);
       $table->enum('difficulty', ['easy', 'medium', 'hard']);
       $table->longText('content_text');
       $table->foreignUlid('created_by')->constrained('users')->cascadeOnDelete();
       $table->timestamps();
   });
   ```
2. Menggunakan trait `Illuminate\Database\Eloquent\Concerns\HasUlids` pada setiap Eloquent model:
   ```php
   use Illuminate\Database\Eloquent\Concerns\HasUlids;
   use Illuminate\Database\Eloquent\Model;

   class Question extends Model
   {
       use HasUlids;
   }
   ```

### 📅 Fase 3: Pembuatan Layer Repositories & Services
1. Membuat Interface dan implementasi Repositori untuk `User`, `Question`, `Assessment`, dan `AssessmentSession`.
2. Mendaftarkan service provider `RepositoryServiceProvider` untuk melakukan binding interface ke kelas Eloquent repository.
3. Membuat kerangka kelas Service di `app/Services/`.

### 📅 Fase 4: Autentikasi & RBAC (Menggunakan ULID)
1. Endpoint API Auth (`/api/auth/login`, `/api/auth/logout`, dll).
2. CRUD Pengguna & Group Peserta.
3. Import peserta secara massal dari Excel/CSV.

### 📅 Fase 5: Bank Soal & Media
1. CRUD kategori dan soal ujian.
2. Lampiran media gambar/audio ke soal via Spatie MediaLibrary.
3. Penambahan opsi jawaban beserta bobot nilainya.

### 📅 Fase 6: Konfigurasi Assessment & Perakitan Paket
1. CRUD `assessments`, pengaturan waktu pengerjaan, batas percobaan, passing grade, dan grup peserta yang ditugaskan.
2. Perakitan soal (static order vs dynamic random order).

### 📅 Fase 7: Test Engine (Transaction Execution)
1. **Join Assessment:** Memulai sesi (`assessment_sessions`) menggunakan ULID.
2. **Fetch Questions:** Mengambil daftar soal (mengacak pertanyaan dan opsi jawaban jika diaktifkan di konfigurasi assessment).
3. **Auto-Save Response:** Menyimpan jawaban parsial secara berkala ke `session_answers`.
4. **Proctoring Logs:** Mencatat event mencurigakan saat ujian berlangsung.

### 📅 Fase 8: Scoring & Penilaian Akhir
1. Auto-grading untuk soal pilihan ganda (PG) berdasarkan bobot di `question_options`.
2. Rubric grading untuk manual assessment (Essay).
3. Penerbitan sertifikat digital berformat PDF jika nilai total melampaui `passing_grade`.

---

> [!IMPORTANT]
> **Langkah Selanjutnya:**
> Saya siap mengeksekusi **Fase 1** untuk setup routing API, menginstal package dependensi utama (`jwt-auth`, `laravel-permission`, `laravel-medialibrary`, `laravel-excel`), dan mengonfigurasinya agar siap mendukung **ULID**. Apakah Anda mengizinkan saya untuk memulai proses instalasi package tersebut?
