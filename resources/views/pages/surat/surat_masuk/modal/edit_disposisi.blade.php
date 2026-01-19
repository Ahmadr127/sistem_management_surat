{{-- Modal Edit Disposisi Surat Masuk --}}
<x-modal-dinamis id="edit-disposisi-masuk" title="Edit Disposisi Surat" size="lg" :showFooter="true">
    <x-slot:trigger>
        {{-- Trigger akan dipanggil dari JavaScript --}}
    </x-slot:trigger>

    <form id="edit-disposisi-form-masuk" class="space-y-4">
        {{-- Hidden inputs --}}
        <input type="hidden" id="surat_id_masuk" name="surat_id" value="">
        <input type="hidden" id="disposisi_id_masuk" name="disposisi_id" value="">

        {{-- Bagian Sekretaris --}}
        <div id="sekretaris-section-masuk" class="border-b border-gray-200 pb-4">
            <h4 class="text-md font-medium text-gray-800 mb-3">Bagian Sekretaris</h4>
            
            <div class="space-y-4">
                <x-form-select
                    name="status_sekretaris"
                    label="Status"
                    :options="[
                        'pending' => 'Menunggu',
                        'review' => 'Sedang Ditinjau',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak'
                    ]"
                />

                <x-form-input
                    type="datetime-local"
                    name="waktu_review_sekretaris"
                    label="Waktu Review"
                />

                <x-form-input
                    type="textarea"
                    name="keterangan_sekretaris"
                    label="Keterangan"
                    rows="3"
                    placeholder="Tambahkan catatan disposisi..."
                />
            </div>
        </div>

        {{-- Bagian Direktur --}}
        <div id="direktur-section-masuk" class="border-b border-gray-200 pb-4">
            <h4 class="text-md font-medium text-gray-800 mb-3">Bagian Direktur</h4>
            
            <div class="space-y-4">
                <x-form-select
                    name="status_dirut"
                    label="Status"
                    :options="[
                        'pending' => 'Menunggu',
                        'review' => 'Sedang Ditinjau',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak'
                    ]"
                />

                <x-form-input
                    type="datetime-local"
                    name="waktu_review_dirut"
                    label="Waktu Review"
                />

                <x-form-input
                    type="textarea"
                    name="keterangan_dirut"
                    label="Keterangan"
                    rows="3"
                    placeholder="Tambahkan catatan disposisi..."
                />
            </div>
        </div>

        {{-- Tujuan Disposisi menggunakan searchable-dropdown --}}
        <div id="tujuan-disposisi-section-masuk">
            <h4 class="text-md font-medium text-gray-800 mb-3">Tujuan Disposisi</h4>
            
            <div id="tujuan-disposisi-container-masuk">
                {{-- Akan diisi dinamis oleh JavaScript --}}
                <div class="text-center py-4 text-gray-500">
                    <i class="ri-loader-4-line animate-spin text-2xl"></i>
                    <p class="mt-2 text-sm">Memuat data...</p>
                </div>
            </div>
        </div>
    </form>

    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <x-button variant="secondary" @click="close()">
                Batal
            </x-button>
            <x-button variant="success" icon="ri-save-line" id="save-disposisi-button-masuk">
                Simpan Perubahan
            </x-button>
        </div>
    </x-slot:footer>
</x-modal-dinamis>

@push('scripts')
<script>
// Fungsi untuk membuka modal edit disposisi surat masuk
window.openEditDisposisiSuratMasuk = function(suratId) {
    // Trigger modal
    window.dispatchEvent(new CustomEvent('open-modal-edit-disposisi-masuk'));
    
    // Load data disposisi
    loadDisposisiDataMasuk(suratId);
};

// Load disposisi data
async function loadDisposisiDataMasuk(suratId) {
    try {
        // Show loading state
        const container = document.getElementById('tujuan-disposisi-container-masuk');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="ri-loader-4-line animate-spin text-2xl"></i>
                    <p class="mt-2 text-sm">Memuat data...</p>
                </div>
            `;
        }
        
        // Set surat ID
        document.getElementById('surat_id_masuk').value = suratId;
        
        // Fetch disposisi data
        const response = await fetch(`/api/disposisi/surat/${suratId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.disposisi) {
            const disposisi = data.disposisi;
            
            // Set disposisi ID
            document.getElementById('disposisi_id_masuk').value = disposisi.id || '';
            
            // Fill form fields
            document.querySelector('[name="status_sekretaris"]').value = disposisi.status_sekretaris || 'pending';
            document.querySelector('[name="keterangan_sekretaris"]').value = disposisi.keterangan_sekretaris || '';
            document.querySelector('[name="status_dirut"]').value = disposisi.status_dirut || 'pending';
            document.querySelector('[name="keterangan_dirut"]').value = disposisi.keterangan_dirut || '';
            
            // Set waktu review
            if (disposisi.waktu_review_sekretaris) {
                const waktuSekretaris = disposisi.waktu_review_sekretaris.replace(' ', 'T').substring(0, 16);
                document.querySelector('[name="waktu_review_sekretaris"]').value = waktuSekretaris;
            }
            if (disposisi.waktu_review_dirut) {
                const waktuDirut = disposisi.waktu_review_dirut.replace(' ', 'T').substring(0, 16);
                document.querySelector('[name="waktu_review_dirut"]').value = waktuDirut;
            }
            
            // Show/hide sections based on role
            const userRole = window.userRole;
            const sekretarisSection = document.getElementById('sekretaris-section-masuk');
            const direkturSection = document.getElementById('direktur-section-masuk');
            const tujuanSection = document.getElementById('tujuan-disposisi-section-masuk');
            
            if (userRole === 1) { // Sekretaris
                sekretarisSection.style.display = 'block';
                direkturSection.style.display = 'none';
                tujuanSection.style.display = 'none';
            } else if (userRole === 2) { // Direktur
                sekretarisSection.style.display = 'none';
                direkturSection.style.display = 'block';
                tujuanSection.style.display = 'block';
                
                // Load tujuan disposisi only if disposisi exists
                if (disposisi.id) {
                    loadTujuanDisposisiMasuk(disposisi.id);
                } else {
                    if (container) {
                        container.innerHTML = '<p class="text-gray-500 text-sm">Disposisi belum dibuat. Simpan terlebih dahulu untuk menambah tujuan.</p>';
                    }
                }
            } else { // Admin or other roles
                sekretarisSection.style.display = 'block';
                direkturSection.style.display = 'block';
                tujuanSection.style.display = 'block';
                
                if (disposisi.id) {
                    loadTujuanDisposisiMasuk(disposisi.id);
                }
            }
        } else {
            throw new Error(data.message || 'Gagal memuat data disposisi');
        }
    } catch (error) {
        console.error('Error loading disposisi:', error);
        
        // Close modal
        window.dispatchEvent(new CustomEvent('close-modal-edit-disposisi-masuk'));
        
        // Show error
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memuat data disposisi: ' + error.message
            });
        } else {
            alert('Gagal memuat data disposisi: ' + error.message);
        }
    }
}

