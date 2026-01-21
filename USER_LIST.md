# Daftar User Sistem Management Surat

Berikut adalah daftar user yang tersedia untuk testing aplikasi. Semua password default adalah `123`.

## Top Level Management
| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Admin** | `admin` | `123` | Super Admin |
| **Direktur Utama** | `dirut` | `123` | Direktur Utama RS Azra |
| **Direktur ASP** | `dirut_asp` | `123` | Direktur PT. Arthasabena Putra |
| **General Manager** | `general_manager` | `123` | General Manager |

## Sekretaris
| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Sekretaris** | `sekretaris` | `123` | Sekretaris RS Azra |
| **Sekretaris ASP** | `sekretaris_asp` | `123` | Sekretaris PT. Arthasabena Putra |

## Managerial & Unit
| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Manager Keuangan** | `manager_keuangan` | `123` | Manager Keuangan (Bawahan GM) |
| **Manager IT** | `manager_it` | `123` | Manager Departemen IT |
| **Kepala Komite** | `kepala.komite` | `123` | Kepala Komite Etik |

## Staff
| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Staff IT** | `staff_it` | `123` | Staff Departemen IT |
| **Staff Keuangan** | `staff_keuangan` | `123` | Staff Keuangan Independen |
| **Anggota Komite** | `anggota.komite` | `123` | Anggota Komite Etik |

---
**Catatan:**
- Gunakan `php artisan db:seed` untuk me-reset dan mengisi ulang data user ini.
- Password `123` diset secara default di seeder.
