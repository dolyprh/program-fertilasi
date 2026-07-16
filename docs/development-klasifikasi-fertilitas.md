# DEVELOPMENT SPECIFICATION

# Sistem Klasifikasi Program Fertilitas Selanjutnya

## 1. Peran dan cara kerja

Anda berperan sebagai senior full-stack developer yang mengembangkan aplikasi skripsi bernama:

**Sistem Klasifikasi Program Fertilitas Selanjutnya**

Sebelum mengubah kode:

1. pelajari struktur repository;
2. identifikasi conventions starter kit;
3. periksa routing, autentikasi, Inertia, Wayfinder, Svelte, komponen UI, migration, dan pola testing;
4. gunakan implementasi yang sudah tersedia sebagai acuan;
5. jangan mengganti arsitektur tanpa alasan yang dapat dibuktikan;
6. jangan menambahkan dependency apabila fitur dapat dibuat dengan dependency yang sudah tersedia.

Pengembangan harus dilakukan bertahap.

Jangan mengerjakan beberapa tahap sekaligus.

Setelah satu tahap selesai:

1. jalankan pemeriksaan yang relevan;
2. laporkan file yang dibuat dan diubah;
3. laporkan hasil command sebenarnya;
4. berhenti dan tunggu instruksi tahap berikutnya.

Jangan mengklaim test, lint, atau build berhasil apabila command tersebut belum dijalankan.

---

# BAGIAN A — KONTEKS SISTEM

## 2. Arsitektur dan teknologi

Proyek menggunakan Laravel Svelte Starter Kit dengan arsitektur monolith server-driven SPA.

### Backend

* PHP 8.3 atau versi aktual yang kompatibel;
* Laravel 13.17 atau versi aktual pada repository;
* Laravel Fortify;
* Laravel Passkeys;
* Inertia Laravel 3;
* Laravel Wayfinder;
* Eloquent ORM;
* Pest PHP;
* SQLite untuk development, kecuali repository menggunakan konfigurasi lain.

### Frontend

* Svelte 5;
* TypeScript;
* Inertia Svelte 3;
* Vite 8;
* Tailwind CSS 4;
* bits-ui;
* lucide-svelte;
* svelte-sonner.

### Alur aplikasi

```text
Laravel Route
→ Controller
→ Form Request
→ Application Service
→ Eloquent Model
→ Inertia::render()
→ Svelte Page
```

### Ketentuan arsitektur

1. Jangan membuat REST API terpisah.
2. Jangan menggunakan Next.js.
3. Jangan menggunakan Axios untuk alur standar Inertia.
4. Jangan hardcode URL pada frontend.
5. Gunakan typed route atau action dari Wayfinder.
6. Gunakan komponen UI yang sudah tersedia.
7. Gunakan TypeScript pada halaman dan komponen Svelte.
8. Gunakan Form Request untuk validasi.
9. Gunakan Pest untuk testing.
10. Jangan merusak auth, settings, 2FA, passkeys, dan appearance.
11. Jangan membuat mekanisme machine-learning training di Laravel.
12. Jangan mengimpor 101 data latih sebagai record aplikasi.
13. Laravel hanya menerapkan decision rule hasil RapidMiner.
14. Jangan menghitung entropy, information gain, atau gain ratio setiap kali klasifikasi dijalankan.
15. Jangan memperbarui decision rule secara otomatis.

---

## 3. Tujuan aplikasi

Aplikasi digunakan oleh pengguna internal yang telah login dan terverifikasi untuk:

1. memasukkan informasi kunjungan pasien;
2. memasukkan atribut klasifikasi;
3. menghitung umur pasien berdasarkan tanggal kunjungan;
4. menjalankan decision rule C4.5;
5. menghasilkan prediksi Promil Selanjutnya;
6. menampilkan jalur keputusan;
7. menampilkan confidence dan dukungan data;
8. menyimpan hasil klasifikasi;
9. melihat riwayat klasifikasi;
10. melihat informasi model penelitian.

Aplikasi bukan rekam medis lengkap.

Aplikasi hanya menyimpan informasi kunjungan dan atribut yang dibutuhkan untuk penerapan model penelitian.

Tampilkan disclaimer pada halaman hasil:

> Hasil klasifikasi merupakan pola berdasarkan data historis penelitian dan tidak menggantikan keputusan dokter.

---

## 4. Ruang lingkup MVP

### Implementasikan

1. Dashboard.
2. Klasifikasi Baru.
3. Hasil Klasifikasi.
4. Riwayat Klasifikasi.
5. Informasi Model.
6. Master Dokter.
7. Master Promil Saat Ini.
8. Master Tindakan Hari Ini.
9. Master PoA1.
10. DecisionTreeService.
11. Pengujian unit dan feature.

### Jangan implementasikan

