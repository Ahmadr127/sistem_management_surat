{{-- Modal Edit Disposisi Surat Masuk --}}
<x-modal-dinamis id="edit-disposisi-masuk" title="Edit Disposisi Surat" size="lg" :showFooter="true">
    <x-slot:trigger>
        {{-- Trigger akan dipanggil dari JavaScript --}}
    </x-slot:trigger>

    <form id="edit-disposisi-form-masuk" class="space-y-4" method="POST" onsubmit="return false;">
        {{-- Hidden inputs --}}
        <input type="hidden" id="surat_id_masuk" value="">
        <input type="hidden" id="disposisi_id_masuk" value="">

        {{-- Bagian Sekretaris --}}
        <div id="sekretaris-section-masuk" class="border-b border-gray-200 pb-4">
            <h4 class="text-md font-medium text-gray-800 mb-3">Bagian Sekretaris</h4>
            
            <div class="space-y-4">
                {{-- Status Sekretaris - Native Select --}}
                <div class="space-y-2">
                    <label for="status_sekretaris_masuk" class="text-sm font-semibold text-gray-800">Status</label>
                    <div class="relative">
                        <select
                            id="status_sekretaris_masuk"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white"
                        >
                            <option value="pending">Menunggu</option>
                            <option value="review">Sedang Ditinjau</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                </div>

                {{-- Waktu Review Sekretaris --}}
                <div class="space-y-2">
                    <label for="waktu_review_sekretaris_masuk" class="text-sm font-semibold text-gray-800">Waktu Review</label>
                    <input 
                        type="datetime-local" 
                        id="waktu_review_sekretaris_masuk"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 bg-white"
                    >
                </div>

                {{-- Keterangan Sekretaris --}}
                <div class="space-y-2">
                    <label for="keterangan_sekretaris_masuk" class="text-sm font-semibold text-gray-800">Keterangan</label>
                    <textarea 
                        id="keterangan_sekretaris_masuk"
                        rows="3"
                        placeholder="Tambahkan catatan disposisi..."
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 bg-white"
                    ></textarea>
                </div>
            </div>
        </div>

        {{-- Bagian Direktur --}}
        <div id="direktur-section-masuk" class="border-b border-gray-200 pb-4">
            <h4 class="text-md font-medium text-gray-800 mb-3">Bagian Direktur</h4>
            
            <div class="space-y-4">
                {{-- Status Direktur - Native Select --}}
                <div class="space-y-2">
                    <label for="status_dirut_masuk" class="text-sm font-semibold text-gray-800">Status</label>
                    <div class="relative">
                        <select
                            id="status_dirut_masuk"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white"
                        >
                            <option value="pending">Menunggu</option>
                            <option value="review">Sedang Ditinjau</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                </div>

                {{-- Waktu Review Direktur --}}
                <div class="space-y-2">
                    <label for="waktu_review_dirut_masuk" class="text-sm font-semibold text-gray-800">Waktu Review</label>
                    <input 
                        type="datetime-local" 
                        id="waktu_review_dirut_masuk"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 bg-white"
                    >
                </div>

                {{-- Keterangan Direktur --}}
                <div class="space-y-2">
                    <label for="keterangan_dirut_masuk" class="text-sm font-semibold text-gray-800">Keterangan</label>
                    <textarea 
                        id="keterangan_dirut_masuk"
                        rows="3"
                        placeholder="Tambahkan catatan disposisi..."
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 bg-white"
                    ></textarea>
                </div>
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
            <button type="button" 
                    onclick="saveDisposisiMasuk()" 
                    id="save-disposisi-button-masuk"
                    class="inline-flex items-center px-6 py-3 font-medium border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 text-white bg-green-600 hover:bg-green-700 focus:ring-green-500">
                <i class="ri-save-line mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </x-slot:footer>
</x-modal-dinamis>

