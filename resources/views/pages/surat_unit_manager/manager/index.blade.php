@extends('home')

@section('title', 'Persetujuan Surat Unit - Manager')

@section('content')
<div x-data="approvalManager" x-init="init()" class="bg-white rounded-xl shadow-md">
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
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="surat in filteredSurat" :key="surat.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2">
                            <div class="text-sm font-semibold text-gray-900" x-text="surat.nomor_surat"></div>
                            <div class="text-xs text-gray-600 truncate max-w-xs" x-text="surat.perihal"></div>
                        </td>
                        <td class="px-2 py-2">
                                <div class="text-sm font-medium text-gray-900" x-text="surat.unit.name"></div>
                                <div class="text-xs text-gray-600" x-text="surat.unit.jabatan ? surat.unit.jabatan.nama_jabatan : ''"></div>
                        </td>
                        <td class="px-2 py-2 text-sm text-gray-700" x-text="formatDate(surat.created_at)"></td>
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
                    <tr x-show="!filteredSurat.length">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data surat yang cocok dengan filter.
                        </td>
                    </tr>
                </tbody>
            </table>
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
                            <h3 class="text-lg font-semibold text-gray-900">Detail & Persetujuan Surat</h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><i class="ri-close-line text-xl"></i></button>
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
                                            <div><dt class="text-sm font-medium text-gray-500">Perihal</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.perihal"></dd></div>
                                            <div class="whitespace-pre-wrap"><dt class="text-sm font-medium text-gray-500">Isi Surat</dt><dd class="mt-1 text-sm text-gray-900" x-text="selectedSurat.isi_surat"></dd></div>
                                            <div x-show="selectedSurat.file_path">
                                                <dt class="text-sm font-medium text-gray-500">Lampiran</dt>
                                                <dd class="mt-1">
                                                    <a :href="`/surat-unit-manager/${selectedSurat.id}/download`" class="inline-flex items-center text-blue-600 hover:underline">
                                                        <i class="ri-download-2-line mr-1"></i> Download Lampiran
                                                    </a>
                                                </dd>
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
                                <!-- Right Column: Approval Form -->
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                                    <h4 class="text-base font-semibold text-gray-800 mb-3"><i class="ri-check-double-line mr-1 text-gray-600"></i>Form Persetujuan</h4>
                                    <form @submit.prevent="submitApproval">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan Anda</label>
                                                <div class="flex space-x-4 p-2 bg-white rounded-lg">
                                                    <label class="flex-1 text-center py-2 px-4 rounded-md cursor-pointer transition-all duration-200" :class="{'bg-green-600 text-white shadow': approvalForm.action === 'approve'}">
                                                        <input type="radio" x-model="approvalForm.action" value="approve" required class="sr-only">
                                                        <span><i class="ri-checkbox-circle-line mr-1"></i>Setujui</span>
                                                    </label>
                                                    <label class="flex-1 text-center py-2 px-4 rounded-md cursor-pointer transition-all duration-200" :class="{'bg-red-600 text-white shadow': approvalForm.action === 'reject'}">
                                                        <input type="radio" x-model="approvalForm.action" value="reject" required class="sr-only">
                                                        <span><i class="ri-close-circle-line mr-1"></i>Tolak</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="keterangan_manager" class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan</label>
                                                <textarea id="keterangan_manager" x-model="approvalForm.keterangan_manager" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all" placeholder="Berikan catatan jika diperlukan..."></textarea>
                                                <p class="text-xs text-gray-500 mt-1">Keterangan ini wajib diisi jika Anda menolak surat.</p>
                                            </div>
                                            <div class="pt-4 flex justify-end">
                                                <button type="submit" class="w-full px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-semibold" :disabled="submitting">
                                                    <span x-show="!submitting">Kirim Keputusan</span>
                                                    <span x-show="submitting"><i class="ri-loader-4-line animate-spin"></i> Memproses...</span>
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
    Alpine.data('approvalManager', () => ({
        allSurat: [],
        searchQuery: '',
        statusFilter: 'pending',
        showModal: false,
        selectedSurat: null,
        submitting: false,
        approvalForm: {
            action: '',
            keterangan_manager: ''
        },
        init() {
            this.allSurat = @json($suratUnitManager);
        },
        get filteredSurat() {
            if (!this.allSurat) return [];
            return this.allSurat.filter(surat => {
                const searchMatch = this.searchQuery.toLowerCase() === '' ||
                    surat.nomor_surat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    surat.perihal.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (surat.unit && surat.unit.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                
                const statusMatch = this.statusFilter === '' || surat.status_manager === this.statusFilter;

                return searchMatch && statusMatch;
            });
        },
        openApprovalModal(surat) {
            this.selectedSurat = surat;
            this.approvalForm.action = '';
            this.approvalForm.keterangan_manager = '';
            this.showModal = true;
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        async submitApproval() {
            if (!this.approvalForm.action) {
                Swal.fire('Tunggu Dulu!', 'Anda harus memilih "Setujui" atau "Tolak" sebelum mengirim.', 'warning');
                return;
            }

            if (this.approvalForm.action === 'reject' && !this.approvalForm.keterangan_manager.trim()) {
                Swal.fire('Tunggu Dulu!', 'Anda harus memberikan alasan penolakan di kolom keterangan.', 'warning');
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch(`/surat-unit-manager/manager/${this.selectedSurat.id}/approval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.approvalForm)
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                    this.showModal = false;
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }
            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            } finally {
                this.submitting = false;
            }
        }
    }));
});
</script>
@endpush 