* rekam medis lengkap;
* data pasangan;
* nomor telepon pasien;
* penjadwalan;
* PoA2;
* follow-up pasien;
* approval dokter;
* training ulang model;
* integrasi langsung dengan RapidMiner;
* REST API;
* ekspor Excel;
* laporan PDF;
* audit log kompleks;
* notifikasi;
* role management kompleks;
* CRUD master melalui antarmuka;
* edit atau hapus hasil klasifikasi.

Pada MVP, master data dikelola melalui migration dan seeder.

---

# BAGIAN B — KEBUTUHAN DOMAIN

## 5. Struktur form klasifikasi

Form Klasifikasi Baru dibagi menjadi dua bagian.

### 5.1 Informasi Kunjungan

#### Nama Lengkap

```text
key: patient_name
type: text
required: true
```

#### Tanggal Kunjungan

```text
key: visit_date
type: date
required: true
```

#### Tanggal Lahir

```text
key: date_of_birth
type: date
required: true
```

Tanggal lahir tidak boleh melewati tanggal kunjungan.

#### Umur

```text
database column: age
frontend: read-only preview
backend: dihitung ulang
```

Umur tidak diterima sebagai sumber kebenaran dari request.

#### Dokter Pemeriksa

```text
key: doctor_id
source: doctors
filter: is_active = true
required: true
```

### 5.2 Data Klasifikasi

#### Promil Saat Ini

```text
key: current_promil_id
source: current_promils
filter: is_active = true
required: true
```

#### Tindakan Hari Ini

```text
key: current_action_id
source: current_actions
filter: is_active = true
required: true
```

#### PoA1

```text
key: poa1_option_id
source: poa1_options
filter: is_active = true
required: true
```

#### Promil Selanjutnya

Promil Selanjutnya bukan input pengguna.

Nilai ini dihasilkan oleh `DecisionTreeService` dan disimpan sebagai:

```text
prediction_result
```

PoA1 tetap disimpan sebagai atribut penelitian, tetapi tidak digunakan oleh decision tree final karena tidak terpilih sebagai node.

---

## 6. Perhitungan umur

Umur dihitung dari tanggal lahir terhadap tanggal kunjungan.

Jangan menggunakan tanggal hari ini.

Contoh:

```text
Tanggal lahir     : 10 Januari 1990
Tanggal kunjungan : 5 Januari 2026
Umur              : 35 tahun
```

Backend menjadi sumber kebenaran.

Gunakan perhitungan jumlah tahun yang telah selesai, bukan selisih tahun kalender.

Contoh implementasi:

```php
$dateOfBirth = CarbonImmutable::parse($validated['date_of_birth']);
$visitDate = CarbonImmutable::parse($validated['visit_date']);

$age = $dateOfBirth->diff($visitDate)->y;
```

Validasi umum:

```text
minimum: 1 tahun
maximum: 120 tahun
```

Rentang umur data training:

```text
minimum: 26 tahun
maximum: 59 tahun
```

Umur di luar rentang training tetap dapat diproses, tetapi tampilkan warning non-blocking.

Karena umur aplikasi disimpan sebagai bilangan bulat penuh:

```text
Umur > 28,5  berarti umur minimal 29
Umur > 27,5 dan ≤ 28,5 berarti umur 28
Umur ≤ 27,5 berarti umur maksimal 27
Umur > 40,5 berarti umur minimal 41
Umur ≤ 40,5 berarti umur maksimal 40
Umur > 46,5 berarti umur minimal 47
Umur > 28,5 dan ≤ 46,5 berarti umur 29–46
Umur ≤ 28,5 berarti umur maksimal 28
```

Frontend boleh menghitung umur untuk preview, tetapi backend wajib menghitung ulang.

Jangan menerima field `age` dari request pengguna.

---

# BAGIAN C — DATABASE DAN MODEL

## 7. Master Dokter

Buat tabel:

```text
doctors
```

Kolom:

```text
id
code string unique
name string
is_active boolean default true
sort_order unsigned integer default 0
created_at
updated_at
```

Contoh format kode:

```text
D001
D002
```

Nama dokter tidak disimpan langsung pada tabel classifications.

Jangan menggunakan nama dokter asli pada seeder sebelum data aktual diberikan.

---

## 8. Master Promil Saat Ini

Buat tabel:

```text
current_promils
```

Kolom:

```text
id
code string unique
name string
is_active boolean default true
sort_order unsigned integer default 0
created_at
updated_at
```

Seed data wajib:

| Code               | Name               |
| ------------------ | ------------------ |
| ALAMI              | Alami              |
| BELUM_PROMIL       | Belum Promil       |
| IUI                | IUI                |
| IVF                | IVF                |
| PEMERIKSAAN_DASAR  | Pemeriksaan Dasar  |
| SENGGAMA_TERJADWAL | Senggama Terjadwal |

DecisionTreeService wajib menggunakan `code`, bukan `name`.

---

