{{-- Modal Detail Surat Keluar --}}
<x-modal-dinamis id="detail-surat" title="Detail Surat Keluar" size="2xl" :showFooter="true">
    <x-slot:trigger>
        {{-- Trigger akan dipanggil dari JavaScript --}}
    </x-slot:trigger>

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 mb-4" x-data="{ activeTab: 'detail' }">
        <nav class="flex space-x-4">
            <button 
                @click="activeTab = 'detail'"
                :class="activeTab === 'detail' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                <i class="ri-file-info-line mr-1"></i>
                Detail Surat
            </button>
            <button 
                @click="activeTab = 'lampiran'"
                :class="activeTab === 'lampiran' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                <i class="ri-attachment-line mr-1"></i>
                Lampiran File
            </button>
        </nav>

        {{-- Detail Tab Content --}}
        <div x-show="activeTab === 'detail'" class="py-4">
            {{-- ... (konten detail tetap sama) ... --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Informasi Umum --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="ri-information-line mr-2 text-gray-600"></i>
                        Informasi Umum
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Surat</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900" id="detail-nomor">-</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Surat</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="detail-tanggal">-</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Surat</dt>
                                <dd class="mt-1">
                                    <span id="detail-jenis-badge" class="px-2 py-1 text-xs font-medium rounded-full">
                                        <span id="detail-jenis">-</span>
                                    </span>
                                </dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sifat Surat</dt>
                            <dd class="mt-1">
                                <span id="detail-sifat-badge" class="px-2 py-1 text-xs font-medium rounded-full">
                                    <span id="detail-sifat">-</span>
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Perihal</dt>
                            <dd class="mt-1 text-sm text-gray-900" id="detail-perihal">-</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Perusahaan</dt>
                            <dd class="mt-1 text-sm text-gray-900" id="detail-perusahaan">-</dd>
                        </div>
                    </div>
                </div>

                {{-- Informasi Disposisi --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="ri-user-line mr-2 text-gray-600"></i>
                        Informasi Disposisi
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                        <div class="flex justify-between items-center">
                            <dt class="text-sm font-medium text-gray-500">Status Sekretaris</dt>
                            <dd class="text-sm" id="detail-status-sekretaris">-</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-sm font-medium text-gray-500">Status Direktur</dt>
                            <dd class="text-sm" id="detail-status-dirut">-</dd>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="mt-6">
                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="ri-chat-quote-line mr-2 text-gray-600"></i>
                    Keterangan
                </h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Pengirim</dt>
                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-pengirim">-</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Sekretaris</dt>
                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-sekretaris">-</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Direktur</dt>
                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-dirut">-</dd>
                    </div>
                </div>
            </div>

            {{-- Tujuan Disposisi --}}
            <div class="mt-6">
                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="ri-share-forward-line mr-2 text-gray-600"></i>
                    Tujuan Disposisi
                </h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div id="detail-tujuan-disposisi" class="text-sm text-gray-700">
                        Loading...
                    </div>
                </div>
            </div>
        </div>

        {{-- Lampiran Tab Content --}}
        <div x-show="activeTab === 'lampiran'" class="py-4" style="display: none;">
            <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <i class="ri-attachment-line mr-2 text-gray-600"></i>
                Daftar Lampiran
            </h4>
            
            <div id="lampiran-files-list" class="space-y-3">
                {{-- File list akan diisi oleh JavaScript --}}
            </div>
            
            <div id="no-lampiran-text" class="text-center py-8 text-gray-500 hidden">
                <i class="ri-file-warning-line text-4xl mb-2 block text-gray-300"></i>
                Tidak ada lampiran file
            </div>
        </div>
    </div>

    <x-slot:footer>
        <x-button variant="secondary" @click="close()">
            Tutup
        </x-button>
    </x-slot:footer>
</x-modal-dinamis>

@push('scripts')
<script>
// Fungsi untuk membuka modal detail surat
window.showDetailSurat = function(suratId) {
    // Trigger modal dengan event yang benar
    window.dispatchEvent(new CustomEvent('open-modal-detail-surat'));
    
    // Load data surat
    loadDetailSurat(suratId);
};

// Fungsi untuk load detail surat
async function loadDetailSurat(suratId) {
    try {
        // Show loading state
        const loadingHTML = `
            <div class="flex flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
                <p class="text-gray-500">Memuat detail surat...</p>
            </div>
        `;
        
        // Set loading state in modal body
        const modalBody = document.querySelector('#detail-surat .px-6.py-4');
        if (modalBody) {
            const originalContent = modalBody.innerHTML;
            modalBody.innerHTML = loadingHTML;
        }
        
        const response = await fetch(`/api/surat-keluar/${suratId}`);
        const data = await response.json();
        
        if (response.ok && data.success) {
            populateDetailModal(data.data);
        } else {
            throw new Error(data.message || 'Gagal memuat detail surat');
        }
    } catch (error) {
        console.error('Error loading detail:', error);
        
        // Close modal
        window.dispatchEvent(new CustomEvent('close-modal-detail-surat'));
        
        // Show error message
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Gagal memuat detail surat'
            });
        } else {
            alert('Gagal memuat detail surat: ' + (error.message || 'Terjadi kesalahan'));
        }
    }
}

// Fungsi untuk populate modal dengan data
function populateDetailModal(surat) {
    // Nomor Surat
    document.getElementById('detail-nomor').textContent = surat.nomor_surat || '-';
    
    // Tanggal
    if (surat.tanggal_surat) {
        const date = new Date(surat.tanggal_surat);
        document.getElementById('detail-tanggal').textContent = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }
    
    // Jenis Surat
    const jenisBadge = document.getElementById('detail-jenis-badge');
    const jenisText = document.getElementById('detail-jenis');
    if (surat.jenis_surat === 'internal') {
        jenisBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
        jenisText.textContent = 'Internal';
    } else {
        jenisBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800';
        jenisText.textContent = 'Eksternal';
    }
    
    // Sifat Surat
    const sifatBadge = document.getElementById('detail-sifat-badge');
    const sifatText = document.getElementById('detail-sifat');
    if (surat.sifat_surat === 'urgent') {
        sifatBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800';
        sifatText.textContent = 'Urgent';
    } else {
        sifatBadge.className = 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800';
        sifatText.textContent = 'Normal';
    }
    
    // Perihal
    document.getElementById('detail-perihal').textContent = surat.perihal || '-';
    
    // Perusahaan
    const perusahaan = surat.perusahaanData?.nama_perusahaan || surat.perusahaan || '-';
    document.getElementById('detail-perusahaan').textContent = perusahaan;
    
    // Populate Lampiran Files
    const lampiranList = document.getElementById('lampiran-files-list');
    const noLampiranText = document.getElementById('no-lampiran-text');
    
    if (surat.files && surat.files.length > 0) {
        lampiranList.innerHTML = surat.files.map(file => {
            // Gunakan route viewFile untuk menghindari masalah permission storage
            let fileUrl = `/suratkeluar/${surat.id}/file/${file.id}/view`;
            
            return `
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-shadow">
                <div class="flex items-center overflow-hidden">
                    <div class="flex-shrink-0 h-10 w-10 rounded bg-green-100 flex items-center justify-center text-green-600 mr-3">
                        <i class="ri-file-text-line text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.original_name}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(file.file_size)}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <a href="${fileUrl}" target="_blank" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-full transition-colors" title="Buka di tab baru">
                        <i class="ri-external-link-line text-lg"></i>
                    </a>
                    <a href="/suratkeluar/${surat.id}/file/${file.id}/download" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors" title="Download">
                        <i class="ri-download-line text-lg"></i>
                    </a>
                </div>
            </div>
            `;
        }).join('');
        noLampiranText.classList.add('hidden');
    } else {
        lampiranList.innerHTML = '';
        noLampiranText.classList.remove('hidden');
    }
    
    // Status
    if (surat.disposisi) {
        document.getElementById('detail-status-sekretaris').innerHTML = getStatusBadge(surat.disposisi.status_sekretaris);
        document.getElementById('detail-status-dirut').innerHTML = getStatusBadge(surat.disposisi.status_dirut);
        
        // Keterangan
        document.getElementById('detail-keterangan-pengirim').textContent = surat.disposisi.keterangan_pengirim || '-';
        document.getElementById('detail-keterangan-sekretaris').textContent = surat.disposisi.keterangan_sekretaris || '-';
        document.getElementById('detail-keterangan-dirut').textContent = surat.disposisi.keterangan_dirut || '-';
        
        // Tujuan Disposisi
        if (surat.disposisi.tujuan && surat.disposisi.tujuan.length > 0) {
            const tujuanHTML = surat.disposisi.tujuan.map(user => `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 mr-2 mb-2">
                    <i class="ri-user-line mr-1"></i>
                    ${user.name}
                </span>
            `).join('');
            document.getElementById('detail-tujuan-disposisi').innerHTML = tujuanHTML;
        } else {
            document.getElementById('detail-tujuan-disposisi').textContent = 'Tidak ada tujuan disposisi';
        }
    }
}

// Helper function untuk format file size
function formatFileSize(bytes) {
    if (!bytes) return '-';
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Helper function untuk status badge
function getStatusBadge(status) {
    const statusConfig = {
        'pending': { class: 'bg-yellow-100 text-yellow-800', label: 'Menunggu' },
        'review': { class: 'bg-blue-100 text-blue-800', label: 'Sedang Ditinjau' },
        'approved': { class: 'bg-green-100 text-green-800', label: 'Disetujui' },
        'rejected': { class: 'bg-red-100 text-red-800', label: 'Ditolak' }
    };
    
    const config = statusConfig[status] || { class: 'bg-gray-100 text-gray-800', label: status || '-' };
    return `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${config.class}">${config.label}</span>`;
}
</script>
@endpush
