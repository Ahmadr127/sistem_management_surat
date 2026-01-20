---
description: Panduan menambahkan user baru dengan sistem Organization Unit
---

# Menambahkan User Baru

Dengan dihapusnya sistem Jabatan, penambahan user baru sekarang melibatkan pemilihan Role dan Organization Unit.

## Langkah-langkah

1.  **Login sebagai Super Admin**.
2.  Masuk ke menu **Pengguna & Akses** -> **Manage User**.
3.  Klik tombol **Tambah User**.
4.  Isi form:
    *   **Nama**: Nama lengkap user.
    *   **Username**: Username unik untuk login.
    *   **Email**: Email user (opsional).
    *   **Password**: Password default.
    *   **Role**: Pilih role yang sesuai (misal: Staff, Manager, Direktur).
    *   **Unit Organisasi**: Pilih unit organisasi tempat user bekerja (misal: IT, HR, Keuangan).
        *   *Catatan*: Untuk role level tinggi seperti Direktur Utama, Unit Organisasi bisa dikosongkan jika tidak terikat unit spesifik.
    *   **Manager / General Manager**: Pilih atasan langsung jika ada (untuk struktur pelaporan).
5.  Klik **Simpan**.

## Struktur Data

User sekarang terhubung ke:
*   `roles` (melalui `role_id` atau kolom `role` legacy).
*   `organization_units` (melalui `organization_unit_id`).

Nama jabatan yang ditampilkan di profil dihasilkan secara dinamis:
*   Jika Role memiliki nama spesifik (misal "Direktur Utama"), itu yang ditampilkan.
*   Jika Role adalah "Manager" atau "Staff", akan digabungkan dengan nama Unit (misal "Manager IT", "Staff HR").