## 9. Master Tindakan Hari Ini

Buat tabel:

```text
current_actions
```

Kolom:

```text
id
code string unique
name string
is_active boolean default true
sort_order unsigned integer default 0
created_at
updated_at
```

Seed data minimum:

| Code                     | Name                     |
| ------------------------ | ------------------------ |
| HO                       | HO                       |
| KONTROL_D2_D5            | Kontrol D2-D5            |
| PRP_OVARIUM_TRANSVAGINAL | PRP Ovarium Transvaginal |
| PAKET_AKUPUNTUR_12X_KE_6 | Paket Akupuntur 12x Ke 6 |
| LAINNYA                  | Lainnya                  |

Kategori tambahan dapat ditambahkan melalui seeder tanpa mengubah rule engine.

Untuk kondisi:

```text
Promil Saat Ini = IVF
Umur minimal 41
Tindakan bukan HO, KONTROL_D2_D5, PRP_OVARIUM_TRANSVAGINAL,
atau PAKET_AKUPUNTUR_12X_KE_6
```

hasilnya adalah `no_rule`.

---

## 10. Master PoA1

Buat tabel:

```text
poa1_options
```

Kolom:

```text
id
code string unique
name string
is_active boolean default true
sort_order unsigned integer default 0
created_at
updated_at
```

Jangan mengarang kategori bisnis PoA1.

Seeder harus menyediakan struktur array yang mudah diisi setelah kategori unik PoA1 dari dataset diberikan.

Testing harus menggunakan factory dan tidak bergantung pada isi seeder PoA1.

Jika tidak ada PoA1 aktif:

1. tampilkan alert pada form;
2. jelaskan bahwa master PoA1 belum tersedia;
3. nonaktifkan tombol submit.

---

## 11. Model master

Buat model:

```text
Doctor
CurrentPromil
CurrentAction
Poa1Option
```

Setiap model memiliki:

1. fillable atau guarded mengikuti convention repository;
2. cast `is_active` menjadi boolean;
3. scope `active`;
4. scope `ordered`;
5. relasi `hasMany` ke Classification.

Contoh:

```php
public function scopeActive(Builder $query): Builder
{
    return $query->where('is_active', true);
}

public function scopeOrdered(Builder $query): Builder
{
    return $query
        ->orderBy('sort_order')
        ->orderBy('name');
}
```

Jangan menggunakan soft delete pada tahap MVP.

Foreign key master pada classifications menggunakan `restrictOnDelete()`.

Master yang tidak lagi digunakan dinonaktifkan dengan:

```text
is_active = false
```

---

## 12. Tabel classifications

Buat tabel:

```text
classifications
```

Kolom:

```text
id
user_id foreign key users cascade on delete
doctor_id foreign key doctors restrict on delete
current_promil_id foreign key current_promils restrict on delete
current_action_id foreign key current_actions restrict on delete
poa1_option_id foreign key poa1_options restrict on delete
case_code string unique
patient_name string
visit_date date
date_of_birth date
age unsigned small integer
prediction_result string nullable
rule_code string nullable
decision_path json nullable
confidence decimal(5,2) nullable
support_count unsigned integer nullable
support_percentage decimal(5,2) nullable
status string default classified
created_at
updated_at
```

Status yang diperbolehkan:

```text
classified
no_rule
```

Status tidak diterima dari request pengguna.

Case code dibuat oleh backend dengan format:

```text
CLS-YYYYMMDD-{ULID}
```

Gunakan ULID penuh agar risiko benturan kode tidak meningkat karena pemotongan identifier.

---

## 13. Model Classification

Buat:

```text
app/Models/Classification.php
```

Tambahkan cast:

```text
visit_date → date
date_of_birth → date
decision_path → array
confidence → decimal:2
support_percentage → decimal:2
```

Relasi:

```text
belongsTo User
belongsTo Doctor
belongsTo CurrentPromil
belongsTo CurrentAction
belongsTo Poa1Option
```

Pada model User tambahkan:

```php
public function classifications(): HasMany
{
    return $this->hasMany(Classification::class);
}
```

Buat constant atau enum internal untuk status apabila sesuai convention repository.

Jangan menerima status dari request pengguna.

---

# BAGIAN D — MODEL PENELITIAN

## 14. Penggunaan data latih

Data penelitian:

```text
126 data lengkap
→ split validation
→ 101 data latih
→ 25 data uji
→ pembentukan model di RapidMiner
→ menghasilkan decision tree
→ menghasilkan 14 decision rule
→ rule diterapkan pada Laravel
```

Laravel tidak menyimpan 101 data latih sebagai record aplikasi.

Laravel hanya menggunakan hasil model berupa:

* struktur cabang;
* threshold umur;
* predicted class;
* rule code;
* confidence;
* support count;
* support percentage.

Laravel tidak:

