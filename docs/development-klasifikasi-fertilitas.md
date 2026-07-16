Anda berperan sebagai senior full-stack developer yang mengembangkan aplikasi
skripsi bernama “Sistem Klasifikasi Program Fertilitas Selanjutnya”.

Sebelum mengubah kode, pelajari struktur repository, conventions starter kit,
komponen UI, routing, autentikasi, Wayfinder, Inertia, Svelte, dan pola testing
yang sudah tersedia.

Jangan mengganti arsitektur atau menambahkan dependency tanpa alasan yang kuat.

==================================================
1. ARSITEKTUR DAN TECH STACK
==================================================

Proyek menggunakan Laravel Svelte Starter Kit dengan arsitektur monolith
server-driven SPA.

Backend:
- PHP ^8.3
- Laravel ^13.17
- Laravel Fortify
- Laravel Passkeys
- Inertia Laravel ^3.0
- Laravel Wayfinder
- Eloquent ORM
- Pest PHP
- SQLite untuk development

Frontend:
- Svelte 5
- TypeScript
- @inertiajs/svelte ^3.0
- Vite 8
- Tailwind CSS 4
- bits-ui
- lucide-svelte
- svelte-sonner

Pola utama:

Laravel Route
→ Controller
→ Form Request
→ Service
→ Eloquent Model
→ Inertia::render()
→ Svelte Page

Ketentuan:

1. Jangan membuat REST API terpisah.
2. Jangan menggunakan Next.js.
3. Jangan menggunakan Axios untuk alur standar Inertia.
4. Jangan hardcode URL pada frontend.
5. Gunakan typed routes dari Wayfinder melalui `@/actions` atau `@/routes`.
6. Gunakan komponen UI yang sudah tersedia pada
   `resources/js/components/ui`.
7. Gunakan TypeScript pada seluruh halaman dan komponen Svelte.
8. Gunakan Form Request untuk validasi.
9. Gunakan Pest untuk pengujian.
10. Jangan merusak fitur auth, settings, 2FA, passkeys, dan appearance.
11. Jangan mengubah dependency apabila fitur dapat dibuat dengan dependency
    yang sudah tersedia.
12. Jangan melakukan training ulang machine learning di Laravel.
13. Laravel hanya menerapkan decision rule hasil RapidMiner.
14. Jangan mengimpor 101 data latih ke database aplikasi.
15. Data latih digunakan secara tidak langsung melalui decision rule,
    confidence, support count, dan support percentage.

==================================================
2. TUJUAN SISTEM
==================================================

Sistem digunakan oleh pengguna internal yang sudah login dan terverifikasi
untuk:

1. memasukkan informasi kunjungan pasien;
2. memasukkan atribut klasifikasi;
3. menghitung umur pasien secara otomatis;
4. menjalankan decision rule C4.5;
5. menghasilkan prediksi Promil Selanjutnya;
6. menampilkan jalur keputusan;
7. menampilkan confidence dan data pendukung;
8. menyimpan hasil klasifikasi;
9. melihat riwayat klasifikasi;
10. melihat informasi model penelitian.

Sistem tidak dimaksudkan sebagai rekam medis lengkap.

Sistem hanya menyimpan informasi kunjungan yang diperlukan dan atribut
yang relevan untuk penerapan algoritma.

Pada halaman hasil tampilkan disclaimer:

“Hasil klasifikasi merupakan pola berdasarkan data historis penelitian dan
tidak menggantikan keputusan dokter.”

==================================================
3. RUANG LINGKUP FITUR
==================================================

Implementasikan:

1. Dashboard
2. Klasifikasi Baru
3. Hasil Klasifikasi
4. Riwayat Klasifikasi
5. Informasi Model
6. Tabel master untuk dokter, Promil Saat Ini, Tindakan Hari Ini, dan PoA1

Jangan implementasikan dahulu:

- rekam medis lengkap;
- data pasangan;
- nomor telepon;
- penjadwalan;
- PoA2;
- follow-up pasien;
- approval dokter;
- training ulang model;
- integrasi RapidMiner;
- REST API;
- ekspor Excel;
- laporan PDF;
- audit log kompleks;
- notifikasi;
- role management kompleks;
- CRUD master data melalui antarmuka.

Master data untuk tahap awal dikelola melalui migration dan seeder.

==================================================
4. STRUKTUR FORM
==================================================

Form Klasifikasi Baru dibagi menjadi dua bagian.

--------------------------------------------------
A. INFORMASI KUNJUNGAN
--------------------------------------------------

Field:

1. Nama Lengkap
   - key: patient_name
   - tipe: text
   - wajib

2. Tanggal Kunjungan
   - key: visit_date
   - tipe: date
   - wajib

3. Tanggal Lahir
   - key: date_of_birth
   - tipe: date
   - wajib
   - tidak boleh melebihi tanggal kunjungan

4. Umur
   - key database: age
   - ditampilkan read-only
   - dihitung otomatis
   - tidak dipercaya dari frontend
   - backend wajib menghitung ulang

5. Dokter Pemeriksa
   - key: doctor_id
   - dropdown dari tabel doctors
   - hanya dokter aktif
   - wajib

--------------------------------------------------
B. DATA KLASIFIKASI
--------------------------------------------------

Field:

1. Promil Saat Ini
   - key: current_promil_id
   - dropdown dari tabel current_promils
   - hanya data aktif
   - wajib

2. Tindakan Hari Ini
   - key: current_action_id
   - dropdown dari tabel current_actions
   - hanya data aktif
   - wajib

3. PoA1
   - key: poa1_option_id
   - dropdown dari tabel poa1_options
   - hanya data aktif
   - wajib

4. Promil Selanjutnya
   - tidak menjadi input
   - dihasilkan oleh DecisionTreeService
   - disimpan sebagai prediction_result

PoA1 tetap disimpan sebagai atribut penelitian, tetapi tidak dipakai oleh
pohon keputusan final karena tidak terpilih sebagai node.

==================================================
5. PERHITUNGAN UMUR
==================================================

Umur dihitung berdasarkan tanggal lahir terhadap tanggal kunjungan.

Jangan menghitung umur berdasarkan tanggal hari ini.

Contoh:

Tanggal lahir:
10 Januari 1990

Tanggal kunjungan:
5 Januari 2026

Umur:
35 tahun

Backend menjadi sumber kebenaran.

Gunakan Carbon:

```php
$age = Carbon::parse($dateOfBirth)
    ->diffInYears(Carbon::parse($visitDate));