# Panduan Pengujian API CBT Menggunakan Postman

Panduan ini mendokumentasikan alur lengkap pengujian API CBT dari **Autentikasi**, **Memulai Ujian**, **Mengerjakan Ujian**, **Log Pengawasan**, hingga **Penerbitan Sertifikat Kelulusan**.

---

## 📌 1. Informasi Umum
* **Base URL:** `http://127.0.0.1:8000/api/v1`
* **Headers Wajib (untuk Rute Terproteksi):**
  * `Accept: application/json`
  * `Authorization: Bearer <your_jwt_token>`

---

## 🔐 2. Autentikasi (Authentication)

### A. Login User
Gunakan endpoint ini untuk masuk sebagai Super Admin atau Peserta.
* **Method:** `POST`
* **Endpoint:** `/auth/login`
* **Body (JSON):**
  ```json
  {
    "username": "peserta",
    "password": "password123"
  }
  ```
  *(Atau gunakan username `admin` dengan password yang sama untuk login Admin)*
* **Response Sukses:** Salin nilai `access_token` untuk digunakan pada rute terproteksi di bawah.

---

## 📝 3. Alur Pengerjaan Ujian (Ujian Berjalan)

Ikuti urutan langkah di bawah ini untuk mensimulasikan alur pengerjaan ujian oleh peserta:

### Langkah 1: Dapatkan Daftar Ujian Terjadwal
Melihat daftar ujian yang tersedia untuk peserta yang sedang login.
* **Method:** `GET`
* **Endpoint:** `/assessments`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Aksi:** Cari ujian *"Ujian Tengah Semester Matematika"* dan salin **`id`** ujian tersebut.

### Langkah 2: Mulai Sesi Ujian (Start Session)
Menginisialisasi sesi ujian dan mendapatkan batas waktu pengerjaan.
* **Method:** `POST`
* **Endpoint:** `/assessments/{assessment_id}/start`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Aksi:** Salin **`id`** sesi ujian (`session_id`) dari response untuk digunakan pada langkah selanjutnya.

### Langkah 3: Simpan Jawaban (Submit Answer)
Mengirimkan jawaban per butir soal secara interaktif.
* **Method:** `POST`
* **Endpoint:** `/sessions/{session_id}/answers`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Body (JSON - Contoh Pilihan Ganda):**
  ```json
  {
    "question_id": "<question_ulid>",
    "selected_option_id": "<option_ulid>"
  }
  ```
* **Body (JSON - Contoh Essay):**
  ```json
  {
    "question_id": "<question_ulid>",
    "answer_text": "Ini adalah jawaban essay saya..."
  }
  ```

### Langkah 4: Simpan Log Pengawasan (Proctoring Log)
Mengirimkan log pengawasan saat peserta melakukan aksi mencurigakan (misalnya pindah tab browser).
* **Method:** `POST`
* **Endpoint:** `/sessions/{session_id}/proctor-logs`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Body (JSON):**
  ```json
  {
    "event_type": "tab_switch",
    "event_details": "Peserta beralih ke tab Google Search"
  }
  ```

### Langkah 5: Selesaikan Ujian (Finish Session)
Mengakhiri sesi ujian dan mengunci jawaban peserta secara permanen.
* **Method:** `POST`
* **Endpoint:** `/sessions/{session_id}/finish`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Response Sukses:** Menampilkan status `completed` beserta **`total_score`** (akumulasi nilai otomatis dari pilihan ganda yang dijawab dengan benar).

---

## 🎓 4. Penerbitan Sertifikat Kelulusan

Jika nilai ujian peserta memenuhi atau melampaui **`passing_grade` (KKM)**, sertifikat dapat diunduh/diakses:

* **Method:** `GET`
* **Endpoint:** `/sessions/{session_id}/certificate`
* **Headers:** `Authorization: Bearer <token_peserta>`
* **Response Sukses:** Menampilkan nomor sertifikat resmi dan tanggal penerbitan.