* membaca Excel data training saat klasifikasi;
* menghitung entropy;
* menghitung information gain;
* menghitung gain ratio;
* membentuk pohon keputusan baru;
* memperbarui rule otomatis.

---

## 15. Konfigurasi model

Buat:

```text
config/fertility.php
```

Isi:

```php
<?php

return [
    'training_age_range' => [
        'min' => 26,
        'max' => 59,
    ],

    'model' => [
        'algorithm' => 'Decision Tree C4.5',
        'criterion' => 'Gain Ratio',
        'training_count' => 101,
        'testing_count' => 25,
        'root_node' => 'Promil Saat Ini',
        'maximal_depth' => 4,
        'minimal_gain' => 0.001,
        'minimal_leaf_size' => 1,
        'pruning' => true,
        'prepruning' => true,
        'accuracy' => 36.00,
        'macro_precision' => 17.11,
        'macro_recall' => 22.92,
        'macro_f1' => 18.97,
    ],
];
```

Opsi dropdown tidak disimpan dalam config karena berasal dari tabel master.

---

## 16. DecisionTreeService

Buat:

```text
app/Services/DecisionTreeService.php
```

Service menerima data yang sudah dinormalisasi:

```php
[
    'age' => 35,
    'current_promil_code' => 'IVF',
    'current_action_code' => 'KONTROL_D2_D5',
]
```

PoA1 tidak dikirim ke service karena tidak digunakan pada pohon keputusan final.

Contoh hasil matched:

```php
[
    'matched' => true,
    'prediction' => 'PRO Stimulasi FET',
    'rule_code' => 'R6',
    'decision_path' => [
        'Promil Saat Ini = IVF',
        'Umur ≤ 40,5',
        'Promil Selanjutnya = PRO Stimulasi FET',
    ],
    'confidence' => 95.83,
    'support_count' => 24,
    'support_percentage' => 23.76,
]
```

Contoh hasil no-rule:

```php
[
    'matched' => false,
    'prediction' => null,
    'rule_code' => null,
    'decision_path' => [
        'Promil Saat Ini = IVF',
        'Umur > 40,5',
        'Tindakan Hari Ini tidak memiliki cabang pada model aktif',
        'Tidak ditemukan decision rule yang sesuai',
    ],
    'confidence' => null,
    'support_count' => null,
    'support_percentage' => null,
]
```

Decision rule tidak boleh ditulis pada controller.

Sediakan method:

```php
public function classify(array $attributes): array
```

Sediakan pula:

```php
public function rules(): array
```

Method `rules()` menjadi sumber metadata untuk halaman Informasi Model.

Jangan menduplikasi metadata rule pada controller atau frontend.

---

## 17. Decision rule final

Total data training:

```text
101
```

### R1

```text
Promil Saat Ini: ALAMI
Umur: minimal 29
Prediksi: PRO IVF
Support count: 42
Confidence: 40,48
Support percentage: 41,58
```

### R2

```text
Promil Saat Ini: ALAMI
Umur: 28
Prediksi: PRO Stimulasi IUI
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R3

```text
Promil Saat Ini: ALAMI
Umur: maksimal 27
Prediksi: PRO ST
Support count: 3
Confidence: 100,00
Support percentage: 2,97
```

### R4

```text
Promil Saat Ini: BELUM_PROMIL
Prediksi: PRO IUI/PRO IVF
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R5

```text
Promil Saat Ini: IUI
Prediksi: PRO IUI
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R6

```text
Promil Saat Ini: IVF
Umur: maksimal 40
Prediksi: PRO Stimulasi FET
Support count: 24
Confidence: 95,83
Support percentage: 23,76
```

### R7

```text
Promil Saat Ini: IVF
Umur: minimal 41
Tindakan: HO
Prediksi: PRO Stimulasi FET
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R8

```text
Promil Saat Ini: IVF
Umur: minimal 41
Tindakan: KONTROL_D2_D5
Prediksi: PRO Stimulasi FET
Support count: 3
Confidence: 66,67
Support percentage: 2,97
```

### R9

