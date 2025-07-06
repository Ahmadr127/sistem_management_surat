@extends('home')

@section('title', 'Surat Unit Manager')

@section('content')
<div x-data="suratUnitManager" class="bg-white rounded-xl shadow-md">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-200 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Surat Unit Anda</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola surat yang Anda kirim untuk persetujuan manajer</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('surat-unit-manager.create') }}" 
               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                <i class="ri-add-line mr-1"></i> Buat Surat Baru
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="p-5 border-b border-gray-200 bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" x-model="searchQuery" placeholder="Cari nomor surat atau perihal..." 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Persetujuan</label>
                <select x-model="statusFilter" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Persetujuan</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="p-4">
        <div class="overflow-x-auto border border-gray-100 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-600">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Detail Surat</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Disetujui</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Status Persetujuan</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="surat in paginatedSurat" :key="surat.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2">
                                <div class="text-sm font-semibold text-gray-900" x-text="surat.nomor_surat"></div>
                                <div class="text-xs text-gray-600 truncate max-w-xs" x-text="surat.perihal"></div>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-700" x-text="formatDateTime(surat.created_at)"></td>
                            <td class="px-2 py-2 text-sm text-gray-700" x-text="surat.waktu_review_manager ? formatDateTime(surat.waktu_review_manager) : '-' "></td>
                            <td class="px-2 py-2">
                                <span class="inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': surat.status_manager === 'approved',
                                        'bg-red-100 text-red-800': surat.status_manager === 'rejected',
                                        'bg-yellow-100 text-yellow-800': surat.status_manager === 'pending'
                                    }"
                                    x-text="surat.status_manager">
                                </span>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex items-center space-x-2">
                                    <button @click="openDetailModal(surat)"
                                            class="text-gray-600 hover:text-gray-900 px-2 py-1 rounded-md bg-gray-50 hover:bg-gray-100 transition-colors flex items-center" title="Lihat Detail">
                                        <i class="ri-eye-line mr-1"></i> Detail
                                    </button>
                                    <a :href="`/surat-unit-manager/${surat.id}/edit`"
                                        x-show="surat.status_manager === 'pending' || surat.status_manager === 'rejected'"
                                       class="text-yellow-600 hover:text-yellow-800 px-2 py-1 rounded-md bg-yellow-50 hover:bg-yellow-100 transition-colors flex items-center" title="Edit Surat">
                                        <i class="ri-edit-line mr-1"></i> Edit
                                    </a>
                                    <button @click="confirmDelete(surat.id)"
                                            class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 transition-colors flex items-center" title="Hapus Surat">
                                        <i class="ri-delete-bin-line mr-1"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="paginatedSurat.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada surat yang cocok dengan filter atau Anda belum membuat surat.
                            </td>
                        </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-center gap-1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-3 py-1 border rounded-l bg-white text-gray-700 hover:bg-gray-50"
                    :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">&laquo;</button>
                <template x-for="page in totalPages" :key="page">
                    <button @click="changePage(page)"
                        :class="{'bg-green-100 text-green-700 font-bold': currentPage === page, 'bg-white text-gray-700': currentPage !== page}"
                        class="px-3 py-1 border-t border-b" x-text="page"></button>
                </template>
                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                    class="px-3 py-1 border rounded-r bg-white text-gray-700 hover:bg-gray-50"
                    :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}">&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showModal" class="fixed inset-0 transition-opacity" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm"></div>
            </div>
            <div x-show="showModal" class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-3xl sm:w-full">
                <template x-if="selectedSurat">
                    <div>
                        <!-- Modal Header -->
                        <div class="px-6 py-4 bg-white border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Surat</h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><i class="ri-close-line text-xl"></i></button>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                            <!-- Basic Info -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-base font-semibold text-gray-800 mb-3">
                                    <i class="ri-information-line mr-1 text-gray-600"></i>
                                    Informasi Umum
                                </h4>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Nomor Surat</dt><dd class="mt-1 text-sm font-semibold text-gray-900" x-text="selectedSurat.nomor_surat"></dd></div>
                                    <div><dt class="text-sm font-medium text-gray-500">Tanggal Surat</dt><dd class="mt-1 text-sm text-gray-900" x-text="formatDate(selectedSurat.tanggal_surat)"></dd></div>
                                    <div><dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt><dd class="mt-1 text-sm text-gray-900" x-text="formatDateTime(selectedSurat.created_at)"></dd></div>
                                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Perihal</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.perihal"></dd></div>
                                    <div class="sm:col-span-2 whitespace-pre-wrap"><dt class="text-sm font-medium text-gray-500">Isi Surat</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.isi_surat"></dd></div>
                                </dl>
                            </div>

                            <!-- Approval Info -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-base font-semibold text-gray-800 mb-3">
                                    <i class="ri-shield-check-line mr-1 text-gray-600"></i>
                                    Informasi Persetujuan Manajer
                                </h4>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd>
                                            <span class="inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full"
                                                  :class="{
                                                      'bg-green-100 text-green-800': selectedSurat.status_manager === 'approved',
                                                      'bg-red-100 text-red-800': selectedSurat.status_manager === 'rejected',
                                                      'bg-yellow-100 text-yellow-800': selectedSurat.status_manager === 'pending'
                                                  }"
                                                  x-text="selectedSurat.status_manager">
                                            </span>
                                        </dd>
                                    </div>
                                    <div><dt class="text-sm font-medium text-gray-500">Tanggal Review Manager</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.waktu_review_manager ? formatDateTime(selectedSurat.waktu_review_manager) : '-' "></dd></div>
                                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Alasan/Keterangan</dt><dd class="mt-1 text-sm text-gray-900 bg-white p-2 rounded border" x-text="selectedSurat.keterangan_manager || '-' "></dd></div>
                                </dl>
                            </div>
                            
                            <!-- Attachment Info -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="text-base font-semibold text-gray-800 mb-3">
                                    <i class="ri-attachment-2 mr-1 text-gray-600"></i>
                                    Lampiran
                                </h4>
                                <div x-show="selectedSurat.files && selectedSurat.files.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="file in selectedSurat.files" :key="file.id">
                                            <div class="flex items-center justify-between p-2 bg-white rounded border">
                                                <div class="flex items-center space-x-2">
                                                    <i :class="getFileIcon(file.original_name)" class="text-lg"></i>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900" x-text="file.original_name"></p>
                                                        <p class="text-xs text-gray-500" x-text="formatFileSize(file.file_size)"></p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <a :href="`/surat-unit-manager/${selectedSurat.id}/preview-file/${file.id}`" 
                                                       class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50" title="Preview" target="_blank">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a :href="`/surat-unit-manager/${selectedSurat.id}/download-file/${file.id}`" 
                                                       class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" title="Download">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div x-show="selectedSurat.files.length > 1" class="mt-3 pt-2 border-t border-gray-200">
                                        <a :href="`/surat-unit-manager/${selectedSurat.id}/download`" class="inline-flex items-center text-blue-600 hover:underline">
                                            <i class="ri-download-2-line mr-1"></i> Download Semua File (ZIP)
                                        </a>
                                    </div>
                                </div>
                                <div x-show="!selectedSurat.files || selectedSurat.files.length === 0" class="text-sm text-gray-500">
                                    Tidak ada lampiran.
                                </div>
                            </div>
                        </div>
                         <!-- Modal Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                            <button @click="showModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg">Tutup</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('suratUnitManager', () => ({
        allSurat: @json($suratUnitManager),
        searchQuery: '',
        statusFilter: '',
        showModal: false,
        selectedSurat: null,
        currentPage: 1,
        itemsPerPage: 10,
        get filteredSurat() {
            let data = this.allSurat;
            console.log('statusFilter:', this.statusFilter, typeof this.statusFilter);
            if (this.searchQuery) {
                data = data.filter(surat =>
                    surat.nomor_surat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    surat.perihal.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            if (this.statusFilter !== '') {
                data = data.filter(surat => surat.status_manager === this.statusFilter);
            }
            return data;
        },
        get totalPages() {
            const total = Math.ceil(this.filteredSurat.length / this.itemsPerPage) || 1;
            console.log('totalPages:', total, 'filteredSurat:', this.filteredSurat.length);
            return total;
        },
        get paginatedSurat() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredSurat.slice(start, end);
        },
        changePage(page) {
            console.log('changePage called:', page, 'totalPages:', this.totalPages);
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },
        openDetailModal(surat) {
            this.selectedSurat = surat;
            this.showModal = true;
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            if (dateString.includes('T')) {
                options.hour = '2-digit';
                options.minute = '2-digit';
            }
            return new Date(dateString).toLocaleDateString('id-ID', options);
        },
        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
        getFileIcon(fileName) {
            const extension = fileName.split('.').pop().toLowerCase();
            switch (extension) {
                case 'pdf':
                    return 'ri-file-pdf-line text-red-500';
                case 'doc':
                case 'docx':
                    return 'ri-file-word-line text-blue-500';
                case 'xls':
                case 'xlsx':
                    return 'ri-file-excel-line text-green-500';
                case 'ppt':
                case 'pptx':
                    return 'ri-file-ppt-line text-orange-500';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'ri-image-line text-purple-500';
                case 'zip':
                case 'rar':
                    return 'ri-file-zip-line text-yellow-500';
                default:
                    return 'ri-file-line text-gray-500';
            }
        },
        formatFileSize(bytes) {
            if (!bytes) return 'Unknown';
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unit = 0;
            while (size >= 1024 && unit < units.length - 1) {
                size /= 1024;
                unit++;
            }
            return Math.round(size * 100) / 100 + ' ' + units[unit];
        },
        confirmDelete(id) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Surat ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteSurat(id);
                }
            });
        },
        async deleteSurat(id) {
            try {
                const response = await fetch(`/surat-unit-manager/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus surat.');
                }
            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            }
        }
    }));
});
</script>
@endpush

@push('styles')

@endpush 