@push('scripts')
<script>
// Prevent form inputs from triggering page refresh
document.addEventListener('DOMContentLoaded', function() {
    const modalForm = document.getElementById('edit-disposisi-form-masuk');
    if (modalForm) {
        // Prevent form submission on Enter key
        modalForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });
        
        // Stop event propagation on all inputs/selects to prevent external handlers
        modalForm.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.addEventListener('change', function(e) {
                e.stopPropagation();
            });
        });
    }
});

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
            
            // Fill form fields using specific IDs to avoid conflicts
            document.getElementById('status_sekretaris_masuk').value = disposisi.status_sekretaris || 'pending';
            document.getElementById('keterangan_sekretaris_masuk').value = disposisi.keterangan_sekretaris || '';
            document.getElementById('status_dirut_masuk').value = disposisi.status_dirut || 'pending';
            document.getElementById('keterangan_dirut_masuk').value = disposisi.keterangan_dirut || '';
            
            // Set waktu review
            if (disposisi.waktu_review_sekretaris) {
                const waktuSekretaris = disposisi.waktu_review_sekretaris.replace(' ', 'T').substring(0, 16);
                document.getElementById('waktu_review_sekretaris_masuk').value = waktuSekretaris;
            }
            if (disposisi.waktu_review_dirut) {
                const waktuDirut = disposisi.waktu_review_dirut.replace(' ', 'T').substring(0, 16);
                document.getElementById('waktu_review_dirut_masuk').value = waktuDirut;
            }
            
            // Show/hide sections based on role
            const userRole = window.userRole;
            const sekretarisSection = document.getElementById('sekretaris-section-masuk');
            const direkturSection = document.getElementById('direktur-section-masuk');
            const tujuanSection = document.getElementById('tujuan-disposisi-section-masuk');
            
            if (userRole === 1 || userRole === 5) { // Sekretaris & Sekretaris ASP
                sekretarisSection.style.display = 'block';
                direkturSection.style.display = 'none';
                tujuanSection.style.display = 'none';
            } else if (userRole === 2 || userRole === 8) { // Direktur & Direktur ASP
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
            
            // Render Search Input & List
            let html = `
                <div class="mb-2">
                    <input type="text" id="search-tujuan-disposisi" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-green-500 focus:border-green-500" 
                           placeholder="Cari nama...">
                </div>
                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3" id="list-tujuan-disposisi">
            `;

            users.forEach(user => {
                const isChecked = selectedIds.includes(user.id);
                html += `
                    <label class="flex items-start space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded user-item-checkbox transition-colors duration-150 border-b border-gray-50 last:border-0">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" 
                                   name="tujuan_disposisi[]" 
                                   value="${user.id}" 
                                   ${isChecked ? 'checked' : ''}
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 user-name truncate">${user.name}</p>
                            <div class="flex items-center mt-0.5 text-xs text-gray-500">
                                <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-md border border-gray-200">${user.role_name || 'User'}</span>
                                <span class="mx-1.5 text-gray-300">|</span>
                                <span class="truncate text-gray-500" title="${user.unit_name}">${user.unit_name || '-'}</span>
                            </div>
                        </div>
                    </label>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;

            // Add search functionality
            const searchInput = document.getElementById('search-tujuan-disposisi');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const items = container.querySelectorAll('.user-item-checkbox');
                    
                    items.forEach(item => {
                        const name = item.querySelector('.user-name').textContent.toLowerCase();
                        if (name.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
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
            
            if (window.userRole === 1 || window.userRole === 5) { // Sekretaris & Sekretaris ASP
                formData.append('status_sekretaris', document.getElementById('status_sekretaris_masuk').value);
                formData.append('keterangan_sekretaris', document.getElementById('keterangan_sekretaris_masuk').value);
                const waktuSekretaris = document.getElementById('waktu_review_sekretaris_masuk').value;
                if (waktuSekretaris) formData.append('waktu_review_sekretaris', waktuSekretaris);
            } else if (window.userRole === 2 || window.userRole === 8) { // Direktur & Direktur ASP
                formData.append('status_dirut', document.getElementById('status_dirut_masuk').value);
                formData.append('keterangan_dirut', document.getElementById('keterangan_dirut_masuk').value);
                const waktuDirut = document.getElementById('waktu_review_dirut_masuk').value;
                if (waktuDirut) formData.append('waktu_review_dirut', waktuDirut);
                
                // Tujuan disposisi - scope to the modal form
                const form = document.getElementById('edit-disposisi-form-masuk');
                form.querySelectorAll('input[name="tujuan_disposisi[]"]:checked').forEach(cb => {
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
                    console.log('Disposisi saved successfully:', data);
                    
                    // Close modal first
                    window.dispatchEvent(new CustomEvent('close-modal-edit-disposisi-masuk'));
                    
                    // Show success message and reload
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Disposisi berhasil disimpan',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        
                        window.location.reload();
                    }
                } else {
                    console.error('Save failed:', data);
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