```text
Promil Saat Ini: IVF
Umur: minimal 41
Tindakan: PRP_OVARIUM_TRANSVAGINAL
Prediksi: PRO Stimulasi IVF
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R10

```text
Promil Saat Ini: IVF
Umur: minimal 41
Tindakan: PAKET_AKUPUNTUR_12X_KE_6
Prediksi: PRO Stimulasi FET
Support count: 1
Confidence: 100,00
Support percentage: 0,99
```

### R11

```text
Promil Saat Ini: PEMERIKSAAN_DASAR
Umur: minimal 47
Prediksi: PRO IUI
Support count: 2
Confidence: 50,00
Support percentage: 1,98
```

### R12

```text
Promil Saat Ini: PEMERIKSAAN_DASAR
Umur: 29 sampai 46
Prediksi: PRO IVF
Support count: 15
Confidence: 40,00
Support percentage: 14,85
```

### R13

```text
Promil Saat Ini: PEMERIKSAAN_DASAR
Umur: maksimal 28
Prediksi: PRO IUI
Support count: 3
Confidence: 100,00
Support percentage: 2,97
```

### R14

```text
Promil Saat Ini: SENGGAMA_TERJADWAL
Prediksi: PRO IUI
Support count: 3
Confidence: 66,67
Support percentage: 2,97
```

### Kondisi no-rule

```text
Promil Saat Ini: IVF
Umur: minimal 41
Tindakan: selain tindakan pada R7, R8, R9, atau R10
```

Hasil:

```php
[
    'matched' => false,
    'prediction' => null,
    'status' => 'no_rule',
]
```

Jangan memaksakan prediksi saat tidak ada cabang rule yang sesuai.

Jumlah support seluruh rule harus tetap berjumlah 101.

---

# BAGIAN E — BACKEND

## 18. StoreClassificationRequest

Buat:

```text
app/Http/Requests/StoreClassificationRequest.php
```

Validasi:

```php
use Illuminate\Validation\Rule;

return [
    'patient_name' => [
        'required',
        'string',
        'max:255',
    ],

    'visit_date' => [
        'required',
        'date',
    ],

    'date_of_birth' => [
        'required',
        'date',
        'before_or_equal:visit_date',
    ],

    'doctor_id' => [
        'required',
        Rule::exists('doctors', 'id')
            ->where(fn ($query) => $query->where('is_active', true)),
    ],

    'current_promil_id' => [
        'required',
        Rule::exists('current_promils', 'id')
            ->where(fn ($query) => $query->where('is_active', true)),
    ],

    'current_action_id' => [
        'required',
        Rule::exists('current_actions', 'id')
            ->where(fn ($query) => $query->where('is_active', true)),
    ],

    'poa1_option_id' => [
        'required',
        Rule::exists('poa1_options', 'id')
            ->where(fn ($query) => $query->where('is_active', true)),
    ],
];
```

Gunakan nama atribut dan pesan validasi berbahasa Indonesia.

Jangan menerima:

```text
age
prediction_result
rule_code
decision_path
confidence
support_count
support_percentage
status
user_id
case_code
```

Semua nilai tersebut dibuat oleh backend.

Setelah Form Request tervalidasi, backend tetap harus memastikan umur berada pada rentang 1–120 tahun.

---

## 19. ClassificationController

Buat:

```text
app/Http/Controllers/ClassificationController.php
```

Method:

```text
index
create
store
show
```

Tidak perlu:

```text
edit
update
destroy
```

### Index

Tampilkan hanya klasifikasi milik pengguna aktif.

```php
Classification::query()
    ->whereBelongsTo($request->user());
```

Gunakan eager loading:

```text
doctor
currentPromil
currentAction
poa1Option
```

Gunakan pagination 10 data dan urutkan terbaru.

Filter MVP:

```text
case_code
current_promil_id
prediction_result
status
```

Pertahankan query string filter.

Jangan menggunakan nama pasien sebagai filter GET karena akan menempatkan data pasien dalam URL dan riwayat browser.

### Create

Kirim props:

```text
doctors
currentPromils
currentActions
poa1Options
trainingAgeRange
masterAvailability
```

Hanya kirim master aktif.

Urutkan berdasarkan:

```text
sort_order
name
```

`masterAvailability` menjelaskan ketersediaan setiap master agar frontend dapat menonaktifkan submit.

### Store

Urutan proses:

1. validasi menggunakan StoreClassificationRequest;
2. ambil Doctor aktif;
3. ambil CurrentPromil aktif;
4. ambil CurrentAction aktif;
5. ambil Poa1Option aktif;
6. hitung umur terhadap tanggal kunjungan;
7. validasi umur 1–120;
8. kirim kode master ke DecisionTreeService;
9. buat case code;
10. simpan hasil dalam database transaction;
11. gunakan status `classified` saat matched;
12. gunakan status `no_rule` saat tidak matched;
13. redirect ke halaman show;
14. tampilkan flash message.

Service hanya menerima:

```php
[
    'age' => $age,
    'current_promil_code' => $currentPromil->code,
    'current_action_code' => $currentAction->code,
]
```

Jangan menggunakan nama tampilan master untuk rule.

### Show

Pengguna hanya boleh melihat klasifikasi miliknya sendiri.

Gunakan policy atau authorization yang mengikuti convention repository.

Kirim:

```text
classification dengan relasi
decision_path
disclaimer
training_count
training_age_range
age_outside_training_range
```

---

## 20. DashboardController

Buat atau perbarui DashboardController mengikuti pola route aktual repository.

Dashboard menampilkan data milik pengguna aktif:

```text
totalClassifications
todayClassifications
classifiedCount
noRuleCount
recentClassifications
resultDistribution
```

`recentClassifications` maksimal 5 record.

Gunakan eager loading pada daftar terbaru.

Distribusi hasil ditampilkan menggunakan horizontal bar berbasis Tailwind.

Jangan menambahkan library chart.

---

## 21. ModelInformationController

Buat:

```text
app/Http/Controllers/ModelInformationController.php
```

Kirim:

```text
informasi model dari config
daftar 14 rule dari DecisionTreeService::rules()
penjelasan penggunaan data latih
disclaimer
```

Jangan menulis ulang metadata 14 rule di controller.

---

## 22. Authorization

Semua halaman klasifikasi menggunakan middleware:

```text
auth
verified
```

Buat ClassificationPolicy atau gunakan mekanisme authorization yang sesuai convention repository.

Ketentuan minimal:

```text
viewAny: pengguna terautentikasi
create: pengguna terautentikasi dan verified
view: classification.user_id sama dengan user.id
```

Pengguna tidak boleh membaca klasifikasi milik pengguna lain.

---

## 23. Routing

Tambahkan route tanpa merusak route starter kit.

Contoh:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('classifications', ClassificationController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/model-information', ModelInformationController::class)
        ->name('model-information');
});
```

