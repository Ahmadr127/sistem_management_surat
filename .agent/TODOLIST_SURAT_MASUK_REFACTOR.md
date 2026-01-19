# 📋 TODOLIST REFACTORING SURAT MASUK

## ✅ COMPLETED
- [x] Buat index.blade.php baru dengan server-side rendering
- [x] Implementasi searchable dropdown untuk pencarian
- [x] Implementasi filter (tanggal, jenis, sifat, status)
- [x] Implementasi tabel dengan Laravel pagination

## 🔄 IN PROGRESS

### 1. UPDATE CONTROLLER
- [ ] Update SuratMasukController@index method
  - [ ] Implement role-based data filtering
  - [ ] Implement search functionality
  - [ ] Implement date range filter
  - [ ] Implement jenis_surat filter
  - [ ] Implement sifat_surat filter
  - [ ] Implement status_sekretaris filter
  - [ ] Implement status_dirut filter
  - [ ] Prepare $suratOptions for searchable dropdown
  - [ ] Implement pagination (10 items per page)
  - [ ] Return view with data

### 2. CREATE MODAL COMPONENTS
- [ ] Create modal/detail_surat.blade.php
  - [ ] Modal structure with Alpine.js
  - [ ] Display surat information
  - [ ] Display disposisi information
  - [ ] Display tujuan disposisi
  - [ ] Display keterangan penerima
  - [ ] Display keterangan pengirim
  - [ ] Display file attachments
  - [ ] Preview & download file buttons
  - [ ] JavaScript for loading data
  - [ ] Error handling

- [ ] Create modal/edit_disposisi.blade.php
  - [ ] Modal structure with Alpine.js
  - [ ] Role-based form sections
  - [ ] Sekretaris section (status only)
  - [ ] Direktur section (status + keterangan + tujuan)
  - [ ] Tujuan disposisi with search & checkboxes
  - [ ] JavaScript for loading data
  - [ ] JavaScript for saving data
  - [ ] Error handling

### 3. CREATE API ENDPOINTS (if needed)
- [ ] Check if API endpoints exist:
  - [ ] GET /api/surat-masuk/{id} - Get detail
  - [ ] GET /api/disposisi/surat/{id} - Get disposisi by surat
  - [ ] POST /api/disposisi/{id}/update - Update disposisi
  - [ ] GET /api/disposisi/{id}/tujuan - Get tujuan with users

### 4. UPDATE ROUTES
- [ ] Verify route('suratmasuk.index') exists
- [ ] Verify API routes exist

### 5. TESTING
- [ ] Test index page loading
- [ ] Test filters functionality
- [ ] Test searchable dropdown
- [ ] Test pagination
- [ ] Test detail modal
- [ ] Test edit disposisi modal
- [ ] Test role-based access
- [ ] Test file preview & download

### 6. CLEANUP
- [ ] Backup old suratmasuk.blade.php
- [ ] Remove old file or rename to suratmasuk.blade.php.old
- [ ] Remove unused JavaScript files
- [ ] Update navigation links if needed

## 📝 NOTES
- Follow the same pattern as surat_keluar
- Use Alpine.js for modals (not jQuery)
- Use server-side pagination (not client-side)
- Use searchable-dropdown component
- Maintain role-based access control
- Keep UI/UX consistent

## ⚠️ IMPORTANT
- Test thoroughly before deploying
- Ensure backward compatibility
- Keep old file as backup
- Document any breaking changes
