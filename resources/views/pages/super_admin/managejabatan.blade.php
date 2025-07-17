@extends('home')

@section('title', 'Manajemen Jabatan - SISM Azra')

@section('content')
    <div x-data="jabatanManagement" class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Manajemen Jabatan</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola semua jabatan dalam sistem</p>
                </div>
                <button @click="openCreateModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                    <i class="ri-add-line"></i>
                    Tambah Jabatan
                </button>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Cari Jabatan</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery"
                            class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200"
                            placeholder="Cari berdasarkan nama jabatan...">
                        <i class="ri-search-line absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="w-48">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Status</label>
                    <select x-model="statusFilter"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Jabatan Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="jabatan in paginatedJabatan" :key="jabatan.id">
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900" x-text="jabatan.nama_jabatan"></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono text-gray-600" x-text="jabatan.kode_jabatan"></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': jabatan.status === 'aktif',
                                            'bg-red-100 text-red-800': jabatan.status === 'nonaktif'
                                        }"
                                        x-text="jabatan.status">
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900" x-text="formatDate(jabatan.created_at)"></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button @click="editJabatan(jabatan)"
                                            class="text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button @click="toggleStatus(jabatan)"
                                            class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                            <i class="ri-toggle-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div class="text-sm text-gray-700">
                    Menampilkan <span x-text="pageStart"></span> - <span x-text="pageEnd"></span> dari <span x-text="filteredJabatan.length"></span> data
                </div>
                <div class="flex gap-1">
                    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1 border rounded-l bg-white text-gray-700 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">&laquo;</button>
                    <template x-for="page in totalPages" :key="page">
                        <button @click="changePage(page)" :class="{'bg-green-100 text-green-700 font-bold': currentPage === page, 'bg-white text-gray-700': currentPage !== page}" class="px-3 py-1 border-t border-b" x-text="page"></button>
                    </template>
                    <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded-r bg-white text-gray-700 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}">&raquo;</button>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="filteredJabatan.length === 0" class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="ri-file-list-3-line text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada jabatan yang ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
        </div>

        <!-- Modal Form -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                    @click="showModal = false"></div>

                <div class="relative bg-white rounded-lg max-w-lg w-full mx-auto shadow-xl"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="px-6 pt-5 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900"
                                x-text="formMode === 'create' ? 'Tambah Jabatan Baru' : 'Edit Jabatan'"></h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <!-- Nama Jabatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jabatan</label>
                                <input type="text" x-model="formData.nama_jabatan" required
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Masukkan nama jabatan">
                            </div>

                            <!-- Kode Jabatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Jabatan</label>
                                <input type="text" x-model="formData.kode_jabatan" required
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="e.g., DIRUT, MNGR">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select x-model="formData.status" required
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center gap-2">
                                    <i class="ri-save-line"></i>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('jabatanManagement', () => ({
                jabatan: [],
                searchQuery: '',
                statusFilter: '',
                showModal: false,
                formMode: 'create',
                formData: {
                    id: null,
                    nama_jabatan: '',
                    kode_jabatan: '',
                    status: 'aktif'
                },
                currentPage: 1,
                itemsPerPage: 10,
                get totalPages() {
                    return Math.ceil(this.filteredJabatan.length / this.itemsPerPage) || 1;
                },
                get paginatedJabatan() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredJabatan.slice(start, start + this.itemsPerPage);
                },
                get pageStart() {
                    if (this.filteredJabatan.length === 0) return 0;
                    return (this.currentPage - 1) * this.itemsPerPage + 1;
                },
                get pageEnd() {
                    return Math.min(this.currentPage * this.itemsPerPage, this.filteredJabatan.length);
                },
                changePage(page) {
                    if (page < 1 || page > this.totalPages) return;
                    this.currentPage = page;
                },

                init() {
                    this.fetchJabatan();
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('statusFilter', () => { this.currentPage = 1; });
                },

                async fetchJabatan() {
                    try {
                        const response = await fetch('/jabatan/data');
                        const data = await response.json();
                        if (data.status === 'success') {
                            this.jabatan = data.data;
                        }
                    } catch (error) {
                        console.error('Error fetching jabatan:', error);
                        this.showError('Gagal mengambil data jabatan');
                    }
                },

                get filteredJabatan() {
                    return this.jabatan.filter(jabatan => {
                        const matchSearch = jabatan.nama_jabatan.toLowerCase().includes(this
                            .searchQuery
                            .toLowerCase()) || (jabatan.kode_jabatan && jabatan.kode_jabatan.toLowerCase().includes(this.searchQuery.toLowerCase()));
                        const matchStatus = this.statusFilter === '' || jabatan.status ===
                            this
                            .statusFilter;
                        return matchSearch && matchStatus;
                    });
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                openCreateModal() {
                    this.formMode = 'create';
                    this.resetForm();
                    this.showModal = true;
                },

                editJabatan(jabatan) {
                    this.formMode = 'edit';
                    this.formData = {
                        id: jabatan.id,
                        nama_jabatan: jabatan.nama_jabatan,
                        kode_jabatan: jabatan.kode_jabatan,
                        status: jabatan.status
                    };
                    this.showModal = true;
                },

                async submitForm() {
                    try {
                        const url = this.formMode === 'create' ? '/jabatan' :
                            `/jabatan/${this.formData.id}`;
                        const method = this.formMode === 'create' ? 'POST' : 'PUT';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            this.showSuccess(data.message);
                            this.showModal = false;
                            await this.fetchJabatan();
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        this.showError(error.message);
                    }
                },

                async toggleStatus(jabatan) {
                    if (!confirm(
                            `Apakah Anda yakin ingin mengubah status jabatan "${jabatan.nama_jabatan}"?`
                        )) return;

                    try {
                        const response = await fetch(`/jabatan/${jabatan.id}/toggle-status`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            this.showSuccess(data.message);
                            await this.fetchJabatan();
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        this.showError(error.message);
                    }
                },

                resetForm() {
                    this.formData = {
                        id: null,
                        nama_jabatan: '',
                        kode_jabatan: '',
                        status: 'aktif'
                    };
                },

                showSuccess(message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: message,
                        confirmButtonColor: '#10B981'
                    });
                },

                showError(message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                        confirmButtonColor: '#10B981'
                    });
                }
            }));
        });
    </script>
@endpush