Untuk dashboard, pertahankan route yang sudah ada dan sesuaikan dengan convention controller aktual.

Frontend harus menggunakan Wayfinder.

Jangan hardcode:

```text
/classifications
/classifications/create
/model-information
/dashboard
```

pada komponen Svelte.

---

# BAGIAN F — FRONTEND

## 24. Halaman dan tipe data

Buat:

```text
resources/js/pages/classifications/Index.svelte
resources/js/pages/classifications/Create.svelte
resources/js/pages/classifications/Show.svelte
resources/js/pages/model/Index.svelte
resources/js/types/classification.ts
```

Perbarui dashboard dan sidebar aktual yang ditemukan pada repository.

Definisikan tipe:

```text
Doctor
CurrentPromil
CurrentAction
Poa1Option
Classification
ClassificationStatus
DecisionRule
ModelInformation
PaginatedData<T>
```

Hindari `any` apabila tipe dapat didefinisikan.

---

## 25. Halaman Klasifikasi Baru

Gunakan dua Card.

### Card Informasi Kunjungan

Field:

```text
Nama Lengkap
Tanggal Kunjungan
Tanggal Lahir
Umur
Dokter Pemeriksa
```

Umur:

1. dihitung otomatis pada frontend;
2. dihitung terhadap tanggal kunjungan;
3. read-only;
4. ditampilkan sebagai `35 tahun`;
5. kosong jika tanggal belum lengkap;
6. tidak perlu dikirim sebagai sumber kebenaran;
7. tampilkan warning jika di luar 26–59.

### Card Data Klasifikasi

Field:

```text
Promil Saat Ini
Tindakan Hari Ini
PoA1
```

Gunakan opsi dari props backend.

Jika salah satu master kosong:

1. tampilkan alert;
2. sebutkan master yang belum tersedia;
3. nonaktifkan tombol submit.

Tombol:

```text
Proses Klasifikasi
Batal
```

Gunakan loading state.

Cegah double submit.

Tampilkan validation error pada field masing-masing.

---

## 26. Halaman Hasil

Tampilkan:

```text
case code
nama pasien
tanggal kunjungan
tanggal lahir
umur
dokter pemeriksa
Promil Saat Ini
Tindakan Hari Ini
PoA1
status
hasil klasifikasi
rule
confidence
support count
support percentage
decision path
disclaimer
warning rentang umur
```

Saat status `classified`:

```text
Hasil Klasifikasi
PRO Stimulasi FET

Rule R6
Confidence 95,83%
Dukungan 24 dari 101 data latih
Support 23,76%
```

Saat status `no_rule`:

```text
Tidak ditemukan decision rule yang sesuai dengan kombinasi data yang
dimasukkan. Hasil memerlukan pemeriksaan lebih lanjut.
```

Jangan menampilkan nilai null sebagai:

```text
0%
0 data
Rule -
```

Tampilkan decision path sebagai stepper vertikal.

---

## 27. Halaman Riwayat

Kolom tabel:

```text
Case Code
Nama Pasien
Tanggal Kunjungan
Umur
Dokter
Promil Saat Ini
Hasil
Rule
Status
Aksi
```

Aksi:

```text
Lihat Detail
```

Filter:

```text
Case Code
Promil Saat Ini
Hasil
Status
```

Gunakan pagination Laravel.

Gunakan horizontal scroll pada layar kecil.

---

## 28. Dashboard

Tampilkan:

```text
Total Klasifikasi
Klasifikasi Hari Ini
Berhasil Diklasifikasikan
Tidak Ada Rule
Distribusi Hasil
Lima Data Terbaru
Tombol Klasifikasi Baru
```