// Load tujuan disposisi dengan searchable dropdown
async function loadTujuanDisposisiMasuk(disposisiId) {
    const container = document.getElementById('tujuan-disposisi-container-masuk');
    
    // Show loading
    container.innerHTML = `
        <div class="text-center py-4 text-gray-500">
            <i class="ri-loader-4-line animate-spin text-2xl"></i>
            <p class="mt-2 text-sm">Memuat tujuan disposisi...</p>
        </div>
    `;
    
    try {
        const response = await fetch(`/api/disposisi/${disposisiId}/tujuan`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Get selected user IDs
            const selectedIds = data.tujuan ? data.tujuan.map(t => t.id) : [];
            
            // Create checkboxes for users
            const users = data.available_users || [];
            
            if (users.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada user tersedia untuk disposisi</p>';
                return;
            }
            
            // Render checkboxes
            let html = '<div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">';
            users.forEach(user => {
                const isChecked = selectedIds.includes(user.id);
                const jabatan = user.jabatan ? user.jabatan.nama_jabatan : '';
                html += `
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                        <input type="checkbox" 
                               name="tujuan_disposisi[]" 
                               value="${user.id}" 
                               ${isChecked ? 'checked' : ''}
                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">${user.name}${jabatan ? ' <span class="text-gray-500">(' + jabatan + ')</span>' : ''}</span>
                    </label>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        } else {
            throw new Error(data.message || 'Gagal memuat tujuan disposisi');
        }
    } catch (error) {
        console.error('Error loading tujuan:', error);
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="ri-error-warning-line text-red-500 text-2xl mb-2"></i>
                <p class="text-red-500 text-sm">Gagal memuat data tujuan disposisi</p>
                <p class="text-gray-500 text-xs mt-1">${error.message}</p>
            </div>
        `;
    }
}

// Save disposisi
document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.getElementById('save-disposisi-button-masuk');
    if (saveBtn) {
        saveBtn.addEventListener('click', async function() {
            const disposisiId = document.getElementById('disposisi_id_masuk').value;
            const suratId = document.getElementById('surat_id_masuk').value;
            
            if (!suratId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID Surat tidak valid'
                });
                return;
            }
            
            // Disable button
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Menyimpan...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('surat_keluar_id', suratId);
            
            if (window.userRole === 1) { // Sekretaris
                formData.append('status_sekretaris', document.querySelector('[name="status_sekretaris"]').value);
                formData.append('keterangan_sekretaris', document.querySelector('[name="keterangan_sekretaris"]').value);
                const waktuSekretaris = document.querySelector('[name="waktu_review_sekretaris"]').value;
                if (waktuSekretaris) formData.append('waktu_review_sekretaris', waktuSekretaris);
            } else if (window.userRole === 2) { // Direktur
                formData.append('status_dirut', document.querySelector('[name="status_dirut"]').value);
                formData.append('keterangan_dirut', document.querySelector('[name="keterangan_dirut"]').value);
                const waktuDirut = document.querySelector('[name="waktu_review_dirut"]').value;
                if (waktuDirut) formData.append('waktu_review_dirut', waktuDirut);
                
                // Tujuan disposisi
                document.querySelectorAll('input[name="tujuan_disposisi[]"]:checked').forEach(cb => {
                    formData.append('tujuan_disposisi[]', cb.value);
                });
            }
            
            try {
                const endpoint = disposisiId ? `/api/disposisi/${disposisiId}/update` : `/api/disposisi/store`;
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Disposisi berhasil disimpan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Close modal
                    window.dispatchEvent(new CustomEvent('close-modal-edit-disposisi-masuk'));
                    
                    // Reload page
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Gagal menyimpan disposisi');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            } finally {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = '<i class="ri-save-line mr-1"></i> Simpan Perubahan';
            }
        });
    }
});
</script>
@endpush
