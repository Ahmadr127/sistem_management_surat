@extends('home')

@section('title', 'Persetujuan Surat Unit - Manager')

@section('content')
<div x-data="suratUnitManager" class="bg-white rounded-xl shadow-md">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-200 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Persetujuan Surat Unit</h2>
            <p class="text-xs text-gray-500 mt-1">Daftar surat dari unit yang memerlukan persetujuan Anda</p>
        </div>
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="p-5 border-b border-gray-200 bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" x-model="searchQuery" placeholder="Cari nomor surat, perihal, atau unit..." 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
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
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Dari Unit</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Review Manager</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
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
                        <td class="px-2 py-2">
                                <div class="text-sm font-medium text-gray-900" x-text="surat.unit.name"></div>
                                <div class="text-xs text-gray-600" x-text="surat.unit.jabatan ? surat.unit.jabatan.nama_jabatan : ''"></div>
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
                            <button @click="openApprovalModal(surat)" 
                                    class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center">
                                <i class="ri-search-eye-line mr-1"></i> Review
                            </button>
                        </td>
                    </tr>
                    </template>
                    <tr x-show="paginatedSurat.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data surat yang cocok dengan filter.
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

    <!-- Approval Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showModal" class="fixed inset-0 transition-opacity" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm"></div>
            </div>
            <div x-show="showModal" class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-4xl sm:w-full">
                <template x-if="selectedSurat">
                    <div>
                        <!-- Modal Header -->
                        <div class="px-6 py-4 bg-white border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold text-gray-900">Detail & Persetujuan Surat</h3>
                                <!-- Badge Status -->
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': selectedSurat.status_manager === 'approved',
                                        'bg-red-100 text-red-800': selectedSurat.status_manager === 'rejected',
                                        'bg-yellow-100 text-yellow-800': selectedSurat.status_manager === 'pending'
                                    }"
                                    x-text="selectedSurat.status_manager === 'approved' ? 'Disetujui' : (selectedSurat.status_manager === 'rejected' ? 'Ditolak' : 'Menunggu Persetujuan')">
                                </span>
                            </div>
                            <button @click="closeModal" 
                                    class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-all duration-200">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-6 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8">
                                <!-- Left Column: Surat Details -->
                                <div class="space-y-6">
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                        <h4 class="text-base font-semibold text-gray-800 mb-3"><i class="ri-information-line mr-1 text-gray-600"></i>Informasi Surat</h4>
                                        <dl class="grid grid-cols-1 gap-y-3">
                                            <div><dt class="text-sm font-medium text-gray-500">Nomor Surat</dt><dd class="mt-1 text-sm font-semibold text-gray-900" x-text="selectedSurat.nomor_surat"></dd></div>
                                            <div><dt class="text-sm font-medium text-gray-500">Tanggal Surat</dt><dd class="mt-1 text-sm text-gray-900" x-text="formatDate(selectedSurat.tanggal_surat)"></dd></div>
                                            <div><dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt><dd class="mt-1 text-sm text-gray-900" x-text="formatDateTime(selectedSurat.created_at)"></dd></div>
                                            <div><dt class="text-sm font-medium text-gray-500">Perihal</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.perihal"></dd></div>
                                            <div class="whitespace-pre-wrap"><dt class="text-sm font-medium text-gray-500">Isi Surat</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.isi_surat"></dd></div>
                                            <div x-show="selectedSurat.files && selectedSurat.files.length > 0">
                                                <dt class="text-sm font-medium text-gray-500">Lampiran</dt>
                                                <dd class="mt-1">
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
                                                </dd>
                                            </div>
                                            <div x-show="!selectedSurat.files || selectedSurat.files.length === 0" class="text-sm text-gray-500">
                                                <dt class="text-sm font-medium text-gray-500">Lampiran</dt>
                                                <dd class="mt-1">Tidak ada lampiran.</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                        <h4 class="text-base font-semibold text-gray-800 mb-3"><i class="ri-user-line mr-1 text-gray-600"></i>Detail Pengirim (Unit)</h4>
                                        <dl class="grid grid-cols-1 gap-y-3">
                                            <div><dt class="text-sm font-medium text-gray-500">Nama</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.unit.name"></dd></div>
                                            <div><dt class="text-sm font-medium text-gray-500">Jabatan</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.unit.jabatan ? selectedSurat.unit.jabatan.nama_jabatan : ''"></dd></div>
                                            <div x-show="selectedSurat.keterangan_unit"><dt class="text-sm font-medium text-gray-500">Keterangan dari Unit</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.keterangan_unit"></dd></div>
                                        </dl>
                                    </div>
                                </div>
                                <!-- Right Column: Approval/Status Info -->
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 space-y-6">
                                    <h4 class="text-base font-semibold text-gray-800 mb-3"><i class="ri-check-double-line mr-1 text-gray-600"></i>Status & Persetujuan</h4>
                                    <dl class="grid grid-cols-1 gap-y-3">
                                        <div><dt class="text-sm font-medium text-gray-500">Status Manager</dt>
                                            <dd class="mt-1 text-sm font-semibold" :class="{
                                                'text-green-700': selectedSurat.status_manager === 'approved',
                                                'text-red-700': selectedSurat.status_manager === 'rejected',
                                                'text-yellow-700': selectedSurat.status_manager === 'pending'
                                            }" x-text="selectedSurat.status_manager === 'approved' ? 'Disetujui' : (selectedSurat.status_manager === 'rejected' ? 'Ditolak' : 'Menunggu Persetujuan')"></dd>
                                        </div>
                                        <div><dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt><dd class="mt-1 text-sm text-gray-900" x-text="formatDateTime(selectedSurat.created_at)"></dd></div>
                                        <div><dt class="text-sm font-medium text-gray-500">Tanggal Review Manager</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.waktu_review_manager ? formatDateTime(selectedSurat.waktu_review_manager) : '-' "></dd></div>
                                        <div x-show="selectedSurat.keterangan_manager"><dt class="text-sm font-medium text-gray-500">Keterangan Manager</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.keterangan_manager"></dd></div>
                                    </dl>
                                    <!-- Approval Form hanya tampil jika status_manager masih pending -->
                                    <form x-show="selectedSurat.status_manager === 'pending'" @submit.prevent="submitApproval">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan Anda <span class="text-red-500">*</span></label>
                                                <div class="flex space-x-4 p-2 bg-white rounded-lg">
                                                    <label class="flex-1 text-center py-2 px-4 rounded-md cursor-pointer transition-all duration-200" 
                                                           :class="{
                                                               'bg-green-600 text-white shadow': approvalForm.action === 'approve',
                                                               'opacity-50 cursor-not-allowed': submitting
                                                           }">
                                                        <input type="radio" x-model="approvalForm.action" value="approve" required class="sr-only" @change="showValidation = false" :disabled="submitting">
                                                        <span><i class="ri-checkbox-circle-line mr-1"></i>Setujui</span>
                                                    </label>
                                                    <label class="flex-1 text-center py-2 px-4 rounded-md cursor-pointer transition-all duration-200" 
                                                           :class="{
                                                               'bg-red-600 text-white shadow': approvalForm.action === 'reject',
                                                               'opacity-50 cursor-not-allowed': submitting
                                                           }">
                                                        <input type="radio" x-model="approvalForm.action" value="reject" required class="sr-only" @change="showValidation = false" :disabled="submitting">
                                                        <span><i class="ri-close-circle-line mr-1"></i>Tolak</span>
                                                    </label>
                                                </div>
                                                <div x-show="approvalForm.action === '' && showValidation" class="text-red-500 text-sm mt-1">
                                                    Silakan pilih keputusan Anda
                                                </div>
                                            </div>
                                            <div>
                                                <label for="keterangan_manager" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Keterangan / Alasan 
                                                    <span x-show="approvalForm.action === 'reject'" class="text-red-500">*</span>
                                                </label>
                                                <textarea id="keterangan_manager" x-model="approvalForm.keterangan_manager" rows="4" 
                                                          class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all" 
                                                          :class="{'opacity-50 cursor-not-allowed': submitting}"
                                                          :placeholder="approvalForm.action === 'reject' ? 'Wajib memberikan alasan penolakan...' : 'Berikan catatan jika diperlukan...'"
                                                          @input="validateKeterangan()"
                                                          :disabled="submitting"></textarea>
                                                <div x-show="approvalForm.action === 'reject' && !approvalForm.keterangan_manager.trim() && showValidation" class="text-red-500 text-sm mt-1">
                                                    Keterangan wajib diisi jika Anda menolak surat
                                                </div>
                                                <div x-show="approvalForm.action === 'reject' && approvalForm.keterangan_manager.trim().length > 0 && approvalForm.keterangan_manager.trim().length < 10" class="text-orange-500 text-sm mt-1">
                                                    Alasan penolakan minimal 10 karakter (tersisa: <span x-text="10 - approvalForm.keterangan_manager.trim().length"></span>)
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <span x-show="approvalForm.action === 'reject'" class="text-red-500">Keterangan ini wajib diisi jika Anda menolak surat.</span>
                                                    <span x-show="approvalForm.action === 'approve'">Keterangan bersifat opsional untuk persetujuan.</span>
                                                </p>
                                            </div>
                                            <div class="pt-4 flex justify-end space-x-3">
                                                <button type="button" @click="closeModal" 
                                                        class="px-4 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 font-semibold transition-all"
                                                        :disabled="submitting">
                                                    Batal
                                                </button>
                                                <button type="submit" 
                                                        class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-semibold transition-all" 
                                                        :class="{'opacity-50 cursor-not-allowed': submitting}"
                                                        :disabled="submitting">
                                                    <span x-show="!submitting">
                                                        <i class="ri-send-plane-line mr-1"></i>Kirim Keputusan
                                                    </span>
                                                    <span x-show="submitting">
                                                        <i class="ri-loader-4-line animate-spin mr-1"></i>Memproses...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
            if (this.searchQuery) {
                data = data.filter(surat =>
                    surat.nomor_surat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    surat.perihal.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (surat.unit && surat.unit.name && surat.unit.name.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            }
            if (this.statusFilter) {
                data = data.filter(surat => surat.status_manager === this.statusFilter);
            }
            return data;
        },
        get totalPages() {
            return Math.ceil(this.filteredSurat.length / this.itemsPerPage) || 1;
        },
        get paginatedSurat() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredSurat.slice(start, start + this.itemsPerPage);
        },
        changePage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
        },
        openApprovalModal(surat) {
            this.selectedSurat = surat;
            this.showModal = true;
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: 'numeric', minute: 'numeric' });
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
        submitting: false,
        approvalForm: {
            action: '',
            keterangan_manager: ''
        },
        showValidation: false,
        async submitApproval() {
            // Prevent multiple submission
            if (this.submitting) {
                return;
            }
            
            // Reset validation state
            this.showValidation = false;
            
            // Validate action selection
            if (!this.approvalForm.action) {
                this.showValidation = true;
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Keputusan',
                    text: 'Silakan pilih keputusan (Setujui/Tolak) sebelum mengirim.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validate keterangan for rejection
            if (this.approvalForm.action === 'reject' && !this.approvalForm.keterangan_manager.trim()) {
                this.showValidation = true;
                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan Wajib',
                    text: 'Anda harus memberikan alasan penolakan di kolom keterangan.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validate keterangan length for rejection
            if (this.approvalForm.action === 'reject' && this.approvalForm.keterangan_manager.trim().length < 10) {
                this.showValidation = true;
                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan Terlalu Pendek',
                    text: 'Alasan penolakan harus minimal 10 karakter.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Confirm action
            const actionText = this.approvalForm.action === 'approve' ? 'menyetujui' : 'menolak';
            const result = await Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Keputusan',
                text: `Apakah Anda yakin ingin ${actionText} surat ini?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: this.approvalForm.action === 'approve' ? '#10B981' : '#EF4444',
                cancelButtonColor: '#6B7280'
            });

            if (!result.isConfirmed) {
                return;
            }

            this.submitting = true;
            
            // Show loading notification
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengirim keputusan Anda',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const csrfToken = '{{ csrf_token() }}';
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan. Silakan refresh halaman.');
                }
                
                const response = await fetch(`/surat-unit-manager/manager/${this.selectedSurat.id}/approval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.approvalForm)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (!data || typeof data !== 'object') {
                    throw new Error('Response tidak valid dari server.');
                }
                
                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true
                    });
                    
                    // Close modal and reload page
                    this.resetModal();
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat memproses permintaan.');
                }
            } catch (error) {
                console.error('Error:', error);
                
                let errorMessage = 'Terjadi kesalahan saat mengirim keputusan. Silakan coba lagi.';
                
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                } else if (error.message.includes('CSRF')) {
                    errorMessage = 'Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.';
                } else if (error.message.includes('HTTP error')) {
                    errorMessage = 'Server mengalami masalah. Silakan coba lagi dalam beberapa saat.';
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#EF4444',
                    confirmButtonText: 'OK'
                });
            } finally {
                this.submitting = false;
                // Close loading notification if still open
                if (Swal.isVisible()) {
                    Swal.close();
                }
            }
        },
        closeModal() {
            // Check if form has data
            if (this.approvalForm.action || this.approvalForm.keterangan_manager.trim()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Tutup Modal?',
                    text: 'Data yang sudah diisi akan hilang. Apakah Anda yakin ingin menutup modal?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tutup',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#6B7280',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.resetModal();
                    }
                });
            } else {
                this.resetModal();
            }
        },
        resetModal() {
            this.showModal = false;
            this.showValidation = false;
            this.approvalForm.action = '';
            this.approvalForm.keterangan_manager = '';
            this.selectedSurat = null;
        },
        validateKeterangan() {
            if (this.approvalForm.action === 'reject' && this.approvalForm.keterangan_manager.trim().length < 10) {
                this.showValidation = true;
                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan Terlalu Pendek',
                    text: 'Alasan penolakan harus minimal 10 karakter.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        }
    }));
});
</script>
@endpush 