Gunakan komponen UI yang tersedia dan pertahankan dark mode.

---

## 29. Informasi Model

Tampilkan:

```text
algoritma
criterion
jumlah data latih
jumlah data uji
root node
maximal depth
minimal gain
minimal leaf size
pruning
prepruning
accuracy
macro precision
macro recall
macro F1-score
```

Tambahkan bagian:

### Peran Data Latih

Gunakan penjelasan:

> Data latih sebanyak 101 data digunakan untuk membentuk pohon keputusan di RapidMiner. Aplikasi tidak membaca ulang data latih pada saat klasifikasi. Aplikasi menerapkan 14 decision rule yang dihasilkan oleh model.

Tampilkan seluruh 14 rule dari backend.

---

## 30. Sidebar

Tambahkan menu:

```text
Dashboard
Klasifikasi Baru
Riwayat Klasifikasi
Informasi Model
```

Ikon yang dapat digunakan:

```text
LayoutDashboard
ClipboardPlus atau FilePlus2
History
GitBranch atau Network
```

Gunakan Wayfinder untuk tujuan navigasi.

---

## 31. Formatter

Gunakan format Indonesia:

```text
tanggal: 16 Juli 2026
confidence: 95,83%
support: 23,76%
umur: 35 tahun
```

Buat helper lokal bila diperlukan.

Jangan menambahkan dependency hanya untuk formatting.

---

# BAGIAN G — SEEDER DAN FACTORY

## 32. Seeder

Buat:

```text
DoctorSeeder
CurrentPromilSeeder
CurrentActionSeeder
Poa1OptionSeeder
```

Ketentuan:

1. CurrentPromilSeeder memuat seluruh kode rule.
2. CurrentActionSeeder memuat seluruh kode tindakan rule dan LAINNYA.
3. DoctorSeeder tidak boleh mengarang nama dokter production.
4. Poa1OptionSeeder tidak boleh mengarang kategori penelitian.
5. Seeder menggunakan `updateOrCreate()` agar aman dijalankan kembali.
6. Data contoh hanya boleh digunakan pada local/testing dan harus diberi komentar yang jelas.

---

## 33. Factory

Buat:

```text
DoctorFactory
CurrentPromilFactory
CurrentActionFactory
Poa1OptionFactory
ClassificationFactory
```

Factory digunakan oleh Pest.

Test tidak boleh bergantung pada isi seeder production.

---

# BAGIAN H — TESTING

## 34. Unit test DecisionTreeService

Buat:

```text
tests/Unit/Services/DecisionTreeServiceTest.php
```

Gunakan Pest dataset untuk menguji seluruh R1 sampai R14.

Uji batas umur:

```text
umur 27 → R3
umur 28 → R2
umur 29 → R1
umur 40 → R6
umur 41 + HO → R7
umur 41 + KONTROL_D2_D5 → R8
umur 41 + PRP_OVARIUM_TRANSVAGINAL → R9
umur 41 + PAKET_AKUPUNTUR_12X_KE_6 → R10
umur 46 → R12
umur 47 → R11
```

Uji no-rule:

```text
Promil Saat Ini: IVF
Umur: 41
Tindakan: LAINNYA
```

Hasil:

```text
matched false
prediction null
rule_code null
```

Uji pula bahwa jumlah `support_count` metadata 14 rule sama dengan 101.

---

## 35. Feature test klasifikasi

Buat:

```text
tests/Feature/Classifications/ClassificationTest.php
```

Minimal menguji:

1. guest tidak dapat membuka halaman klasifikasi;
2. pengguna belum verified tidak dapat membuka halaman;
3. verified user dapat membuka form;
4. master aktif ditampilkan;
5. master nonaktif tidak ditampilkan;
6. validasi field wajib berjalan;
7. tanggal lahir tidak boleh melewati tanggal kunjungan;
8. umur dihitung backend dengan benar;
9. nilai umur dari request diabaikan;
10. umur kurang dari 1 atau lebih dari 120 ditolak;
11. klasifikasi valid tersimpan;
12. relasi master tersimpan;
13. hasil rule sesuai;
14. decision path tersimpan sebagai JSON;
15. no-rule tersimpan dengan status no_rule;
16. nilai confidence dan support no-rule tetap null;
17. pengguna hanya dapat melihat data miliknya;
18. pengguna tidak dapat melihat data pengguna lain;
19. filter index bekerja;
20. pagination bekerja;
21. dashboard hanya menggunakan data pengguna aktif;
22. master nonaktif ditolak saat store;
23. case code unik dibuat backend.

---

# BAGIAN I — KEAMANAN DAN PRIVASI

## 36. Ketentuan keamanan

Karena sistem menyimpan nama pasien:

