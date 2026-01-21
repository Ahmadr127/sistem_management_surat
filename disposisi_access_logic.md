# Dokumentasi Logika Akses & Filter Disposisi

Dokumen ini menjelaskan logika akses (siapa yang bisa melihat/mengedit) dan logika filter (siapa yang bisa dipilih) untuk fitur "Tujuan Disposisi" pada aplikasi Sistem Manajemen Surat.

## 1. Ikhtisar Akses

Fitur Disposisi terdapat di dua area utama:
1.  **Surat Keluar (Buat/Edit):** Digunakan saat membuat atau mengedit surat keluar baru.
2.  **Surat Masuk (Edit Disposisi):** Digunakan untuk menindaklanjuti surat masuk yang sudah ada.

### Matriks Akses

| Role | Surat Keluar (Buat/Edit) | Surat Masuk (Edit Disposisi) | Bagian yang Bisa Diedit (Surat Masuk) |
| :--- | :--- | :--- | :--- |
| **Sekretaris (1)** | Ya | Ya | Status/Ket. Sekretaris |
| **Direktur (2)** | Ya | Ya | Status/Ket. Direktur & Tujuan Disposisi |
| **Admin (0/3)** | Ya | Ya | Semua Bagian |
| **Manager (4)** | Ya | Ya | Semua Bagian |
| **Sekretaris ASP (5)** | Ya | Ya | Status/Ket. Sekretaris |
| **GM (6)** | Ya | Ya | Semua Bagian |
| **Manager Keuangan (7)**| Ya | Ya | Semua Bagian |
| **Direktur ASP (8)** | Ya | Ya | Status/Ket. Direktur & Tujuan Disposisi |

---

## 2. Logika Filter User (Siapa yang bisa dipilih?)

Daftar user yang muncul dalam pencarian "Tujuan Disposisi" difilter berdasarkan role user yang sedang login. Logika ini diterapkan konsisten baik di Surat Keluar maupun Surat Masuk.

### Aturan Umum
*   User yang sedang login **tidak pernah** muncul dalam daftar (tidak bisa mendisposisikan ke diri sendiri).
*   Hanya user dengan status akun **'aktif'** yang muncul.

### Aturan Berdasarkan Role Pengguna

#### A. Jika Pengguna adalah Sekretaris ASP (Role 5)
Hanya dapat melihat/memilih user dengan role:
*   **Sekretaris (1)**
*   **Direktur (2)**
*   **General Manager (6)**
*   **Manager Keuangan (7)**
*   **Direktur ASP (8)**

#### B. Jika Pengguna adalah Manager (Role 4)
Dapat melihat/memilih user dengan role:
*   **Sekretaris (1)**
*   **Direktur (2)**
*   **Manager (4)**
*   **Sekretaris ASP (5)**
*   **Manager Keuangan (7)**
*   **Direktur ASP (8)**
*   **General Manager (6)** -> *HANYA JIKA* user tersebut adalah atasan langsung (GM) dari Manager yang sedang login.

#### C. Pengguna Lainnya (Admin, Direktur, Staff, dll)
Dapat melihat/memilih user dengan role:
*   **Sekretaris (1)**
*   **Direktur (2)**
*   **Manager (4)**
*   **Sekretaris ASP (5)**
*   **Manager Keuangan (7)**
*   **Direktur ASP (8)**
*   *(Catatan: Role 6 (GM) tidak muncul secara default untuk grup ini kecuali ada logika khusus yang ditambahkan di masa depan)*

---

## 3. Detail Implementasi Teknis

### A. Surat Keluar (`suratkeluar.blade.php` & `editsuratkeluar.blade.php`)
*   **Frontend:** Menggunakan komponen Blade `<x-tujuan-disposisi :users="$users" />`.
*   **Backend:** Controller mengirimkan *semua* user aktif (kecuali diri sendiri).
*   **Filtering:** Dilakukan di sisi **Frontend** (dalam file `resources/views/components/tujuan-disposisi.blade.php`) menggunakan logika PHP `@if` di dalam loop `@foreach`.

### B. Surat Masuk (`edit_disposisi.blade.php`)
*   **Frontend:** Menggunakan JavaScript `loadTujuanDisposisiMasuk()` yang memanggil API.
*   **Backend:** API Endpoint `/api/disposisi/{id}/tujuan` yang ditangani oleh `DisposisiController::getTujuanDisposisiWithUsers`.
*   **Filtering:** Dilakukan di sisi **Backend** (dalam `DisposisiController.php`). Query database dimodifikasi untuk mencerminkan logika yang sama persis dengan komponen Blade di atas.

---

## 4. Perilaku Khusus Role pada Modal Edit Disposisi

Pada modal edit disposisi surat masuk (`edit_disposisi.blade.php`), tampilan formulir disesuaikan secara dinamis:

1.  **Grup Sekretaris (Role 1 & 5)**
    *   **Tampil:** Hanya bagian "Bagian Sekretaris" (Status, Waktu Review, Keterangan).
    *   **Tersembunyi:** Bagian "Bagian Direktur" dan input "Tujuan Disposisi".
    *   **Aksi Simpan:** Hanya memperbarui data terkait sekretaris.

2.  **Grup Direktur (Role 2 & 8)**
    *   **Tampil:** Bagian "Bagian Direktur" dan input "Tujuan Disposisi".
    *   **Tersembunyi:** Bagian "Bagian Sekretaris".
    *   **Aksi Simpan:** Memperbarui data direktur dan daftar tujuan disposisi.

3.  **Grup Lainnya (Admin, Manager, dll)**
    *   **Tampil:** Semua bagian (Sekretaris, Direktur, Tujuan Disposisi).
    *   **Aksi Simpan:** Memperbarui semua data.
