# Review Permissions & Roles: Surat Masuk & Disposisi

## 1. List Role yang Digunakan
Berdasarkan `database/seeders/UserSeeder.php` dan penggunaan di aplikasi:

| Role ID | Nama Role | Keterangan |
|---------|-----------|------------|
| 0 | Staff | User biasa / Staff Unit |
| 1 | Sekretaris | Sekretaris Utama |
| 2 | Direktur | Direktur Utama |
| 3 | Admin | Admin IT / System Admin |
| 4 | Manager | Manager Unit |
| 5 | Sekretaris ASP | Asisten Pribadi (Sekretaris Khusus) |
| 6 | General Manager | General Manager |
| 7 | Manager Keuangan | Manager Keuangan |
| 8 | Direktur ASP | Direktur Asisten Pribadi |

## 2. Review Permission `getSuratMasuk`
Analisis berdasarkan `SuratMasukController.php` (method `getSuratMasuk`) dan `suratmasuk.blade.php`.

### Logic Filter per Role:

*   **Role 0 (Staff) & Role 3 (Admin)**
    *   **API:** Mengirim `user_id` (ID user login) dan `include_created=true`.
    *   **Controller:** Mengambil surat dimana user adalah **penerima disposisi** ATAU **pembuat surat**.
    *   **Frontend Filter:** Memfilter ulang untuk menampilkan:
        1.  Surat dimana user adalah tujuan disposisi.
        2.  Surat yang dibuat user DAN `status_dirut` sudah `approved`.

*   **Role 1 (Sekretaris)**
    *   **API:** Mengirim `all=true`.
    *   **Controller:** Mengambil **SEMUA** data surat masuk tanpa filter.
    *   **Frontend Filter:** Menampilkan semua data.

*   **Role 2 (Direktur)**
    *   **API:** Mengirim `status_sekretaris=approved`.
    *   **Controller:** Hanya mengambil surat yang `status_sekretaris`-nya sudah `approved`.
    *   **Frontend Filter:** Memastikan user adalah tujuan disposisi ATAU pembuat surat (Logic ini di frontend sepertinya agak redundan atau membatasi view Direktur hanya ke surat terkait, padahal API sudah memfilter by status approval sekretaris).

*   **Role 4 (Manager), 6 (General Manager), 7 (Manager Keuangan)**
    *   **API:** Mengirim `user_id` dan `include_created=true`.
    *   **Controller:** Mengambil surat dimana user adalah **penerima disposisi** ATAU **pembuat surat**.
    *   **Frontend Filter:** Menampilkan surat terkait (tujuan atau pembuat).

*   **Role 5 (Sekretaris ASP)**
    *   **API:** Mengirim `user_id` dan `include_created=true`.
    *   **Controller:** Mengambil surat dimana user adalah **penerima disposisi** ATAU **pembuat surat**.
    *   **Frontend Filter:** Menampilkan surat terkait.

*   **Role 8 (Direktur ASP)**
    *   **API:** Mengirim `status_sekretaris_asp=approved`.
    *   **Controller:** Hanya mengambil surat yang `status_sekretaris`-nya sudah `approved` (Logic controller menggunakan `status_sekretaris` untuk query parameter `status_sekretaris_asp`).
    *   **Frontend Filter:** Memastikan user adalah tujuan disposisi ATAU pembuat surat.

## 3. Role Akses Modal Edit Disposisi & Tujuan Disposisi
Analisis berdasarkan `suratmasuk.blade.php` (fungsi `editDisposisi` dan `saveDisposisi`) serta `DisposisiController.php`.

### Akses Modal Edit Disposisi
Modal ini hanya dapat diakses (tombol muncul) oleh:
*   **Role 1 (Sekretaris)**
*   **Role 2 (Direktur)**
*   **Role 5 (Sekretaris ASP)**
*   **Role 8 (Direktur ASP)**

### Pembagian View/Akses dalam Modal:

#### A. Section Sekretaris (Role 1 & 5)
*   **Akses:** Hanya terlihat oleh Role 1 dan 5.
*   **Fitur:**
    *   Mengubah `Status Sekretaris` (Pending, Review, Approved, Rejected).
    *   Tidak bisa melihat atau mengubah "Tujuan Disposisi".

#### B. Section Direktur (Role 2 & 8)
*   **Akses:** Hanya terlihat oleh Role 2 dan 8.
*   **Fitur:**
    *   Mengubah `Status Direktur` (Pending, Review, Approved, Rejected).
    *   Mengisi `Keterangan Direktur`.
    *   **Mengelola Tujuan Disposisi** (Hanya muncul jika status `approved`).

### Penjelasan "Tujuan Disposisi"
*   **Fungsi:** Memilih user mana saja yang akan menerima disposisi surat ini.
*   **Controller (`DisposisiController@getTujuanDisposisiWithUsers`):**
    *   Mengambil daftar user yang tersedia untuk dipilih.
    *   **Filter User Tersedia:** User dengan Role 1, 2, 4, 5, 6, 7, 8.
    *   **Pengecualian:** Role 0 (Staff) dan Role 3 (Admin) **TIDAK** termasuk dalam daftar user yang bisa dipilih sebagai tujuan disposisi baru (hardcoded di controller).

### Temuan Penting (Bug Report)
Pada `suratmasuk.blade.php`, terdapat potensi bug pada logic **Role 2 (Direktur)** di dalam fungsi `editDisposisi`:
*   Untuk **Role 8**, kode memanggil `loadDisposisiUsers(data.disposisi.id)` untuk memuat daftar user.
*   Untuk **Role 2**, kode **TIDAK** memanggil `loadDisposisiUsers`. Akibatnya, Direktur Utama mungkin tidak melihat daftar user untuk disposisi meskipun status diubah menjadi `approved`.

## 4. Kesimpulan File Controller & Seeder
*   **`UserSeeder.php`**: Mendefinisikan 8 role utama dan membuat user dummy untuk masing-masing role.
*   **`SuratMasukController.php`**: Menangani logic pengambilan data surat dengan filter yang cukup kompleks berdasarkan role. Logic filter terbagi antara query database (Controller) dan filter array javascript (Frontend).
*   **`DisposisiController.php`**: Menangani logic update status dan pengambilan data user untuk disposisi. Membatasi user yang bisa dipilih sebagai tujuan disposisi (mengecualikan Staff & Admin).
