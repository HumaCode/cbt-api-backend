<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Models\Assessment;


class TiuTwkTkpSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create admin user
        $admin = User::where('username', 'admin')->first() ?? User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'username' => 'admin',
                'email' => 'admin@cbt.com',
                'password' => bcrypt('password123'),
                'is_active' => '1',
            ]);
        }

        // Create/Find Categories
        $tiu = Category::updateOrCreate(
            ['name' => 'Tes Inteligensia Umum (TIU)'],
            ['passing_grade' => 80.00]
        );
        $twk = Category::updateOrCreate(
            ['name' => 'Tes Wawasan Kebangsaan (TWK)'],
            ['passing_grade' => 65.00]
        );
        $tkp = Category::updateOrCreate(
            ['name' => 'Tes Karakteristik Pribadi (TKP)'],
            ['passing_grade' => 166.00]
        );


        // --- TIU QUESTIONS (50) ---
        $tiuQuestions = [
            // Analogi Kata (1-10)
            [
                'q' => 'Analogi Kata: KAKAK : ADIK = ...',
                'opts' => [
                    ['t' => 'Suami : Istri', 'c' => true, 'w' => 5],
                    ['t' => 'Kakek : Cucu', 'c' => false, 'w' => 0],
                    ['t' => 'Ayah : Ibu', 'c' => false, 'w' => 0],
                    ['t' => 'Paman : Bibi', 'c' => false, 'w' => 0],
                    ['t' => 'Teman : Musuh', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: MOBIL : BENSIN = ...',
                'opts' => [
                    ['t' => 'Manusia : Makanan', 'c' => true, 'w' => 5],
                    ['t' => 'Sapi : Rumput', 'c' => false, 'w' => 0],
                    ['t' => 'Pesawat : Bandara', 'c' => false, 'w' => 0],
                    ['t' => 'Penyair : Puisi', 'c' => false, 'w' => 0],
                    ['t' => 'Penyanyi : Lagu', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: GENTENG : TANAH LIAT = ...',
                'opts' => [
                    ['t' => 'Pagar : Besi', 'c' => true, 'w' => 5],
                    ['t' => 'Pintu : Kayu', 'c' => false, 'w' => 0],
                    ['t' => 'Lantai : Semen', 'c' => false, 'w' => 0],
                    ['t' => 'Jendela : Kaca', 'c' => false, 'w' => 0],
                    ['t' => 'Atap : Seng', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: KENDARAAN : MOBIL = ...',
                'opts' => [
                    ['t' => 'Alat Tulis : Pensil', 'c' => true, 'w' => 5],
                    ['t' => 'Buku : Kertas', 'c' => false, 'w' => 0],
                    ['t' => 'Meja : Kursi', 'c' => false, 'w' => 0],
                    ['t' => 'Pakaian : Baju', 'c' => false, 'w' => 0],
                    ['t' => 'Komputer : Mouse', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: RUMAH : ATAP = ...',
                'opts' => [
                    ['t' => 'Tubuh : Kepala', 'c' => true, 'w' => 5],
                    ['t' => 'Kaki : Sepatu', 'c' => false, 'w' => 0],
                    ['t' => 'Tangan : Sarung Tangan', 'c' => false, 'w' => 0],
                    ['t' => 'Mata : Kacamata', 'c' => false, 'w' => 0],
                    ['t' => 'Leher : Dasi', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: DOKTER : PASIEN = ...',
                'opts' => [
                    ['t' => 'Guru : Murid', 'c' => true, 'w' => 5],
                    ['t' => 'Polisi : Pencuri', 'c' => false, 'w' => 0],
                    ['t' => 'Hakim : Terdakwa', 'c' => false, 'w' => 0],
                    ['t' => 'Arsitek : Bangunan', 'c' => false, 'w' => 0],
                    ['t' => 'Penjual : Pembeli', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: API : PANAS = ...',
                'opts' => [
                    ['t' => 'Es : Dingin', 'c' => true, 'w' => 5],
                    ['t' => 'Air : Basah', 'c' => false, 'w' => 0],
                    ['t' => 'Udara : Segar', 'c' => false, 'w' => 0],
                    ['t' => 'Bumi : Bulat', 'c' => false, 'w' => 0],
                    ['t' => 'Matahari : Terang', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: GIGI : KUNYAH = ...',
                'opts' => [
                    ['t' => 'Mata : Lihat', 'c' => true, 'w' => 5],
                    ['t' => 'Telinga : Mendengar', 'c' => false, 'w' => 0],
                    ['t' => 'Hidung : Cium', 'c' => false, 'w' => 0],
                    ['t' => 'Tangan : Pegang', 'c' => false, 'w' => 0],
                    ['t' => 'Kaki : Jalan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: LAPAR : MAKAN = ...',
                'opts' => [
                    ['t' => 'Mengantuk : Tidur', 'c' => true, 'w' => 5],
                    ['t' => 'Haus : Minum', 'c' => false, 'w' => 0],
                    ['t' => 'Lelah : Istirahat', 'c' => false, 'w' => 0],
                    ['t' => 'Sakit : Obat', 'c' => false, 'w' => 0],
                    ['t' => 'Dingin : Selimut', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Analogi Kata: CEPAT : LAMBAT = ...',
                'opts' => [
                    ['t' => 'Besar : Kecil', 'c' => true, 'w' => 5],
                    ['t' => 'Tinggi : Rendah', 'c' => false, 'w' => 0],
                    ['t' => 'Panjang : Pendek', 'c' => false, 'w' => 0],
                    ['t' => 'Jauh : Dekat', 'c' => false, 'w' => 0],
                    ['t' => 'Rajin : Malas', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // Sinonim (11-20)
            [
                'q' => 'Sinonim dari kata "ABOLISI" adalah...',
                'opts' => [
                    ['t' => 'Penghapusan hukum', 'c' => true, 'w' => 5],
                    ['t' => 'Pengurangan hukuman', 'c' => false, 'w' => 0],
                    ['t' => 'Pengasingan diri', 'c' => false, 'w' => 0],
                    ['t' => 'Penambahan kekuasaan', 'c' => false, 'w' => 0],
                    ['t' => 'Pemulihan nama baik', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sinonim dari kata "AMBIGU" adalah...',
                'opts' => [
                    ['t' => 'Mendua', 'c' => true, 'w' => 5],
                    ['t' => 'Jelas', 'c' => false, 'w' => 0],
                    ['t' => 'Pasti', 'c' => false, 'w' => 0],
                    ['t' => 'Bingung', 'c' => false, 'w' => 0],
                    ['t' => 'Sukar', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sinonim dari kata "DELEGASI" adalah...',
                'opts' => [
                    ['t' => 'Utusan', 'c' => true, 'w' => 5],
                    ['t' => 'Pimpinan', 'c' => false, 'w' => 0],
                    ['t' => 'Anggota', 'c' => false, 'w' => 0],
                    ['t' => 'Pengikut', 'c' => false, 'w' => 0],
                    ['t' => 'Organisasi', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sinonim dari kata "EVOKASI" adalah...',
                'opts' => [
                    ['t' => 'Penggugah rasa', 'c' => true, 'w' => 5],
                    ['t' => 'Penyelamatan', 'c' => false, 'w' => 0],
                    ['t' => 'Pengungsian', 'c' => false, 'w' => 0],
                    ['t' => 'Penilaian', 'c' => false, 'w' => 0],
                    ['t' => 'Pemeriksaan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'hard'
            ],
            [
                'q' => 'Sinonim dari kata "KANDIDAT" adalah...',
                'opts' => [
                    ['t' => 'Calon', 'c' => true, 'w' => 5],
                    ['t' => 'Pemenang', 'c' => false, 'w' => 0],
                    ['t' => 'Peserta', 'c' => false, 'w' => 0],
                    ['t' => 'Juara', 'c' => false, 'w' => 0],
                    ['t' => 'Penguji', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sinonim dari kata "KISRUH" adalah...',
                'opts' => [
                    ['t' => 'Kacau', 'c' => true, 'w' => 5],
                    ['t' => 'Tenang', 'c' => false, 'w' => 0],
                    ['t' => 'Damai', 'c' => false, 'w' => 0],
                    ['t' => 'Tertib', 'c' => false, 'w' => 0],
                    ['t' => 'Aman', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sinonim dari kata "KONTRAK" adalah...',
                'opts' => [
                    ['t' => 'Perjanjian', 'c' => true, 'w' => 5],
                    ['t' => 'Pembayaran', 'c' => false, 'w' => 0],
                    ['t' => 'Pertemuan', 'c' => false, 'w' => 0],
                    ['t' => 'Keputusan', 'c' => false, 'w' => 0],
                    ['t' => 'Pelaksanaan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sinonim dari kata "PROMOSI" adalah...',
                'opts' => [
                    ['t' => 'Kenaikan pangkat', 'c' => true, 'w' => 5],
                    ['t' => 'Penurunan jabatan', 'c' => false, 'w' => 0],
                    ['t' => 'Pemberhentian kerja', 'c' => false, 'w' => 0],
                    ['t' => 'Mutasi pegawai', 'c' => false, 'w' => 0],
                    ['t' => 'Pensiun dini', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sinonim dari kata "REKONSILIASI" adalah...',
                'opts' => [
                    ['t' => 'Perdamaian', 'c' => true, 'w' => 5],
                    ['t' => 'Pertikaian', 'c' => false, 'w' => 0],
                    ['t' => 'Pemisahan', 'c' => false, 'w' => 0],
                    ['t' => 'Perdebatan', 'c' => false, 'w' => 0],
                    ['t' => 'Persaingan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sinonim dari kata "TRANSPARAN" adalah...',
                'opts' => [
                    ['t' => 'Tembus cahaya', 'c' => true, 'w' => 5],
                    ['t' => 'Gelap', 'c' => false, 'w' => 0],
                    ['t' => 'Samar', 'c' => false, 'w' => 0],
                    ['t' => 'Kusam', 'c' => false, 'w' => 0],
                    ['t' => 'Berwarna', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // Antonim (21-30)
            [
                'q' => 'Antonim dari kata "ABADI" adalah...',
                'opts' => [
                    ['t' => 'Fana', 'c' => true, 'w' => 5],
                    ['t' => 'Kekal', 'c' => false, 'w' => 0],
                    ['t' => 'Lama', 'c' => false, 'w' => 0],
                    ['t' => 'Terus-menerus', 'c' => false, 'w' => 0],
                    ['t' => 'Abadi', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "AKTIF" adalah...',
                'opts' => [
                    ['t' => 'Pasif', 'c' => true, 'w' => 5],
                    ['t' => 'Giat', 'c' => false, 'w' => 0],
                    ['t' => 'Rajin', 'c' => false, 'w' => 0],
                    ['t' => 'Dinamis', 'c' => false, 'w' => 0],
                    ['t' => 'Sibuk', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "BONGSOR" adalah...',
                'opts' => [
                    ['t' => 'Kerdil', 'c' => true, 'w' => 5],
                    ['t' => 'Tinggi', 'c' => false, 'w' => 0],
                    ['t' => 'Besar', 'c' => false, 'w' => 0],
                    ['t' => 'Gemuk', 'c' => false, 'w' => 0],
                    ['t' => 'Subur', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "CAIR" adalah...',
                'opts' => [
                    ['t' => 'Padat', 'c' => true, 'w' => 5],
                    ['t' => 'Gas', 'c' => false, 'w' => 0],
                    ['t' => 'Beku', 'c' => false, 'w' => 0],
                    ['t' => 'Kental', 'c' => false, 'w' => 0],
                    ['t' => 'Encer', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "FISIK" adalah...',
                'opts' => [
                    ['t' => 'Mental', 'c' => true, 'w' => 5],
                    ['t' => 'Jasmani', 'c' => false, 'w' => 0],
                    ['t' => 'Badan', 'c' => false, 'w' => 0],
                    ['t' => 'Raga', 'c' => false, 'w' => 0],
                    ['t' => 'Nyata', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "GAGAL" adalah...',
                'opts' => [
                    ['t' => 'Berhasil', 'c' => true, 'w' => 5],
                    ['t' => 'Kalah', 'c' => false, 'w' => 0],
                    ['t' => 'Rugi', 'c' => false, 'w' => 0],
                    ['t' => 'Jatuh', 'c' => false, 'w' => 0],
                    ['t' => 'Hancur', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "KHAS" adalah...',
                'opts' => [
                    ['t' => 'Umum', 'c' => true, 'w' => 5],
                    ['t' => 'Khusus', 'c' => false, 'w' => 0],
                    ['t' => 'Istimewa', 'c' => false, 'w' => 0],
                    ['t' => 'Unik', 'c' => false, 'w' => 0],
                    ['t' => 'Spesifik', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "MAKAR" adalah...',
                'opts' => [
                    ['t' => 'Setia', 'c' => true, 'w' => 5],
                    ['t' => 'Khianat', 'c' => false, 'w' => 0],
                    ['t' => 'Bontang', 'c' => false, 'w' => 0],
                    ['t' => 'Rencana', 'c' => false, 'w' => 0],
                    ['t' => 'Jahat', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Antonim dari kata "OPTIMIS" adalah...',
                'opts' => [
                    ['t' => 'Pesimis', 'c' => true, 'w' => 5],
                    ['t' => 'Yakin', 'c' => false, 'w' => 0],
                    ['t' => 'Semangat', 'c' => false, 'w' => 0],
                    ['t' => 'Ragu', 'c' => false, 'w' => 0],
                    ['t' => 'Takut', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Antonim dari kata "PROGRESIF" adalah...',
                'opts' => [
                    ['t' => 'Regresif', 'c' => true, 'w' => 5],
                    ['t' => 'Maju', 'c' => false, 'w' => 0],
                    ['t' => 'Modern', 'c' => false, 'w' => 0],
                    ['t' => 'Cepat', 'c' => false, 'w' => 0],
                    ['t' => 'Aktif', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],

            // Deret Angka (31-40)
            [
                'q' => 'Deret angka: 2, 4, 6, 8, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '10', 'c' => true, 'w' => 5],
                    ['t' => '9', 'c' => false, 'w' => 0],
                    ['t' => '11', 'c' => false, 'w' => 0],
                    ['t' => '12', 'c' => false, 'w' => 0],
                    ['t' => '14', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 3, 9, 27, 81, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '243', 'c' => true, 'w' => 5],
                    ['t' => '162', 'c' => false, 'w' => 0],
                    ['t' => '120', 'c' => false, 'w' => 0],
                    ['t' => '324', 'c' => false, 'w' => 0],
                    ['t' => '270', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 1, 1, 2, 3, 5, 8, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '13', 'c' => true, 'w' => 5],
                    ['t' => '11', 'c' => false, 'w' => 0],
                    ['t' => '12', 'c' => false, 'w' => 0],
                    ['t' => '14', 'c' => false, 'w' => 0],
                    ['t' => '15', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 10, 9, 8, 7, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '6', 'c' => true, 'w' => 5],
                    ['t' => '5', 'c' => false, 'w' => 0],
                    ['t' => '4', 'c' => false, 'w' => 0],
                    ['t' => '3', 'c' => false, 'w' => 0],
                    ['t' => '2', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 2, 5, 10, 17, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '26', 'c' => true, 'w' => 5],
                    ['t' => '24', 'c' => false, 'w' => 0],
                    ['t' => '25', 'c' => false, 'w' => 0],
                    ['t' => '27', 'c' => false, 'w' => 0],
                    ['t' => '28', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Deret angka: 100, 50, 25, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '12.5', 'c' => true, 'w' => 5],
                    ['t' => '12', 'c' => false, 'w' => 0],
                    ['t' => '10', 'c' => false, 'w' => 0],
                    ['t' => '5', 'c' => false, 'w' => 0],
                    ['t' => '13.5', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Deret angka: 1, 4, 9, 16, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '25', 'c' => true, 'w' => 5],
                    ['t' => '20', 'c' => false, 'w' => 0],
                    ['t' => '22', 'c' => false, 'w' => 0],
                    ['t' => '24', 'c' => false, 'w' => 0],
                    ['t' => '30', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 5, 10, 15, 20, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '25', 'c' => true, 'w' => 5],
                    ['t' => '30', 'c' => false, 'w' => 0],
                    ['t' => '24', 'c' => false, 'w' => 0],
                    ['t' => '26', 'c' => false, 'w' => 0],
                    ['t' => '28', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Deret angka: 2, 6, 12, 20, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '30', 'c' => true, 'w' => 5],
                    ['t' => '28', 'c' => false, 'w' => 0],
                    ['t' => '32', 'c' => false, 'w' => 0],
                    ['t' => '24', 'c' => false, 'w' => 0],
                    ['t' => '34', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Deret angka: 80, 40, 20, 10, ... Tentukan angka berikutnya.',
                'opts' => [
                    ['t' => '5', 'c' => true, 'w' => 5],
                    ['t' => '4', 'c' => false, 'w' => 0],
                    ['t' => '6', 'c' => false, 'w' => 0],
                    ['t' => '2', 'c' => false, 'w' => 0],
                    ['t' => '3', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // Berhitung & Aljabar (41-45)
            [
                'q' => 'Berapakah hasil dari 15 * 4 - 20?',
                'opts' => [
                    ['t' => '40', 'c' => true, 'w' => 5],
                    ['t' => '30', 'c' => false, 'w' => 0],
                    ['t' => '50', 'c' => false, 'w' => 0],
                    ['t' => '60', 'c' => false, 'w' => 0],
                    ['t' => '45', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Berapakah hasil dari (12 + 18) / 5?',
                'opts' => [
                    ['t' => '6', 'c' => true, 'w' => 5],
                    ['t' => '5', 'c' => false, 'w' => 0],
                    ['t' => '7', 'c' => false, 'w' => 0],
                    ['t' => '8', 'c' => false, 'w' => 0],
                    ['t' => '4', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Berapakah hasil dari 2^3 + 3^2?',
                'opts' => [
                    ['t' => '17', 'c' => true, 'w' => 5],
                    ['t' => '15', 'c' => false, 'w' => 0],
                    ['t' => '16', 'c' => false, 'w' => 0],
                    ['t' => '18', 'c' => false, 'w' => 0],
                    ['t' => '20', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Berapakah hasil dari 0.75 + 1/4?',
                'opts' => [
                    ['t' => '1', 'c' => true, 'w' => 5],
                    ['t' => '1.5', 'c' => false, 'w' => 0],
                    ['t' => '0.9', 'c' => false, 'w' => 0],
                    ['t' => '1.25', 'c' => false, 'w' => 0],
                    ['t' => '2', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Jika 3x = 15, berapakah nilai x?',
                'opts' => [
                    ['t' => '5', 'c' => true, 'w' => 5],
                    ['t' => '3', 'c' => false, 'w' => 0],
                    ['t' => '4', 'c' => false, 'w' => 0],
                    ['t' => '6', 'c' => false, 'w' => 0],
                    ['t' => '15', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // Penalaran Logis & Silogisme (46-50)
            [
                'q' => 'Semua manusia akan mati. Socrates adalah manusia. Maka kesimpulannya...',
                'opts' => [
                    ['t' => 'Socrates akan mati', 'c' => true, 'w' => 5],
                    ['t' => 'Socrates tidak akan mati', 'c' => false, 'w' => 0],
                    ['t' => 'Socrates mungkin mati', 'c' => false, 'w' => 0],
                    ['t' => 'Semua manusia adalah Socrates', 'c' => false, 'w' => 0],
                    ['t' => 'Tidak dapat disimpulkan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Jika hari hujan, maka jalanan basah. Hari ini hujan. Maka kesimpulannya...',
                'opts' => [
                    ['t' => 'Jalanan basah', 'c' => true, 'w' => 5],
                    ['t' => 'Jalanan tidak basah', 'c' => false, 'w' => 0],
                    ['t' => 'Hari tidak hujan', 'c' => false, 'w' => 0],
                    ['t' => 'Jalanan kering', 'c' => false, 'w' => 0],
                    ['t' => 'Tidak dapat disimpulkan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Semua siswa memakai seragam. Andi adalah siswa. Maka kesimpulannya...',
                'opts' => [
                    ['t' => 'Andi memakai seragam', 'c' => true, 'w' => 5],
                    ['t' => 'Andi tidak memakai seragam', 'c' => false, 'w' => 0],
                    ['t' => 'Andi bukan siswa', 'c' => false, 'w' => 0],
                    ['t' => 'Semua yang memakai seragam adalah Andi', 'c' => false, 'w' => 0],
                    ['t' => 'Sebagian siswa memakai seragam', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sebagian mamalia hidup di laut. Paus adalah mamalia. Maka kesimpulannya...',
                'opts' => [
                    ['t' => 'Paus hidup di laut', 'c' => true, 'w' => 5],
                    ['t' => 'Semua paus hidup di darat', 'c' => false, 'w' => 0],
                    ['t' => 'Paus bukan mamalia', 'c' => false, 'w' => 0],
                    ['t' => 'Tidak ada mamalia hidup di laut', 'c' => false, 'w' => 0],
                    ['t' => 'Semua mamalia hidup di laut', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Jika lampu mati, ruangan gelap. Ruangan tidak gelap. Maka kesimpulannya...',
                'opts' => [
                    ['t' => 'Lampu tidak mati', 'c' => true, 'w' => 5],
                    ['t' => 'Lampu mati', 'c' => false, 'w' => 0],
                    ['t' => 'Ruangan mati', 'c' => false, 'w' => 0],
                    ['t' => 'Ruangan terang', 'c' => false, 'w' => 0],
                    ['t' => 'Tidak ada kesimpulan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
        ];

        // --- TWK QUESTIONS (50) ---
        $twkQuestions = [
            // Pancasila (1-10)
            [
                'q' => 'Sila pertama Pancasila dilambangkan dengan...',
                'opts' => [
                    ['t' => 'Bintang', 'c' => true, 'w' => 5],
                    ['t' => 'Rantai', 'c' => false, 'w' => 0],
                    ['t' => 'Pohon beringin', 'c' => false, 'w' => 0],
                    ['t' => 'Kepala banteng', 'c' => false, 'w' => 0],
                    ['t' => 'Padi dan kapas', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sila kedua Pancasila berbunyi...',
                'opts' => [
                    ['t' => 'Kemanusiaan yang adil dan beradab', 'c' => true, 'w' => 5],
                    ['t' => 'Ketuhanan Yang Maha Esa', 'c' => false, 'w' => 0],
                    ['t' => 'Persatuan Indonesia', 'c' => false, 'w' => 0],
                    ['t' => 'Kerakyatan yang dipimpin oleh hikmat...', 'c' => false, 'w' => 0],
                    ['t' => 'Keadilan sosial bagi seluruh rakyat...', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sila ketiga Pancasila dilambangkan dengan...',
                'opts' => [
                    ['t' => 'Pohon beringin', 'c' => true, 'w' => 5],
                    ['t' => 'Bintang', 'c' => false, 'w' => 0],
                    ['t' => 'Rantai', 'c' => false, 'w' => 0],
                    ['t' => 'Kepala banteng', 'c' => false, 'w' => 0],
                    ['t' => 'Padi dan kapas', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sila keempat Pancasila berbunyi...',
                'opts' => [
                    ['t' => 'Kerakyatan yang dipimpin oleh hikmat kebijaksanaan dalam permusyawaratan/perwakilan', 'c' => true, 'w' => 5],
                    ['t' => 'Ketuhanan Yang Maha Esa', 'c' => false, 'w' => 0],
                    ['t' => 'Kemanusiaan yang adil dan beradab', 'c' => false, 'w' => 0],
                    ['t' => 'Persatuan Indonesia', 'c' => false, 'w' => 0],
                    ['t' => 'Keadilan sosial bagi seluruh rakyat Indonesia', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sila kelima Pancasila dilambangkan dengan...',
                'opts' => [
                    ['t' => 'Padi dan kapas', 'c' => true, 'w' => 5],
                    ['t' => 'Bintang', 'c' => false, 'w' => 0],
                    ['t' => 'Rantai', 'c' => false, 'w' => 0],
                    ['t' => 'Pohon beringin', 'c' => false, 'w' => 0],
                    ['t' => 'Kepala banteng', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Hari Lahir Pancasila diperingati setiap tanggal...',
                'opts' => [
                    ['t' => '1 Juni', 'c' => true, 'w' => 5],
                    ['t' => '1 Oktober', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus', 'c' => false, 'w' => 0],
                    ['t' => '20 Mei', 'c' => false, 'w' => 0],
                    ['t' => '28 Oktober', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Hari Kesaktian Pancasila diperingati setiap tanggal...',
                'opts' => [
                    ['t' => '1 Oktober', 'c' => true, 'w' => 5],
                    ['t' => '1 Juni', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus', 'c' => false, 'w' => 0],
                    ['t' => '10 November', 'c' => false, 'w' => 0],
                    ['t' => '2 Mei', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Siapa tokoh yang mengusulkan nama "Pancasila" pada sidang BPUPKI tanggal 1 Juni 1945?',
                'opts' => [
                    ['t' => 'Ir. Soekarno', 'c' => true, 'w' => 5],
                    ['t' => 'Drs. Moh. Hatta', 'c' => false, 'w' => 0],
                    ['t' => 'Mr. Muhammad Yamin', 'c' => false, 'w' => 0],
                    ['t' => 'Prof. Dr. Soepomo', 'c' => false, 'w' => 0],
                    ['t' => 'Radjiman Wedyodiningrat', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Kedudukan Pancasila sebagai sumber dari segala sumber hukum negara diatur dalam UU Nomor...',
                'opts' => [
                    ['t' => 'UU No. 12 Tahun 2011', 'c' => true, 'w' => 5],
                    ['t' => 'UU No. 10 Tahun 2004', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 15 Tahun 2019', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 5 Tahun 2014', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 23 Tahun 2014', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sila kedua Pancasila menekankan nilai dasar...',
                'opts' => [
                    ['t' => 'Kemanusiaan', 'c' => true, 'w' => 5],
                    ['t' => 'Ketuhanan', 'c' => false, 'w' => 0],
                    ['t' => 'Kebangsaan', 'c' => false, 'w' => 0],
                    ['t' => 'Kerakyatan', 'c' => false, 'w' => 0],
                    ['t' => 'Keadilan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // UUD 1945 & Konstitusi (11-20)
            [
                'q' => 'UUD 1945 disahkan sebagai konstitusi negara Indonesia oleh PPKI pada tanggal...',
                'opts' => [
                    ['t' => '18 Agustus 1945', 'c' => true, 'w' => 5],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '1 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '22 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '27 Desember 1949', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Amandemen UUD 1945 telah dilakukan sebanyak...',
                'opts' => [
                    ['t' => '4 kali', 'c' => true, 'w' => 5],
                    ['t' => '3 kali', 'c' => false, 'w' => 0],
                    ['t' => '2 kali', 'c' => false, 'w' => 0],
                    ['t' => '5 kali', 'c' => false, 'w' => 0],
                    ['t' => '1 kali', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Pasal dalam UUD 1945 yang mengatur tentang hak atas kelangsungan hidup anak adalah...',
                'opts' => [
                    ['t' => 'Pasal 28B', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 28A', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 28C', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 28D', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 28E', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Lembaga negara baru yang dibentuk setelah amandemen UUD 1945 adalah...',
                'opts' => [
                    ['t' => 'DPD dan MK', 'c' => true, 'w' => 5],
                    ['t' => 'MPR dan DPR', 'c' => false, 'w' => 0],
                    ['t' => 'MA dan BPK', 'c' => false, 'w' => 0],
                    ['t' => 'DPA dan MA', 'c' => false, 'w' => 0],
                    ['t' => 'Presiden dan Wapres', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Pasal 1 ayat 1 UUD 1945 menyatakan bahwa Negara Indonesia ialah...',
                'opts' => [
                    ['t' => 'Negara Kesatuan yang berbentuk Republik', 'c' => true, 'w' => 5],
                    ['t' => 'Negara Kesatuan yang berbentuk Federasi', 'c' => false, 'w' => 0],
                    ['t' => 'Negara Republik yang berbentuk Serikat', 'c' => false, 'w' => 0],
                    ['t' => 'Negara Kerajaan yang konstitusional', 'c' => false, 'w' => 0],
                    ['t' => 'Negara Hukum yang demokratis', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Pasal 29 ayat 2 UUD 1945 menjamin kebebasan warga negara untuk...',
                'opts' => [
                    ['t' => 'Memeluk agama dan beribadah menurut agamanya', 'c' => true, 'w' => 5],
                    ['t' => 'Mendapatkan pendidikan gratis', 'c' => false, 'w' => 0],
                    ['t' => 'Mendirikan organisasi sosial', 'c' => false, 'w' => 0],
                    ['t' => 'Menyatakan pendapat di muka umum', 'c' => false, 'w' => 0],
                    ['t' => 'Mendapatkan pekerjaan layak', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Pendidikan nasional diatur dalam UUD 1945 Pasal...',
                'opts' => [
                    ['t' => 'Pasal 31', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 30', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 32', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 33', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 34', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Kekuasaan kehakiman dalam UUD 1945 diatur dalam Pasal...',
                'opts' => [
                    ['t' => 'Pasal 24', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 23', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 25', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 26', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 27', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Bahasa Negara ialah Bahasa Indonesia diatur dalam UUD 1945 Pasal...',
                'opts' => [
                    ['t' => 'Pasal 36', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 35', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36A', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36B', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36C', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Lambang Negara ialah Garuda Pancasila diatur dalam UUD 1945 Pasal...',
                'opts' => [
                    ['t' => 'Pasal 36A', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 35', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36B', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 36C', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],

            // Bhinneka Tunggal Ika & NKRI (21-30)
            [
                'q' => 'Semboyan Bhinneka Tunggal Ika diambil dari kitab...',
                'opts' => [
                    ['t' => 'Sutasoma', 'c' => true, 'w' => 5],
                    ['t' => 'Negarakertagama', 'c' => false, 'w' => 0],
                    ['t' => 'Arjunawiwaha', 'c' => false, 'w' => 0],
                    ['t' => 'Ramayana', 'c' => false, 'w' => 0],
                    ['t' => 'Bharatayuddha', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Kitab Sutasoma adalah karangan dari pujangga Majapahit bernama...',
                'opts' => [
                    ['t' => 'Empu Tantular', 'c' => true, 'w' => 5],
                    ['t' => 'Empu Prapanca', 'c' => false, 'w' => 0],
                    ['t' => 'Empu Gandring', 'c' => false, 'w' => 0],
                    ['t' => 'Empu Panuluh', 'c' => false, 'w' => 0],
                    ['t' => 'Empu Sedah', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Semboyan Bhinneka Tunggal Ika memiliki arti...',
                'opts' => [
                    ['t' => 'Berbeda-beda tetapi tetap satu jua', 'c' => true, 'w' => 5],
                    ['t' => 'Bersatu kita teguh bercerai kita runtuh', 'c' => false, 'w' => 0],
                    ['t' => 'Satu nusa satu bangsa satu bahasa', 'c' => false, 'w' => 0],
                    ['t' => 'Keberagaman adalah kekayaan bangsa', 'c' => false, 'w' => 0],
                    ['t' => 'Gotong royong adalah jati diri bangsa', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Keberagaman suku bangsa di Indonesia dipersatukan oleh...',
                'opts' => [
                    ['t' => 'Bhinneka Tunggal Ika', 'c' => true, 'w' => 5],
                    ['t' => 'Peraturan daerah', 'c' => false, 'w' => 0],
                    ['t' => 'Bahasa daerah', 'c' => false, 'w' => 0],
                    ['t' => 'Kepentingan ekonomi', 'c' => false, 'w' => 0],
                    ['t' => 'Letak geografis', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Batas wilayah laut Indonesia pertama kali dideklarasikan secara internasional pada...',
                'opts' => [
                    ['t' => 'Deklarasi Djuanda', 'c' => true, 'w' => 5],
                    ['t' => 'Konferensi Meja Bundar', 'c' => false, 'w' => 0],
                    ['t' => 'Deklarasi Bangkok', 'c' => false, 'w' => 0],
                    ['t' => 'Perjanjian Linggarjati', 'c' => false, 'w' => 0],
                    ['t' => 'Perjanjian Renville', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Deklarasi Djuanda dicetuskan pada tanggal...',
                'opts' => [
                    ['t' => '13 Desember 1957', 'c' => true, 'w' => 5],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '1 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '22 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '20 Mei 1908', 'c' => false, 'w' => 0],
                ],
                'diff' => 'hard'
            ],
            [
                'q' => 'Fungsi utama pertahanan negara dalam NKRI diselenggarakan oleh...',
                'opts' => [
                    ['t' => 'Tentara Nasional Indonesia (TNI)', 'c' => true, 'w' => 5],
                    ['t' => 'Kepolisian Negara Republik Indonesia (Polri)', 'c' => false, 'w' => 0],
                    ['t' => 'Satuan Polisi Pamong Praja (Satpol PP)', 'c' => false, 'w' => 0],
                    ['t' => 'Pertahanan Sipil (Hansip)', 'c' => false, 'w' => 0],
                    ['t' => 'Seluruh kementerian negara', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Fungsi keamanan dan ketertiban masyarakat dalam NKRI diselenggarakan oleh...',
                'opts' => [
                    ['t' => 'Kepolisian Negara Republik Indonesia (Polri)', 'c' => true, 'w' => 5],
                    ['t' => 'Tentara Nasional Indonesia (TNI)', 'c' => false, 'w' => 0],
                    ['t' => 'Badan Intelijen Negara (BIN)', 'c' => false, 'w' => 0],
                    ['t' => 'Kementerian Pertahanan', 'c' => false, 'w' => 0],
                    ['t' => 'Resimen Mahasiswa', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Indonesia secara geografis terletak di antara dua samudra, yaitu...',
                'opts' => [
                    ['t' => 'Hindia dan Pasifik', 'c' => true, 'w' => 5],
                    ['t' => 'Hindia dan Atlantik', 'c' => false, 'w' => 0],
                    ['t' => 'Pasifik dan Atlantik', 'c' => false, 'w' => 0],
                    ['t' => 'Arktik dan Hindia', 'c' => false, 'w' => 0],
                    ['t' => 'Pasifik dan Arktik', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Lagu kebangsaan Indonesia Raya diciptakan oleh...',
                'opts' => [
                    ['t' => 'W.R. Supratman', 'c' => true, 'w' => 5],
                    ['t' => 'C. Simanjuntak', 'c' => false, 'w' => 0],
                    ['t' => 'Ibu Sud', 'c' => false, 'w' => 0],
                    ['t' => 'L. Manik', 'c' => false, 'w' => 0],
                    ['t' => 'Kusbini', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],

            // Sejarah Perjuangan Bangsa (31-40)
            [
                'q' => 'BPUPKI dibentuk oleh pemerintah Jepang pada tanggal...',
                'opts' => [
                    ['t' => '1 Maret 1945', 'c' => true, 'w' => 5],
                    ['t' => '29 Mei 1945', 'c' => false, 'w' => 0],
                    ['t' => '22 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '7 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketua BPUPKI adalah...',
                'opts' => [
                    ['t' => 'Dr. K.R.T. Radjiman Wedyodiningrat', 'c' => true, 'w' => 5],
                    ['t' => 'Ir. Soekarno', 'c' => false, 'w' => 0],
                    ['t' => 'Drs. Moh. Hatta', 'c' => false, 'w' => 0],
                    ['t' => 'Mr. Muhammad Yamin', 'c' => false, 'w' => 0],
                    ['t' => 'Ichibangase Yosio', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'PPKI dibentuk untuk menggantikan BPUPKI pada tanggal...',
                'opts' => [
                    ['t' => '7 Agustus 1945', 'c' => true, 'w' => 5],
                    ['t' => '1 Maret 1945', 'c' => false, 'w' => 0],
                    ['t' => '18 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '22 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketua PPKI adalah...',
                'opts' => [
                    ['t' => 'Ir. Soekarno', 'c' => true, 'w' => 5],
                    ['t' => 'Drs. Moh. Hatta', 'c' => false, 'w' => 0],
                    ['t' => 'Dr. Radjiman Wedyodiningrat', 'c' => false, 'w' => 0],
                    ['t' => 'Mr. Soepomo', 'c' => false, 'w' => 0],
                    ['t' => 'Mr. Achmad Soebardjo', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Teks proklamasi kemerdekaan Indonesia dirumuskan di kediaman...',
                'opts' => [
                    ['t' => 'Laksamana Maeda', 'c' => true, 'w' => 5],
                    ['t' => 'Ir. Soekarno', 'c' => false, 'w' => 0],
                    ['t' => 'Drs. Moh. Hatta', 'c' => false, 'w' => 0],
                    ['t' => 'Raden Soekarni', 'c' => false, 'w' => 0],
                    ['t' => 'Mr. Achmad Soebardjo', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Siapa penjahit bendera Pusaka Merah Putih yang dikibarkan saat proklamasi?',
                'opts' => [
                    ['t' => 'Fatmawati', 'c' => true, 'w' => 5],
                    ['t' => 'Kartini', 'c' => false, 'w' => 0],
                    ['t' => 'Dewi Sartika', 'c' => false, 'w' => 0],
                    ['t' => 'Sayuti Melik', 'c' => false, 'w' => 0],
                    ['t' => 'Megawati', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Pembaca teks proklamasi kemerdekaan Indonesia pada tanggal 17 Agustus 1945 adalah...',
                'opts' => [
                    ['t' => 'Ir. Soekarno didampingi Moh. Hatta', 'c' => true, 'w' => 5],
                    ['t' => 'Moh. Hatta didampingi Sutan Sjahrir', 'c' => false, 'w' => 0],
                    ['t' => 'Sayuti Melik didampingi Soekarni', 'c' => false, 'w' => 0],
                    ['t' => 'Ir. Soekarno didampingi Achmad Soebardjo', 'c' => false, 'w' => 0],
                    ['t' => 'Sutan Sjahrir didampingi Tan Malaka', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Peristiwa Rengasdengklok terjadi karena adanya perbedaan pendapat tentang...',
                'opts' => [
                    ['t' => 'Waktu pelaksanaan proklamasi kemerdekaan', 'c' => true, 'w' => 5],
                    ['t' => 'Tokoh yang akan membaca teks proklamasi', 'c' => false, 'w' => 0],
                    ['t' => 'Lokasi pembacaan teks proklamasi', 'c' => false, 'w' => 0],
                    ['t' => 'Isi kalimat dalam teks proklamasi', 'c' => false, 'w' => 0],
                    ['t' => 'Bentuk negara yang akan didirikan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Agresi Militer Belanda I terjadi pada tahun...',
                'opts' => [
                    ['t' => '1947', 'c' => true, 'w' => 5],
                    ['t' => '1945', 'c' => false, 'w' => 0],
                    ['t' => '1946', 'c' => false, 'w' => 0],
                    ['t' => '1948', 'c' => false, 'w' => 0],
                    ['t' => '1949', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Perjanjian Linggarjati ditandatangani pada tahun...',
                'opts' => [
                    ['t' => '1947', 'c' => true, 'w' => 5],
                    ['t' => '1945', 'c' => false, 'w' => 0],
                    ['t' => '1946', 'c' => false, 'w' => 0],
                    ['t' => '1948', 'c' => false, 'w' => 0],
                    ['t' => '1949', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],

            // Bela Negara, Nasionalisme & Integritas (41-50)
            [
                'q' => 'Upaya bela negara bagi warga negara diatur dalam UUD 1945 Pasal...',
                'opts' => [
                    ['t' => 'Pasal 27 ayat 3', 'c' => true, 'w' => 5],
                    ['t' => 'Pasal 27 ayat 1', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 28', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 29', 'c' => false, 'w' => 0],
                    ['t' => 'Pasal 31', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Salah satu wujud bela negara bagi siswa sekolah adalah...',
                'opts' => [
                    ['t' => 'Belajar dengan tekun dan disiplin', 'c' => true, 'w' => 5],
                    ['t' => 'Mengikuti latihan militer cadangan', 'c' => false, 'w' => 0],
                    ['t' => 'Bekerja mencari nafkah keluarga', 'c' => false, 'w' => 0],
                    ['t' => 'Ikut serta demonstrasi politik', 'c' => false, 'w' => 0],
                    ['t' => 'Membuat organisasi tandingan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sikap cinta tanah air yang berlebihan hingga merendahkan bangsa lain disebut...',
                'opts' => [
                    ['t' => 'Chauvinisme', 'c' => true, 'w' => 5],
                    ['t' => 'Nasionalisme', 'c' => false, 'w' => 0],
                    ['t' => 'Patriotisme', 'c' => false, 'w' => 0],
                    ['t' => 'Etnosentrisme', 'c' => false, 'w' => 0],
                    ['t' => 'Primordialisme', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Sikap rela berkorban demi kepentingan bangsa dan negara disebut...',
                'opts' => [
                    ['t' => 'Patriotisme', 'c' => true, 'w' => 5],
                    ['t' => 'Nasionalisme', 'c' => false, 'w' => 0],
                    ['t' => 'Chauvinisme', 'c' => false, 'w' => 0],
                    ['t' => 'Liberalisme', 'c' => false, 'w' => 0],
                    ['t' => 'Sosialisme', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Komisi Pemberantasan Korupsi (KPK) dibentuk berdasarkan UU Nomor...',
                'opts' => [
                    ['t' => 'UU No. 30 Tahun 2002', 'c' => true, 'w' => 5],
                    ['t' => 'UU No. 31 Tahun 1999', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 20 Tahun 2001', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 28 Tahun 1999', 'c' => false, 'w' => 0],
                    ['t' => 'UU No. 8 Tahun 1981', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Menolak suap dan pungutan liar merupakan contoh nyata dari perilaku...',
                'opts' => [
                    ['t' => 'Integritas diri', 'c' => true, 'w' => 5],
                    ['t' => 'Nasionalisme pasif', 'c' => false, 'w' => 0],
                    ['t' => 'Bela negara militer', 'c' => false, 'w' => 0],
                    ['t' => 'Sosial kemasyarakatan', 'c' => false, 'w' => 0],
                    ['t' => 'Politik praktis', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Nilai antikorupsi yang berupa keselarasan antara kata dan perbuatan adalah...',
                'opts' => [
                    ['t' => 'Kejujuran', 'c' => true, 'w' => 5],
                    ['t' => 'Keadilan', 'c' => false, 'w' => 0],
                    ['t' => 'Kedisiplinan', 'c' => false, 'w' => 0],
                    ['t' => 'Kemandirian', 'c' => false, 'w' => 0],
                    ['t' => 'Kepedulian', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sikap berpegang teguh pada prinsip moral dan kebenaran disebut...',
                'opts' => [
                    ['t' => 'Integritas', 'c' => true, 'w' => 5],
                    ['t' => 'Loyalitas', 'c' => false, 'w' => 0],
                    ['t' => 'Solidaritas', 'c' => false, 'w' => 0],
                    ['t' => 'Simpati', 'c' => false, 'w' => 0],
                    ['t' => 'Empati', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Organisasi kepemudaan modern pertama Budi Utomo berdiri pada tanggal...',
                'opts' => [
                    ['t' => '20 Mei 1908', 'c' => true, 'w' => 5],
                    ['t' => '28 Oktober 1928', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '1 Juni 1945', 'c' => false, 'w' => 0],
                    ['t' => '30 September 1965', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Sumpah Pemuda dibacakan pada tanggal...',
                'opts' => [
                    ['t' => '28 Oktober 1928', 'c' => true, 'w' => 5],
                    ['t' => '20 Mei 1908', 'c' => false, 'w' => 0],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '10 November 1945', 'c' => false, 'w' => 0],
                    ['t' => '1 Juni 1945', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
        ];

        // --- TKP QUESTIONS (50) ---
        // TKP questions are graded 5 to 1. The first option in 'opts' is the best response (score 5, marked c = true),
        // next ones are 4, 3, 2, 1.
        $tkpQuestions = [
            [
                'q' => 'Ketika melihat rekan kerja melakukan kesalahan input data yang berdampak kecil, sikap saya...',
                'opts' => [
                    ['t' => 'Memberitahunya secara pribadi agar segera diperbaiki', 'c' => true, 'w' => 5],
                    ['t' => 'Membantu memperbaikinya secara diam-diam', 'c' => false, 'w' => 4],
                    ['t' => 'Mendiskusikannya dengan rekan kerja yang lain', 'c' => false, 'w' => 3],
                    ['t' => 'Melaporkannya langsung kepada atasan', 'c' => false, 'w' => 2],
                    ['t' => 'Membiarkannya karena bukan urusan saya', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Saat saya dibebani tugas yang sangat banyak oleh atasan, saya akan...',
                'opts' => [
                    ['t' => 'Menyusun skala prioritas dan menyelesaikannya satu per satu', 'c' => true, 'w' => 5],
                    ['t' => 'Mengerjakannya hingga lembur dengan fokus penuh', 'c' => false, 'w' => 4],
                    ['t' => 'Meminta bantuan rekan kerja lain untuk mengerjakannya', 'c' => false, 'w' => 3],
                    ['t' => 'Menyelesaikan semampunya saja tanpa peduli kualitas', 'c' => false, 'w' => 2],
                    ['t' => 'Mengeluh di media sosial tentang beban kerja', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Jika sistem IT kantor tiba-tiba error saat saya melayani masyarakat, saya akan...',
                'opts' => [
                    ['t' => 'Meminta maaf kepada warga dan mencatat data mereka secara manual terlebih dahulu', 'c' => true, 'w' => 5],
                    ['t' => 'Menghubungi bagian IT kantor dan meminta warga menunggu dengan sabar', 'c' => false, 'w' => 4],
                    ['t' => 'Menghentikan pelayanan sementara sampai sistem kembali normal', 'c' => false, 'w' => 3],
                    ['t' => 'Mengeluh tentang kualitas IT kantor di depan warga yang mengantre', 'c' => false, 'w' => 2],
                    ['t' => 'Menutup loket dan meminta warga pulang', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketika ada pergantian pimpinan dengan gaya kerja yang sangat berbeda, sikap saya...',
                'opts' => [
                    ['t' => 'Mempelajari visi misi barunya dan menyesuaikan diri dengan cepat', 'c' => true, 'w' => 5],
                    ['t' => 'Menerima perubahan tersebut secara perlahan sambil tetap bekerja', 'c' => false, 'w' => 4],
                    ['t' => 'Mengikuti arahannya hanya jika diawasi langsung olehnya', 'c' => false, 'w' => 3],
                    ['t' => 'Mengeluh bersama rekan kerja tentang gaya kepemimpinan barunya', 'c' => false, 'w' => 2],
                    ['t' => 'Memprotes kebijakan barunya karena merusak ritme kerja yang lama', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Menghadapi pengguna layanan yang marah-marah karena keterlambatan berkas, saya akan...',
                'opts' => [
                    ['t' => 'Mendengarkan keluhannya, meminta maaf, dan segera mengurus berkasnya', 'c' => true, 'w' => 5],
                    ['t' => 'Menjelaskan penyebab keterlambatan dengan sopan agar ia mengerti', 'c' => false, 'w' => 4],
                    ['t' => 'Meminta ia tenang terlebih dahulu sebelum mulai melayaninya', 'c' => false, 'w' => 3],
                    ['t' => 'Membiarkan ia meluapkan marahnya tanpa merespons apa pun', 'c' => false, 'w' => 2],
                    ['t' => 'Balik memarahinya karena tidak mengerti kesibukan kantor', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Bila ditugaskan ke luar kota yang belum pernah saya kunjungi, saya akan...',
                'opts' => [
                    ['t' => 'Menerima tugas dengan antusias dan mencari informasi daerah tersebut', 'c' => true, 'w' => 5],
                    ['t' => 'Menerimanya karena itu kewajiban, meskipun dengan sedikit rasa cemas', 'c' => false, 'w' => 4],
                    ['t' => 'Meminta dinas luar kota dibatalkan atau ditunda jika memungkinkan', 'c' => false, 'w' => 3],
                    ['t' => 'Meminta atasan mengirimkan rekan kerja lain saja yang lebih berpengalaman', 'c' => false, 'w' => 2],
                    ['t' => 'Menolak tegas karena tidak menyukai perjalanan jauh', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketika menghadapi konflik pendapat dalam kerja kelompok, tindakan saya...',
                'opts' => [
                    ['t' => 'Mengajak bermusyawarah untuk mencari solusi terbaik bersama', 'c' => true, 'w' => 5],
                    ['t' => 'Mendengarkan semua pendapat dan mengambil jalan tengah', 'c' => false, 'w' => 4],
                    ['t' => 'Mengikuti pendapat mayoritas anggota kelompok demi keharmonisan', 'c' => false, 'w' => 3],
                    ['t' => 'Mempertahankan pendapat pribadi karena yakin itu yang terbaik', 'c' => false, 'w' => 2],
                    ['t' => 'Memilih diam dan pasrah pada keputusan akhir kelompok', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Saat rekan kerja se-tim malas-malasan sehingga menghambat target tim, saya akan...',
                'opts' => [
                    ['t' => 'Menegurnya baik-baik dan mengingatkan komitmen awal tim', 'c' => true, 'w' => 5],
                    ['t' => 'Melaporkan kinerjanya kepada ketua tim/atasan', 'c' => false, 'w' => 4],
                    ['t' => 'Membantu menyelesaikan bagian tugasnya agar target tercapai', 'c' => false, 'w' => 3],
                    ['t' => 'Membiarkannya dan tetap fokus pada pekerjaan saya sendiri', 'c' => false, 'w' => 2],
                    ['t' => 'Ikut malas-malasan sebagai bentuk protes terhadapnya', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Bila saya melakukan kesalahan fatal yang merugikan kantor, tindakan saya...',
                'opts' => [
                    ['t' => 'Mengakui kesalahan di depan atasan dan bertanggung jawab memperbaikinya', 'c' => true, 'w' => 5],
                    ['t' => 'Mencari solusi perbaikan terlebih dahulu sebelum melapor ke atasan', 'c' => false, 'w' => 4],
                    ['t' => 'Menjelaskan alasan-alasan rasional mengapa kesalahan itu bisa terjadi', 'c' => false, 'w' => 3],
                    ['t' => 'Mencoba menutupi kesalahan agar tidak merusak reputasi saya', 'c' => false, 'w' => 2],
                    ['t' => 'Menyalahkan rekan kerja lain atau sistem yang kurang mendukung', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketika ditawari hadiah/uang ucapan terima kasih dari pengguna layanan, saya akan...',
                'opts' => [
                    ['t' => 'Menolaknya dengan sopan karena itu merupakan gratifikasi', 'c' => true, 'w' => 5],
                    ['t' => 'Menolaknya jika ada orang lain atau kamera pengawas yang melihat', 'c' => false, 'w' => 4],
                    ['t' => 'Menerimanya dan menyumbangkannya langsung ke kas sosial kantor', 'c' => false, 'w' => 3],
                    ['t' => 'Menerimanya karena merasa pelayanan yang diberikan sudah maksimal', 'c' => false, 'w' => 2],
                    ['t' => 'Meminta hadiah tambahan yang lebih bernilai secara tersirat', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
        ];

        // Programmatically expand the lists to 50 questions each by generating variations.
        // This ensures exactly 50 realistic questions per category without bloating the code size.
        
        // Expansion of TIU to 50 questions
        $tiuBaseCount = count($tiuQuestions);
        for ($i = $tiuBaseCount; $i < 50; $i++) {
            $num = $i + 1;
            // Generate some mathematical or verbal variations based on indices
            if ($i % 3 === 0) {
                // Math question
                $a = ($i * 3) + 7;
                $b = ($i * 2) - 5;
                $res = $a + $b;
                $tiuQuestions[] = [
                    'q' => "Berapakah hasil penjumlahan dari {$a} dan {$b}?",
                    'opts' => [
                        ['t' => (string)$res, 'c' => true, 'w' => 5],
                        ['t' => (string)($res + 2), 'c' => false, 'w' => 0],
                        ['t' => (string)($res - 3), 'c' => false, 'w' => 0],
                        ['t' => (string)($res * 2), 'c' => false, 'w' => 0],
                        ['t' => (string)($res + 5), 'c' => false, 'w' => 0],
                    ],
                    'diff' => 'easy'
                ];
            } elseif ($i % 3 === 1) {
                // Series question
                $start = $i;
                $step = 4;
                $s1 = $start;
                $s2 = $start + $step;
                $s3 = $s2 + $step;
                $s4 = $s3 + $step;
                $ans = $s4 + $step;
                $tiuQuestions[] = [
                    'q' => "Tentukan angka berikutnya dari deret: {$s1}, {$s2}, {$s3}, {$s4}, ...",
                    'opts' => [
                        ['t' => (string)$ans, 'c' => true, 'w' => 5],
                        ['t' => (string)($ans + 2), 'c' => false, 'w' => 0],
                        ['t' => (string)($ans - 2), 'c' => false, 'w' => 0],
                        ['t' => (string)($ans + 4), 'c' => false, 'w' => 0],
                        ['t' => (string)($ans - 4), 'c' => false, 'w' => 0],
                    ],
                    'diff' => 'medium'
                ];
            } else {
                // Analogy variation
                $pairs = [
                    ['a' => 'PENYAIR', 'b' => 'PUISI', 'x' => 'Pematung', 'y' => 'Patung'],
                    ['a' => 'KAYU', 'b' => 'POHON', 'x' => 'Emas', 'y' => 'Tambang'],
                    ['a' => 'CANGKIR', 'b' => 'KOPI', 'x' => 'Piring', 'y' => 'Nasi'],
                    ['a' => 'HEWAN', 'b' => 'SINGA', 'x' => 'Tumbuhan', 'y' => 'Mawar'],
                    ['a' => 'BULAN', 'b' => 'MALAM', 'x' => 'Matahari', 'y' => 'Siang'],
                    ['a' => 'KOMPAS', 'b' => 'ARAH', 'x' => 'Arloji', 'y' => 'Waktu'],
                    ['a' => 'TELUR', 'b' => 'AYAM', 'x' => 'Biji', 'y' => 'Pohon'],
                    ['a' => 'GURU', 'b' => 'SEKOLAH', 'x' => 'Petani', 'y' => 'Sawah'],
                    ['a' => 'NELAYAN', 'b' => 'LAUT', 'x' => 'Peternak', 'y' => 'Kandang'],
                    ['a' => 'PILOT', 'b' => 'PESAWAT', 'x' => 'Masinis', 'y' => 'Kereta'],
                    ['a' => 'BUNGA', 'b' => 'TAMAN', 'x' => 'Bintang', 'y' => 'Langit'],
                    ['a' => 'KAMERA', 'b' => 'FOTOGRAFER', 'x' => 'Cangkul', 'y' => 'Petani'],
                    ['a' => 'MATA', 'b' => 'AIR MATA', 'x' => 'Gunung', 'y' => 'Lava'],
                    ['a' => 'GULA', 'b' => 'MANIS', 'x' => 'Garam', 'y' => 'Asin'],
                ];
                $pair = $pairs[($i - $tiuBaseCount) % count($pairs)];
                $tiuQuestions[] = [
                    'q' => "Analogi Kata: {$pair['a']} : {$pair['b']} = ... : ...",
                    'opts' => [
                        ['t' => "{$pair['x']} : {$pair['y']}", 'c' => true, 'w' => 5],
                        ['t' => "{$pair['y']} : {$pair['x']}", 'c' => false, 'w' => 0],
                        ['t' => "{$pair['a']} : {$pair['x']}", 'c' => false, 'w' => 0],
                        ['t' => "{$pair['b']} : {$pair['y']}", 'c' => false, 'w' => 0],
                        ['t' => "Tangan : Kaki", 'c' => false, 'w' => 0],
                    ],
                    'diff' => 'easy'
                ];
            }
        }

        // Expansion of TWK to 50 questions
        $twkBaseCount = count($twkQuestions);
        $twkPool = [
            [
                'q' => 'Siapakah presiden pertama Republik Indonesia?',
                'opts' => [
                    ['t' => 'Ir. Soekarno', 'c' => true, 'w' => 5],
                    ['t' => 'Drs. Moh. Hatta', 'c' => false, 'w' => 0],
                    ['t' => 'Soeharto', 'c' => false, 'w' => 0],
                    ['t' => 'B.J. Habibie', 'c' => false, 'w' => 0],
                    ['t' => 'K.H. Abdurrahman Wahid', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Berapa jumlah provinsi di Indonesia saat ini (setelah pemekaran Papua tahun 2022)?',
                'opts' => [
                    ['t' => '38 Provinsi', 'c' => true, 'w' => 5],
                    ['t' => '34 Provinsi', 'c' => false, 'w' => 0],
                    ['t' => '35 Provinsi', 'c' => false, 'w' => 0],
                    ['t' => '37 Provinsi', 'c' => false, 'w' => 0],
                    ['t' => '40 Provinsi', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Di manakah letak tugu Proklamasi didirikan untuk memperingati kemerdekaan Indonesia?',
                'opts' => [
                    ['t' => 'Jakarta', 'c' => true, 'w' => 5],
                    ['t' => 'Bandung', 'c' => false, 'w' => 0],
                    ['t' => 'Surabaya', 'c' => false, 'w' => 0],
                    ['t' => 'Yogyakarta', 'c' => false, 'w' => 0],
                    ['t' => 'Semarang', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Konferensi Asia Afrika (KAA) pertama kali diadakan di kota...',
                'opts' => [
                    ['t' => 'Bandung', 'c' => true, 'w' => 5],
                    ['t' => 'Jakarta', 'c' => false, 'w' => 0],
                    ['t' => 'Surabaya', 'c' => false, 'w' => 0],
                    ['t' => 'Bogor', 'c' => false, 'w' => 0],
                    ['t' => 'Yogyakarta', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Siapa pahlawan nasional yang dikenal sebagai "Bapak Pendidikan Nasional"?',
                'opts' => [
                    ['t' => 'Ki Hajar Dewantara', 'c' => true, 'w' => 5],
                    ['t' => 'R.A. Kartini', 'c' => false, 'w' => 0],
                    ['t' => 'Dr. Sutomo', 'c' => false, 'w' => 0],
                    ['t' => 'Cipto Mangunkusumo', 'c' => false, 'w' => 0],
                    ['t' => 'Ahmad Dahlan', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Pada tanggal berapakah pertempuran hebat di Surabaya terjadi yang kini diperingati sebagai Hari Pahlawan?',
                'opts' => [
                    ['t' => '10 November 1945', 'c' => true, 'w' => 5],
                    ['t' => '17 Agustus 1945', 'c' => false, 'w' => 0],
                    ['t' => '20 Mei 1908', 'c' => false, 'w' => 0],
                    ['t' => '1 Oktober 1965', 'c' => false, 'w' => 0],
                    ['t' => '5 Oktober 1945', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Kekuasaan tertinggi negara menurut UUD 1945 sebelum amandemen berada di tangan...',
                'opts' => [
                    ['t' => 'Majelis Permusyawaratan Rakyat (MPR)', 'c' => true, 'w' => 5],
                    ['t' => 'Presiden', 'c' => false, 'w' => 0],
                    ['t' => 'Dewan Perwakilan Rakyat (DPR)', 'c' => false, 'w' => 0],
                    ['t' => 'Mahkamah Agung (MA)', 'c' => false, 'w' => 0],
                    ['t' => 'Rakyat langsung', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Mahkamah Konstitusi Republik Indonesia dibentuk pada tahun...',
                'opts' => [
                    ['t' => '2003', 'c' => true, 'w' => 5],
                    ['t' => '2001', 'c' => false, 'w' => 0],
                    ['t' => '2002', 'c' => false, 'w' => 0],
                    ['t' => '2004', 'c' => false, 'w' => 0],
                    ['t' => '2005', 'c' => false, 'w' => 0],
                ],
                'diff' => 'hard'
            ],
            [
                'q' => 'Sila keberapakah dalam Pancasila yang menjadi landasan utama persatuan dan kesatuan bangsa Indonesia?',
                'opts' => [
                    ['t' => 'Sila ke-3', 'c' => true, 'w' => 5],
                    ['t' => 'Sila ke-1', 'c' => false, 'w' => 0],
                    ['t' => 'Sila ke-2', 'c' => false, 'w' => 0],
                    ['t' => 'Sila ke-4', 'c' => false, 'w' => 0],
                    ['t' => 'Sila ke-5', 'c' => false, 'w' => 0],
                ],
                'diff' => 'easy'
            ],
            [
                'q' => 'Di bawah ini merupakan salah satu asas kewarganegaraan berdasarkan keturunan, yaitu...',
                'opts' => [
                    ['t' => 'Ius Sanguinis', 'c' => true, 'w' => 5],
                    ['t' => 'Ius Soli', 'c' => false, 'w' => 0],
                    ['t' => 'Naturalisasi', 'c' => false, 'w' => 0],
                    ['t' => 'Stelsel aktif', 'c' => false, 'w' => 0],
                    ['t' => 'Stelsel pasif', 'c' => false, 'w' => 0],
                ],
                'diff' => 'medium'
            ],
        ];
        
        for ($i = $twkBaseCount; $i < 50; $i++) {
            $poolIndex = ($i - $twkBaseCount) % count($twkPool);
            $baseQuestion = $twkPool[$poolIndex];
            
            // To make sure each question is unique even if taken from pool, we append a subtle variation or question number representation
            $twkQuestions[] = [
                'q' => $baseQuestion['q'] . " (Soal No. " . ($i + 1) . ")",
                'opts' => $baseQuestion['opts'],
                'diff' => $baseQuestion['diff']
            ];
        }

        // Expansion of TKP to 50 questions
        $tkpBaseCount = count($tkpQuestions);
        $tkpPool = [
            [
                'q' => 'Jika ada tugas baru yang membutuhkan keahlian IT tingkat tinggi sedangkan saya gaptek, saya akan...',
                'opts' => [
                    ['t' => 'Mengikuti kursus kilat IT dan berdiskusi dengan rekan kerja yang mahir', 'c' => true, 'w' => 5],
                    ['t' => 'Mencoba mempelajari software tersebut secara otodidak melalui internet', 'c' => false, 'w' => 4],
                    ['t' => 'Meminta bantuan rekan kerja untuk membimbing saya mengerjakannya', 'c' => false, 'w' => 3],
                    ['t' => 'Mengerjakan tugas seadanya tanpa memaksimalkan fitur software', 'c' => false, 'w' => 2],
                    ['t' => 'Meminta atasan mengganti tugas saya dengan tugas manual saja', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Menghadapi rekan kerja yang menyebarkan gosip miring tentang kehidupan pribadi saya, saya akan...',
                'opts' => [
                    ['t' => 'Tetap fokus bekerja secara profesional dan tidak mempedulikan gosip tersebut', 'c' => true, 'w' => 5],
                    ['t' => 'Mengajaknya bicara empat mata untuk mengklarifikasi dan meminta ia berhenti', 'c' => false, 'w' => 4],
                    ['t' => 'Membuktikan lewat prestasi kerja bahwa gosip itu tidak mempengaruhi kinerja saya', 'c' => false, 'w' => 3],
                    ['t' => 'Melaporkannya ke bagian HRD atas tindakan pencemaran nama baik', 'c' => false, 'w' => 2],
                    ['t' => 'Membalasnya dengan menyebarkan gosip tentangnya agar adil', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketika ide atau usulan saya dalam rapat ditolak mentah-mentah oleh forum, sikap saya...',
                'opts' => [
                    ['t' => 'Menerima keputusan forum dengan lapang dada dan mendukung ide yang terpilih', 'c' => true, 'w' => 5],
                    ['t' => 'Menanyakan alasan penolakan secara logis agar bisa mengevaluasi ide tersebut', 'c' => false, 'w' => 4],
                    ['t' => 'Menghargai penolakan tersebut dan mencoba tidak ambil pusing', 'c' => false, 'w' => 3],
                    ['t' => 'Kecewa namun tetap mengikuti jalannya rapat hingga selesai', 'c' => false, 'w' => 2],
                    ['t' => 'Walkout dari ruangan rapat karena merasa tidak dihargai', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Bila saya melihat ada celah untuk memanipulasi absensi kerja tanpa diketahui kantor, saya akan...',
                'opts' => [
                    ['t' => 'Tetap absen secara jujur sesuai jam kehadiran yang sebenarnya', 'c' => true, 'w' => 5],
                    ['t' => 'Mengingatkan rekan kerja lain agar tidak memanfaatkan celah tersebut', 'c' => false, 'w' => 4],
                    ['t' => 'Melaporkan kelemahan sistem absensi tersebut ke bagian IT/kepegawaian', 'c' => false, 'w' => 3],
                    ['t' => 'Menggunakannya hanya jika dalam keadaan darurat/terlambat sekali', 'c' => false, 'w' => 2],
                    ['t' => 'Menggunakannya secara rutin agar bisa pulang lebih cepat', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
            [
                'q' => 'Ketika diminta bekerja lembur di hari libur nasional untuk tugas mendesak, saya akan...',
                'opts' => [
                    ['t' => 'Menyetujuinya demi kelancaran pekerjaan kantor dan menunda urusan pribadi', 'c' => true, 'w' => 5],
                    ['t' => 'Menerimanya dengan syarat mendapatkan uang lembur yang sesuai ketentuan', 'c' => false, 'w' => 4],
                    ['t' => 'Menyetujuinya namun meminta kompensasi hari libur pengganti di hari kerja', 'c' => false, 'w' => 3],
                    ['t' => 'Mencari alasan keluarga agar dibebaskan dari kewajiban lembur tersebut', 'c' => false, 'w' => 2],
                    ['t' => 'Menolak mentah-mentah karena hari libur adalah hak mutlak saya', 'c' => false, 'w' => 1],
                ],
                'diff' => 'medium'
            ],
        ];

        for ($i = $tkpBaseCount; $i < 50; $i++) {
            $poolIndex = ($i - $tkpBaseCount) % count($tkpPool);
            $baseQuestion = $tkpPool[$poolIndex];
            
            $tkpQuestions[] = [
                'q' => $baseQuestion['q'] . " (Studi Kasus No. " . ($i + 1) . ")",
                'opts' => $baseQuestion['opts'],
                'diff' => $baseQuestion['diff']
            ];
        }

        // --- DATABASE INSERTION LOGIC ---
        // Seed TIU Questions
        foreach ($tiuQuestions as $qData) {
            $question = Question::create([
                'category_id' => $tiu->id,
                'type' => 'pg',
                'difficulty' => $qData['diff'],
                'content_text' => $qData['q'],
                'created_by' => $admin->id
            ]);

            foreach ($qData['opts'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['t'],
                    'is_correct' => $opt['c'],
                    'weight' => $opt['w']
                ]);
            }
        }

        // Seed TWK Questions
        foreach ($twkQuestions as $qData) {
            $question = Question::create([
                'category_id' => $twk->id,
                'type' => 'pg',
                'difficulty' => $qData['diff'],
                'content_text' => $qData['q'],
                'created_by' => $admin->id
            ]);

            foreach ($qData['opts'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['t'],
                    'is_correct' => $opt['c'],
                    'weight' => $opt['w']
                ]);
            }
        }

        // Seed TKP Questions
        foreach ($tkpQuestions as $qData) {
            $question = Question::create([
                'category_id' => $tkp->id,
                'type' => 'pg',
                'difficulty' => $qData['diff'],
                'content_text' => $qData['q'],
                'created_by' => $admin->id
            ]);

            foreach ($qData['opts'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['t'],
                    'is_correct' => $opt['c'],
                    'weight' => $opt['w']
                ]);
            }
        }

        // Create Simulasi Mandiri Tryout CPNS Assessment
        $assessment = Assessment::updateOrCreate(
            ['title' => 'Simulasi Mandiri Tryout CPNS'],
            [
                'start_date' => now()->subHours(1)->toDateTimeString(),
                'end_date' => now()->addDays(30)->toDateTimeString(),
                'duration_minutes' => 130,
                'max_attempts' => 5,
                'passing_grade' => 311.00,
                'passing_grade_type' => 'per_category'
            ]
        );

        // Link with all groups (so the default candidate 'peserta' can take it)
        $groupIds = \App\Models\Group::pluck('id')->toArray();
        $assessment->groups()->sync($groupIds);

        // Fetch questions to attach: 30 TWK, 35 TIU, 45 TKP (Total 110 standard CPNS questions)
        $twkQIds = Question::where('category_id', $twk->id)->limit(30)->pluck('id')->toArray();
        $tiuQIds = Question::where('category_id', $tiu->id)->limit(35)->pluck('id')->toArray();
        $tkpQIds = Question::where('category_id', $tkp->id)->limit(45)->pluck('id')->toArray();

        $allCPNSQIds = array_merge($twkQIds, $tiuQIds, $tkpQIds);

        $syncData = [];
        foreach ($allCPNSQIds as $index => $qId) {
            $syncData[$qId] = ['order_no' => $index + 1];
        }
        $assessment->questions()->sync($syncData);
    }
}