1. semua halaman wajib menggunakan auth dan verified;
2. pengguna hanya melihat riwayat miliknya;
3. route show wajib melakukan authorization;
4. jangan menyediakan route publik;
5. jangan menulis nama pasien dalam log aplikasi;
6. jangan menaruh nama pasien pada URL;
7. jangan menggunakan nama pasien sebagai filter GET;
8. jangan menyimpan field request yang tidak diperlukan;
9. gunakan CSRF dan Inertia convention;
10. jangan menampilkan data milik pengguna lain;
11. gunakan eager loading untuk mencegah N+1 query;
12. jangan mengirim seluruh kolom model apabila frontend hanya membutuhkan beberapa kolom.

---

# BAGIAN J — PROTOKOL IMPLEMENTASI

## 37. Tahapan pengembangan

### Tahap 0 — Audit repository

Kerjakan:

```text
audit composer.json
audit package.json
audit routes
audit model User
audit migration
audit components/ui
audit sidebar
audit Wayfinder
audit pola Inertia
audit pola Form Request
audit pola Pest
audit script test, lint, check, dan build
```

Jangan mengubah kode.

Output:

```text
arsitektur aktual
perbedaan dengan spesifikasi
file yang akan dibuat
file yang akan diubah
potensi konflik
urutan implementasi
command validasi aktual
```

Berhenti setelah audit.

### Tahap 1 — Database dan domain dasar

Kerjakan:

```text
migration master
migration classifications
model master
model Classification
relationship User
scope
factory
seeder
```

Jangan membuat service, controller, route, atau frontend.

Berhenti setelah migration, test relevan, dan Pint dijalankan.

### Tahap 2 — Rule engine

Kerjakan:

```text
config fertility
DecisionTreeService
metadata 14 rule
unit test seluruh rule
unit test batas umur
unit test no-rule
```

Jangan membuat controller atau frontend.

Berhenti setelah unit test dan Pint dijalankan.

### Tahap 3 — Backend aplikasi

Kerjakan:

```text
StoreClassificationRequest
ClassificationController
DashboardController
ModelInformationController
Policy
Route
Feature test
```

Jangan mengerjakan UI penuh.

Berhenti setelah route dan feature test diperiksa.

### Tahap 4 — Frontend

Kerjakan:

```text
TypeScript types
Create page
Show page
Index page
Dashboard
Model Information
Sidebar
Formatter
Wayfinder integration
```

Jangan menambahkan dependency.

Berhenti setelah check, lint, dan build dijalankan.

### Tahap 5 — Integrasi dan quality assurance

Periksa:

```text
seluruh acceptance criteria
seluruh rule
authorization
warning umur
master kosong
no-rule
query ownership
N+1 query
Wayfinder
hardcoded URL
TypeScript
responsive layout
dark mode
test
Pint
check
lint
build
```

Perbaiki hanya masalah yang ditemukan.

---

## 38. Quality commands

Gunakan command aktual berdasarkan repository.

Command yang kemungkinan tersedia:

```bash
php artisan migrate
php artisan migrate:status
php artisan test
vendor/bin/pint --test
npm run check
npm run lint
npm run build
```

Jangan menjalankan `migrate:fresh` apabila terdapat data yang perlu dipertahankan.

Jangan berasumsi sebuah script tersedia sebelum memeriksa `composer.json` dan `package.json`.

---

## 39. Laporan setiap tahap

Setelah setiap tahap, tampilkan:

1. ringkasan pekerjaan;
2. file baru;
3. file yang diubah;
4. keputusan implementasi;
5. migration yang dibuat;
6. test yang dibuat;
7. command yang dijalankan;
8. hasil command sebenarnya;
9. masalah yang ditemukan;
10. sisa pekerjaan;
11. asumsi.

Berhenti setelah laporan.

---

## 40. Acceptance criteria

Implementasi dianggap selesai apabila:

* form terbagi menjadi Informasi Kunjungan dan Data Klasifikasi;
* umur dihitung terhadap tanggal kunjungan;
* frontend menampilkan preview umur;
* backend menghitung ulang umur;
* umur request tidak dipercaya;
* master digunakan melalui foreign key;
* master nonaktif tidak dapat digunakan;
* rule menggunakan kode master;
* seluruh 14 rule lulus unit test;
* jumlah support rule sama dengan 101;
* no-rule ditangani tanpa memaksakan prediksi;
* hasil klasifikasi tersimpan;
* decision path tersimpan;
* confidence dan support tersimpan;
* riwayat ditampilkan;
* ownership diterapkan;
* dashboard tersedia;
* informasi model tersedia;
* Wayfinder digunakan;
* tidak ada URL frontend yang di-hardcode;
* auth starter kit tetap berjalan;
* tidak ada dependency yang tidak diperlukan;
* Pest lulus;
* Pint lulus;
* svelte-check atau npm check lulus;
* lint lulus;
* Vite build berhasil.

Mulai hanya dari Tahap 0: Audit Repository.

Jangan mengubah kode sebelum audit selesai.
